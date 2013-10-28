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
	$thumb   = get_post_thumbnail_id();
	$img_url = wp_get_attachment_image_src( $thumb, $size );
	if( $image ) {
		$src = $image[0];
		if( @ini_get( 'allow_url_fopen' ) ){
			$exists = file_exists( $src );
		} else {
			$headers = @get_headers( $src );
			$exists = (strpos( $headers[0], '404' ) === false);
		}
		if( $exists )
			return $src;

		$check_flag = get_post_meta( get_the_ID(), '_pig_image_404', true );

		if ( ! $check_flag )
			$flag = update_post_meta( get_the_ID(), '_pig_image_404', current_time( 'timestamp' ) + ( 60 * 60 * 24 * 30 ) ); // flag for removal in 30 days.
	}
	return false;
}
