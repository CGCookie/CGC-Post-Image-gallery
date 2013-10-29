<?php

add_action( 'admin_menu', 'pig_admin_menu' );

function pig_admin_menu(){
	add_media_page( 'Image Repair', 'Image Repair', 'delete_posts', 'pig-image-repair', 'pig_admin_image_repair' );
}

function pig_admin_image_repair(){

	$images = new WP_Query( array(
		'post_type' => 'images',
		'posts_per_page' => '50',
		'post_status' => 'publish',
		'meta_query' => array(
			array(
				'key' => '_pig_image_404',
				'compare' => '!=',
				'value' => 'CGCOOKIE'
			)
		)
	) );

	include( 'admin-page.php' );
}
