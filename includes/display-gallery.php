<?php

/**
 * pig_display_gallery()
 *
 * @param mixed $post_id
 * @param integer $display
 * @return string
 */
function pig_display_gallery( $post_id, $display = 8 ) {

	global $pig_base_dir;

	$args = array(
		'meta_key'		=> 'pig_parent_post_id',
		'meta_value'	=> $post_id,
		'post_type'		=> 'images',
		'posts_per_page'=> $display
	);
	$image_query  = new WP_Query();
	$image_query->query( $args );

	ob_start();

	if( $image_query->have_posts() ) {
		echo '<div id="pig_gallery">';
			while ( $image_query->have_posts() ) : $image_query->the_post();
				get_template_part( 'gallery', 'loop-single' );
			endwhile;
			wp_reset_postdata();
		echo '</div>';
	} else {
		echo '<p class="empty">No images submitted yet</p>';
	}

	return ob_get_clean();
}

function pig_get_image( $size = 'full' ) {
	$resize = false;
	$thumb = get_post_thumbnail_id();
	$image = wp_get_attachment_image_src( $thumb, $size );

	if( $image && pig_src_exists( $image[0] ) ) {
		$dims = pig_get_thumbnail_size( $size );
		if( ( $dims[0] != $image[1] || $dims[1] != $image[2] ) && function_exists( 'aq_resize' ) ){
			if( $new_src = aq_resize( $image[0], $dims[0], $dims[1], true, true, true ) )
				return $new_src;
		}

		return $image[0];
	}

	pig_flag_for_removal();

	return false;
}

function pig_get_thumbnail_size( $size ){
	global $_wp_additional_image_sizes;

	foreach( get_intermediate_image_sizes() as $s ){
		$dimensions = array( 0, 0 );
		if( in_array( $s, array( 'thumbnail', 'medium', 'large' ) ) ){
			$dimensions[0] = get_option( $s . '_size_w' );
			$dimensions[1] = get_option( $s . '_size_h' );
		} else {
			if( isset( $_wp_additional_image_sizes ) && isset( $_wp_additional_image_sizes[ $s ] ) )
				$dimensions = array( $_wp_additional_image_sizes[ $s ]['width'], $_wp_additional_image_sizes[ $s ]['height'] );
		}

		if( $s == $size ){
			return $dimensions;
		}
	}

	return false;
}

function pig_src_exists( $src ){
	if( ! $src )
		return false;

	$uploads_dir = wp_upload_dir();

	$path_check1 = str_replace( $uploads_dir['baseurl'], $uploads_dir['basedir'], $src );
	$path_check2 = str_replace( '/virtualwww/staging.cgcookie.com/', '/www/', $path_check1 );

	return ( file_exists( $path_check1 ) || file_exists( $path_check2 ) );

}

function pig_remove_404_images( $q ){
	if( $q->is_main_query() && $q->get('post_type') == 'images' ){
		if( ! is_array( $q->meta_query ) )
			$q->meta_query = array();

		$q->meta_query[] = array(
			'key' => '_pig_image_404',
			'compare' => 'NOT EXISTS',
			'value' => 'CGCOOKIE'
		);
	}

	return $q;
}

add_filter( 'pre_get_posts', 'pig_remove_404_images' );

function pig_check_featured_image(){
	if( is_404() )
		return;

	$fourofour = false;

	if( is_singular( 'images' ) ){
		$post_id = get_queried_object_id();
		$featured = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ) );
		if( ! $featured ){
			$fourofour = true;
		} else {
			$exists = pig_src_exists( $featured[0] );
			if( ! $exists )
				$fourofour = true;
		}
	}

	if( $fourofour ){
		pig_flag_for_removal( $post_id );
		add_filter( 'body_class', 'pig_error404_body_class' );
		status_header( 404 );
		nocache_headers();
		include( get_404_template() );
		exit;
	}
}

add_action( 'template_redirect', 'pig_check_featured_image' );

function pig_flag_for_removal( $id = NULL ){
	if( ! $id )
		$id = get_the_ID();

	$check_flag = get_post_meta( $id, '_pig_image_404', true );

	if ( ! $check_flag )
		$flag = update_post_meta( $id, '_pig_image_404', current_time( 'timestamp' ) + ( 60 * 60 * 24 * 30 ) ); // flag for removal in 30 days.
}

function pig_error404_body_class( $classes ){
	$classes[] = 'error404';
	return $classes;
}