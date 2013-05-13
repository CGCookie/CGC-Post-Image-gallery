<?php


function pig_display_gallery($post_id) {

	global $pig_base_dir;

	$args = array(
		'meta_key'		=> 'pig_parent_post_id',
		'meta_value'	=> $post_id,
		'post_type'		=> 'images',
		'posts_per_page'=> 6
	);		
	$images = get_posts( $args );

	if($images) {
		$gallery = '<ul id="pig_gallery">';
			$i = 1;
			$image_query  = new WP_Query();
			$image_query->query($args);
			while ( $image_query->have_posts() ) : $image_query->the_post();
				if($i == 6 || $i == 12 || $i == 18 || $i == 24 || $i == 20 || $i == 26 || $i == 32) { $class = ' class="last"'; } else { $class = ''; }
				$gallery .= '<li' . $class .'>';
					$image_link = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), array(600, 600) );
					$gallery .= '<a class="thickbox" rel="user-gallery" href="' . $image_link[0] . '" title="' . get_the_title( get_the_ID() ) . ' - by ' . get_the_author_meta( 'user_login' ) . '">';
						$gallery .= get_the_post_thumbnail(get_the_ID(), 'pig-gallery-image');
					$gallery .= '</a>';
				$gallery .= '</li>';
				$i++;
			endwhile; 
			wp_reset_postdata();		
		$gallery .= '</ul>';
		
		
		// Reset Post Data
	
	} else {
		return '<p class="empty">No images submitted yet</p>';
	}
	
	return $gallery;
}
