<?php

/************************************
* Process Image Edits and Deletions
************************************/

function pig_process_image_edit() {

	global $blog_id;
	if( ! empty( $_POST ) ) {
		if ( isset( $_POST['pig-image-action'] ) && $_POST['pig-image-action'] == 'edit' ) {

			if ( ! is_user_logged_in() )
				return;


			// edit an image
			$image_id    		= strip_tags( stripslashes( $_POST['pig-image-id'] ) );   // get the image ID on the main site
			$site_id    		= strip_tags( stripslashes( $_POST['pig-subsite-id'] ) );   // get the ID of the site the image belongs to
			$name     			= strip_tags( stripslashes( $_POST['pig-image-title'] ) );   // get the name of the image
			$desc    			= strip_tags( stripslashes( $_POST['pig-image-desc'] ) );   // get the image description
			$url    			= strip_tags( stripslashes( $_POST['pig-referrer'] ) );   // get the redirect URL
			$error    			= NULL;

			if ( get_current_user_id() !== get_post_field( 'post_author', $image_id ) )
				wp_die( 'You do not have permission to edit this image.', 'Error' );

			$image = get_post( $image_id );

			if ( empty( $image ) )
				return;

			if ( !$name || $name == '' ) {
				$error .= 'Please enter a name for the image.<br/>';
			}
			if ( !$desc || $desc == 'Describe your image' ) {
				$error .= 'Please enter a description.<br/>';
			}

			$mature = isset( $_POST['pig-image-mature'] ) ? 'on' : false;

			// everything ok
			if ( ! $error ) {

				// update the image on the main site
				$updated_image_id = wp_update_post( array(
						'ID'   => $image_id,
						'post_title' => $name,
						'post_content' => $desc
					)
				);

				update_post_meta( $image_id, 'pig_mature', $mature );

				switch_to_blog( $site_id );

				// update the image on the sub site
				$updated_sub_site_image_id = wp_update_post( array(
						'ID'   => $subsite_image_id,
						'post_title' => $name,
						'post_content' => $desc
					)
				);

				update_post_meta( $subsite_image_id, 'pig_mature', $mature );

				restore_current_blog();

				// the IMAGE post was created okay
				if ( $updated_image_id && $updated_sub_site_image_id ) {
					wp_redirect( $url . '?image-updated=1#gallery_tab' ); exit;
				} else {
					wp_redirect( $url . $url . '?image-updated=0#gallery_tab' ); exit;
				}
			} else {
				// if there's an error
				header( "Location: " . $url . '?image-updated=0&fields-empty=1#gallery_tab' );
			}
		} else if ( isset( $_POST['pig-image-action'] ) && $_POST['pig-image-action'] == 'delete' ) {
			// delete an image
			$image_id    		= strip_tags( stripslashes( $_POST['pig-image-id'] ) );   // get the image ID on the main site
			$site_id    		= strip_tags( stripslashes( $_POST['pig-subsite-id'] ) );   // get the ID of the site the image belongs to
			$subsite_image_id  	= strip_tags( $_POST['pig-subsite-image-id'] );     // get the ID of the image on the subsite
			$url    			= strip_tags( stripslashes( $_POST['pig-referrer'] ) );   // get the redirect URL
			$error    			= NULL;

			if ( ! $image_id ) {
				$error .= 'Something went wrong.<br/>';
			}

			// everything ok
			if ( ! $error ) {

				// remove the image from the main site

				switch_to_blog( $site_id );

				if ( get_current_user_id() !== get_post_field( 'post_author', $image_id ) )
					wp_die( 'You do not have permission to edit this image.', 'Error' );

				wp_delete_post( $image_id );

				restore_current_blog();

				header( "Location: " . $url . '?image-removed=1#gallery_tab' );

			} else {
				// if there's an error
				header( "Location: " . $url . '?image-removed=0#gallery_tab' );
			}
		}
	}
}
add_action( 'init', 'pig_process_image_edit' );
