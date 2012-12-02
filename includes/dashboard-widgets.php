<?php
/*
add_action('wp_dashboard_setup', 'pig_dashboard_widgets');

function pig_dashboard_widgets() {
   global $wp_meta_boxes;

   unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_plugins']);
   unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_primary']);
   unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_secondary']);

   wp_add_dashboard_widget('pig_dashboard_widget', 'Pending User Images', 'pig_dashboard_widget_render');
}

function pig_dashboard_widget_render() {
  
  $image_args = array('post_type' => 'images', 'post_status' => 'pending');
  $images = get_posts($image_args);
  echo '<ul>';
  foreach($images as $image) {
	$author = get_userdata($image->post_author);
	echo '<li style="height: 40px; margin: 0 0 10px;">';
		echo '<div style="float: left;margin: 0 5px 0 0;">' . get_the_post_thumbnail($image->ID, array(40,40)) . '</div>';
		echo '<a href="wp-admin/post.php?post=' . $image->ID . '&action=edit">' . $image->post_title . '</a><br/>';
		echo 'Submitted by: <strong>' . $author->user_login . '</strong>';
	echo '</li>';
  }	
  echo '</ul>';
  
}
*/
