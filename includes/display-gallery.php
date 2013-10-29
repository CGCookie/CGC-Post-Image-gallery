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

	if ( ! $image && $size != 'full' ){
		$image = wp_get_attachment_image_src( $thumb, 'full' );
		$resize = true;
	}

	if( $image ) {
		$src = $image[0];
		$headers = @get_headers( $src );
		$exists = (strpos( $headers[0], '404' ) === false);
		if( $exists ){
			if( $resize && function_exists( 'aq_resize' ) ){
				if( $dims = pig_get_thumbnail_size( $size ) )
					$src = aq_resize( $src, $dims[0], $dims[1], true, true, true );
			}

			return $src;
		}
	}

	$check_flag = get_post_meta( get_the_ID(), '_pig_image_404', true );

	if ( ! $check_flag )
		$flag = update_post_meta( get_the_ID(), '_pig_image_404', current_time( 'timestamp' ) + ( 60 * 60 * 24 * 30 ) ); // flag for removal in 30 days.

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