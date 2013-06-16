<?php

function pig_sidebar_images_widget($number = 6) {

	$images = get_transient('pig_sidebar_images');
	if($images === false) {
		$image_args = array('post_type' => 'images', 'post_status' => 'publish', 'numberposts' => $number, 'suppress_filters' => true);
		$images = get_posts($image_args);
		set_transient('pig_sidebar_images', $images, 1800);
	}

	if($images) {
		echo '<ul class="user-images-widget clearfix">';
		$count = 0;
		foreach($images as $image) {
			$author = get_userdata($image->post_author);
			if($count == 1 || $count == 3) { $image_class = 'user-image-item last'; } else { $image_class = 'user-image-item'; }
			echo '<li class="' . $image_class . '">';
			echo '<a href="' . get_permalink($image->ID) . '" title="By ' . $author->user_login . '" class="user-image tool-tip">';
			echo get_the_post_thumbnail($image->ID, 'related-image', array("title" => ""));
			echo '</a>';
			echo '</li>';
			$count++;
		}
		echo '</ul>';
		echo '<a href="' . get_bloginfo('url') . '/gallery">View All Images</a>';
	}

}

function pig_sidebar_featured_images_widget($number = 6) {

	$images = get_transient('pig_sidebar_featured_images');
	if($images === false) {
		$image_args = array(
			'post_type' => 'images',
			'post_status' => 'publish',
			'numberposts' => $number,
			'suppress_filters' => true,
			'meta_key' => 'pig_featured',
			'meta_value' => 'on'
		);
		$images = get_posts($image_args);
		set_transient('pig_sidebar_featured_images', $images, 1800);
	}

	if($images) {
		echo '<ul class="user-images-widget clearfix">';
		$count = 0;
		foreach($images as $image) {
			$author = get_userdata($image->post_author);
			if($count == 1 || $count == 3) { $image_class = 'user-image-item last'; } else { $image_class = 'user-image-item'; }
			echo '<li class="' . $image_class . '">';
			echo '<a href="' . get_permalink($image->ID) . '" title="By ' . $author->user_login . '" class="user-image tool-tip">';
			echo get_the_post_thumbnail($image->ID, 'post-image', array("title" => ""));
			echo '</a>';
			echo '</li>';
			$count++;
		}
		echo '</ul>';
		echo '<a href="' . get_bloginfo('url') . '/gallery">View All Images &raquo;</a>';
	}

}

function pig_show_images_from_following($number = 6) {

	global $user_ID;

	if( ! function_exists( 'cgc_get_following' ) )
		return;

	$following = cgc_get_following($user_ID);
	if($following) {
		$users = implode(',', $following);
		$images = new WP_Query( array('post_type' => 'images', 'post_status' => 'publish', 'posts_per_page' => $number, 'author' => $users) );
		if ( $images->have_posts() ) :

			echo '<ul class="user-images-widget clearfix">';

			while ( $images->have_posts() ) : $images->the_post();

				$count = 0;
				if($count == 1 || $count == 3) { $image_class = 'user-image-item last'; } else { $image_class = 'user-image-item'; }
				echo '<li class="' . $image_class . '">';
					echo '<a href="' . get_permalink() . '" title="' . get_the_title() . '" class="user-image tool-tip">';
						echo get_the_post_thumbnail(get_the_ID(), 'related-image', array("title" => ""));
					echo '</a>';
				echo '</li>';
				$count++;

			endwhile;

			echo '</ul>';
			echo '<a href="' . get_bloginfo('url') . '/gallery/?view=following">View All Images &raquo;</a>';
		else:
			echo '<p class="empty">No images found on this site.</p>';
		endif;
		wp_reset_postdata();
	} else {
		echo '<p class="empty">You are not following any users.</p>';
	}
}
