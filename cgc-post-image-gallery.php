<?php
/*
Plugin Name: CGC Post Image Gallery
Plugin URI: http://pippinspages.com
Description: A post image gallery system with submissions
Author: Pippin Williamson
Author URI: http://pippinspages.com
Version: 1.2
*/

/*****************************************
plugin shortname = pig
*****************************************/

/*
error_reporting(E_ALL);
*/
ini_set('display_errors', 'on');


/*****************************************
global variables and CONSTANTS
*****************************************/


// plugin prefix
$pig_prefix = 'pig_';
define( 'CGCPIG_DIR', plugin_dir_url( __FILE__ ) );

/*****************************************
Includes
*****************************************/

if( is_admin() ) {
	include( dirname(__FILE__) . '/includes/metabox.php' );
	include( dirname(__FILE__) . '/includes/columns.php' );
	include( dirname(__FILE__) . '/includes/admin-init.php' );
} else {
	include( dirname(__FILE__) . '/includes/upload-image.php' );
	include( dirname(__FILE__) . '/includes/display-gallery.php' );
	include( dirname(__FILE__) . '/includes/process-image-edit.php' );
	include( dirname(__FILE__) . '/includes/widgets.php' );
	//include( dirname(__FILE__) . '/includes/scripts.php' );
	include( dirname(__FILE__) . '/includes/submission-form.php' );
	include( dirname(__FILE__) . '/includes/user-dashboard.php' );
}
include(dirname(__FILE__) . '/includes/post-types.php');