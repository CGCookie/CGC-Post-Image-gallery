<?php

/*****************************************
CGC PIG Post Type and Taxonomies
*****************************************/

function pig_create_post_types() {


	$images_labels = array(
		'name' => _x( 'Images', 'post type general name' ), // Tip: _x('') is used for localization
		'singular_name' => _x( 'Image', 'post type singular name' ),
		'add_new' => _x( 'Add Image', 'Image' ),
		'add_new_item' => __( 'Add Image' ),
		'edit_item' => __( 'Edit Image' ),
		'new_item' => __( 'Add Image' ),
		'view_item' => __( 'View Image' ),
		'search_items' => __( 'Search Images' ),
		'not_found' =>  __( 'No Images found' ),
		'not_found_in_trash' => __( 'No Images found in Trash' ),
		'parent_item_colon' => ''
	);

 	$images_args = array(
     	'labels' =>$images_labels,
     	'singular_label' => __('Image'),
     	'public' => true,
     	'show_ui' => true,
		'has_archive' => 'images',
	  	'capability_type' => 'post',
     	'hierarchical' => false,
		'exclude_from_search' => true,
     	'rewrite' => array('slug' => 'images'),
     	'supports' => array('title', 'editor', 'revisions', 'comments', 'author', 'thumbnail', 'custom-fields'),
		'menu_position' => 5
     );
 	register_post_type('images',$images_args);

}
add_action('init', 'pig_create_post_types');



// registration code for ImageCategories taxonomy
function pig_register_imagecategories_tax() {
	$labels = array(
		'name' 					=> _x( 'Categories', 'taxonomy general name' ),
		'singular_name' 		=> _x( 'Category', 'taxonomy singular name' ),
		'add_new' 				=> _x( 'Add New Category', 'Category'),
		'add_new_item' 			=> __( 'Add New Category' ),
		'edit_item' 			=> __( 'Edit Category' ),
		'new_item' 				=> __( 'New Category' ),
		'view_item' 			=> __( 'View Category' ),
		'search_items' 			=> __( 'Search Categories' ),
		'not_found' 			=> __( 'No Category found' ),
		'not_found_in_trash' 	=> __( 'No Category found in Trash' ),
	);

	$pages = array('images');

	$args = array(
		'labels' 			=> $labels,
		'singular_label' 	=> __('Category'),
		'public' 			=> true,
		'show_ui' 			=> true,
		'hierarchical' 		=> true,
		'show_tagcloud' 	=> false,
		'show_in_nav_menus' => true,
		'rewrite' 			=> array('slug' => 'image_categories'),
	 );
	register_taxonomy('imagecategories', $pages, $args);

	$labels = array(
		'name' 					=> _x( 'Tags', 'taxonomy general name' ),
		'singular_name' 		=> _x( 'Tag', 'taxonomy singular name' ),
		'add_new' 				=> _x( 'Add New Tag', 'Tag'),
		'add_new_item' 			=> __( 'Add New Tag' ),
		'edit_item' 			=> __( 'Edit Tag' ),
		'new_item' 				=> __( 'New Tag' ),
		'view_item' 			=> __( 'View Tag' ),
		'search_items' 			=> __( 'Search Tags' ),
		'not_found' 			=> __( 'No Tags found' ),
		'not_found_in_trash' 	=> __( 'No Tags found in Trash' ),
	);

	$pages = array('images');

	$args = array(
		'labels' 			=> $labels,
		'singular_label' 	=> __('Tag'),
		'public' 			=> true,
		'show_ui' 			=> true,
		'hierarchical' 		=> true,
		'show_tagcloud' 	=> false,
		'show_in_nav_menus' => true,
		'rewrite' 			=> array('slug' => 'image_tags'),
	 );
	register_taxonomy('imagetags', $pages, $args);
}
add_action('init', 'pig_register_imagecategories_tax');
