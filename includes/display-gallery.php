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

function pig_src_exists( $src ){
	if( ! $src )
		return false;

	$size = @getimagesize( $src );

	if( $size !== false )
		return true;

	$headers = @get_headers( $src );

	return ( strpos( $headers[0], '404' ) === false );

	$ch = curl_init( $src );
    curl_setopt( $ch, CURLOPT_NOBODY, true ); // prevents any content from being downloaded
    curl_setopt( $ch, CURLOPT_TIMEOUT, 5 );
    curl_exec( $ch );
    $code = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
    curl_close($ch);

    return ( $code == 200 );
}