<?php

/************************************
* Process Front end Submissions
************************************/

//This function reads the extension of the file. It is used to determine if the file  is an image by checking the extension.
function pig_get_extension( $str ) {
	$parts = explode( '.', $str );
	return end( $parts );
}

// processes the uploaded images and attaches them to the post as a thumbnail
function pig_insert_attachment( $file_handler, $post_id, $setthumb='false' ) {

	// check to make sure its a successful upload
	if ( $_FILES[$file_handler]['error'] !== UPLOAD_ERR_OK )
		return false;

	require_once ABSPATH . "wp-admin" . '/includes/image.php';
	require_once ABSPATH . "wp-admin" . '/includes/file.php';
	require_once ABSPATH . "wp-admin" . '/includes/media.php';

	$attach_id = media_handle_upload( $file_handler, $post_id );

	if ( $setthumb ) update_post_meta( $post_id, '_thumbnail_id', $attach_id );
	return $attach_id;
}

function pig_upload_image() {

	global $blog_id;
	if ( isset( $_POST['pig_nonce'] ) && wp_verify_nonce( $_POST['pig_nonce'], 'pig-nonce' ) ) {

		if ( ! is_user_logged_in() )
			return;

		$user_id = get_current_user_ID();
		$referer = isset( $_POST['pig_referrer'] ) ? strip_tags( $_POST['pig_referrer'] ) : $_SERVER['HTTP_REFERER'];
		$status = isset( $_POST['pig_image_status'] ) ? sanitize_text_field( $_POST['pig_image_status'] ) : 'finished';

		$name = isset( $_POST['pig_image_name'] ) ? sanitize_text_field( $_POST['pig_image_name'] ) : '';
		$parent_id = isset( $_POST['pig_post_parent_id'] ) ? sanitize_text_field( $_POST['pig_post_parent_id'] ) : '';
		$parent_name = isset( $_POST['pig_post_parent_name'] ) ? sanitize_text_field( $_POST['pig_post_parent_name'] ) : '';
		$desc = isset( $_POST['pig_image_desc'] ) ? strip_tags( stripslashes( $_POST['pig_image_desc'] ), '<iframe><p><strong><ul><ol><li><em><a>' ) : '';
		$cat = isset( $_POST['pig_image_cat'] ) ? sanitize_text_field( $_POST['pig_image_cat'] ) : '';
		$can_be_used = isset( $_POST['pig_okay_to_use'] ) && sanitize_text_field( $_POST['pig_okay_to_use'] ) == 1 ? 'yes' : 'no';

		$image = $_FILES['pig_image_file']['name'];
		$size  = $_FILES['pig_image_file']['size'];
		$error = $_FILES['pig_image_file']['error'];

		// max is 2.2 mb
		// only images 2.2 meg or less are allowed

		if ( $error === UPLOAD_ERR_INI_SIZE || $size > 2200000 ) {
			wp_redirect( add_query_arg( 'image-error', '1', $referer ) ); exit;
		}
		if ( empty( $name ) ) {
			wp_redirect( add_query_arg( 'image-error', '2', $referer ) ); exit;
		}
		if ( empty( $desc ) || $desc == 'Describe your image' ) {
			wp_redirect( add_query_arg( 'image-error', '3', $referer ) ); exit;
		}
		if ( ! $image ) {
			wp_redirect( add_query_arg( 'image-error', '4', $referer ) ); exit;
		}
		if( $error !== UPLOAD_ERR_OK ){ // let's just catch if it wasn't a successful upload
			wp_redirect( add_query_arg( array('image-error' => '5', 'image-error-code' => $error), $referer ) );
			exit;
		}

		// everything ok
		if ( ! $error ) {
			$image_data = array(
				'post_title'   => $name,
				'post_content' => $desc,
				'post_status'  => 'publish',
				'post_author'  => $user_id,
				'post_type'    => 'images',
			);

			// create the pending IMAGE post
			$image_id = wp_insert_post( $image_data );

			// the IMAGE post was created okay
			if ( $image_id ) {

				//$cat_name = get_term_by('id', $cat, 'imagecategories');
				wp_set_object_terms( $image_id, $cat, 'imagecategories' );
				wp_set_object_terms( $image_id, 'gallery', 'imagetags' );

				// process the image upload and set it as a thumbnail
				if ( $_FILES ) {
					foreach ( $_FILES as $file => $array ) {
						$thumbnail = pig_insert_attachment( $file, $image_id, true );
					}
				}

				$permalink  = get_permalink( $image_id );
				$subsite_id = $blog_id;

				// set the post meta that attaches it to its parent post
				update_post_meta( $image_id, 'pig_parent_post_id', $parent_id );
				update_post_meta( $image_id, 'pig_subsite_id', $subsite_id );
				update_post_meta( $image_id, 'pig_subsite_image_id', $image_id );
				update_post_meta( $image_id, 'pig_parent_post_name', $parent_name );
				update_post_meta( $image_id, 'pig_image_url', $permalink );
				update_post_meta( $image_id, 'pig_okay_to_use', $can_be_used );

				if ( isset( $_POST['pig_mature'] ) && $_POST['pig_mature'] == 1 ) {
					update_post_meta( $image_id, 'pig_mature', 'on' );
				}

				if( class_exists( 'CWS_Fragment_Cache' ) ) {
					switch_to_blog( 1 );

					// Flush user profile cache
					$frag = new CWS_Fragment_Cache( 'cgc-profile-' . $user_id, 3600 );
					$frag->flush();

					restore_current_blog();

				}

				do_action( 'pig_image_uploaded', $image_id, $thumbnail );

				wp_redirect( $referer . '?image-submitted=1&image-id=' . $image_id . '#image-gallery' ); exit;

			} else {
				wp_redirect( $referer . '?image-submitted=0&error=6#image-gallery' ); exit;
			}
		}

		// if there's an error
		else {
			wp_die( 'The gallery gremlins are acting up. Please click back and try again.', 'Error' );
		}
	}
}
add_action( 'init', 'pig_upload_image' );
