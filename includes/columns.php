<?php

// Add to our admin_init function
add_filter( 'manage_images_columns', 'pig_add_post_columns' ); 
function pig_add_post_columns($columns) {
	$columns['featured'] = 'Featured';
	return $columns;
}


//modify column content

function pig_edit_columns($image_columns){
	$image_columns = array(
		"cb" => "<input type=\"checkbox\" />",
		"title" => "Title",
		"author" => "Author",
		"date" => "Date",
		"3wp_broadcast" => "Broadcast",
		"featured" => "Featured",
		"won" => "Contest Winner",
		"thumbnail" => "Thumbnail"
	);
	return $image_columns;
}
add_filter("manage_edit-images_columns", "pig_edit_columns");


// Add to our admin_init function
add_action('manage_posts_custom_column', 'pig_render_post_columns', 10, 2);
function pig_render_post_columns($column_name, $id) {
	switch ($column_name) {
	case 'featured':
		$featured = get_post_meta( $id, 'pig_featured', TRUE);
		if($featured == true) {
			echo 'Yes';
		} else { 
			echo 'No';
		}				
		break;
		
	case 'won':
		$won = get_post_meta( $id, 'pig_won', TRUE);
		if($won == true) {
			echo 'Winner!';
		} else { 
			echo 'No';
		}				
		break;
		
	
	case 'thumbnail':
		echo get_the_post_thumbnail($id, array(50,50));
		break;
	}
}
