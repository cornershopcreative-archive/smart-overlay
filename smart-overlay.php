<?php
/**
Plugin Name: Smart Overlay
Plugin URI: https://wordpress.org/plugins/smart-overlay/
Description: Show a highly-configurable lightbox modal on your pages to encourage donations, actions. etc.
Version: 0.8.1
Author: Cornershop Creative
Author URI: https://cornershopcreative.com
License: GPLv2 or later
Text Domain: smart-overlay
 */

if ( ! defined( 'WPINC' ) ) {
	die( 'Direct access not allowed' );
}


define( 'SMART_OVERLAY_VERSION', '0.8.1' );


require_once dirname( __FILE__ ) . '/classes/class-smart-overlay.php';


/**
 * Kick things off by hooking into `plugins_loaded`
 */
function run_smart_overlay() {
	$smart_overlay_plugin = new Smart_Overlay();
	$smart_overlay_plugin->init();
}
add_action( 'plugins_loaded', 'run_smart_overlay' );
