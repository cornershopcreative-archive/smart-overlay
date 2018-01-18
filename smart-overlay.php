<?php
/*
Plugin Name: Smart Overlay
Plugin URI: http://cornershopcreative.com/code/smart-overlay
Description: Show a highly-configurable lightbox overlay on your pages to encourage donations, actions. etc.
Version: 0.7
Author: Cornershop Creative
Author URI: http://cornershopcreative.com
License: GPLv2 or later
Text Domain: smart-overlay
*/

if ( ! defined( 'WPINC' ) ) {
	die( 'Direct access not allowed' );
}

define( 'SMART_OVERLAY_VERSION', '0.7' );

require_once dirname( __FILE__ ) . '/classes/smart-overlay.php';

function run_smart_overlay() {

	$smart_overlay_plugin = new Smart_Overlay();
	$smart_overlay_plugin->on_loaded();

}
add_action( 'plugins_loaded', 'run_smart_overlay' );