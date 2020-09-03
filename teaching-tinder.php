<?php
/*
Plugin Name: Teaching Tinder
Description: Browse teaching opportunities at Imperial
Version: 0.1
Author: Alex Furr
License: GPL
*/


if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Global defines
define( 'ICL_TEACHING_TINDER_URL', plugins_url('teaching-tinder' , dirname( __FILE__ )) );
define( 'ICL_TEACHING_TINDER_PATH', plugin_dir_path(__FILE__) );


include_once( ICL_TEACHING_TINDER_PATH . 'classes/class-wp.php' );
include_once( ICL_TEACHING_TINDER_PATH . 'classes/class-tt-opps-cpt.php' );
include_once( ICL_TEACHING_TINDER_PATH . 'lib/forms/class-forms.php' );
include_once( ICL_TEACHING_TINDER_PATH . 'classes/class-draw.php' );
include_once( ICL_TEACHING_TINDER_PATH . 'classes/class-queries.php' );
include_once( ICL_TEACHING_TINDER_PATH . 'classes/class-db.php' );
include_once( ICL_TEACHING_TINDER_PATH . 'classes/class-actions.php' );
include_once( ICL_TEACHING_TINDER_PATH . 'classes/class-ajax.php' );

//include_once( ICL_TEACHING_TINDER_PATH . 'classes/class-database.php' );
//include_once( ICL_TEACHING_TINDER_PATH . 'classes/class-cpt-admin-setup.php' );
//include_once( ICL_TEACHING_TINDER_PATH . 'classes/class-utils.php' );


?>
