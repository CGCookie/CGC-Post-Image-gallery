<?php


function pig_display_gallery( $post_id, $display = 8 ) {

	global $pig_base_dir;

	$args = array(
		'meta_key'		=> 'pig_parent_post_id',
		'meta_value'	=> $post_id,
		'post_type'		=> 'images',
		'posts_per_page'=> $display
	);
	$image_query  = new WP_Query();
	$image_query->query($args);

	ob_start();

	if( $image_query->have_posts() ) {
		echo '<div id="pig_gallery">';
			while ( $image_query->have_posts() ) : $image_query->the_post();
				get_template_part('gallery', 'loop-single');
			endwhile;
			wp_reset_postdata();
		echo '</div>';
	} else {
		return '<p class="empty">No images submitted yet</p>';
	}

	return ob_get_clean();
}
