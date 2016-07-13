<?php
/*
Plugin Name: Conditional Overlay
Plugin URI: http://cornershopcreative.com/code/conditional-overlay
Description: Show a highly-configurable lightbox overlay on your pages to encourage donations, actions. etc.
Version: 0.5.6
Author: Cornershop Creative
Author URI: http://cornershopcreative.com
License: GPLv2 or later
Text Domain: coverlay
*/

if ( ! defined( 'ABSPATH' ) ) { die('Direct access not allowed'); }

define( 'COVERLAY_VERSION', '0.5' );

/**
 * Checks to see if ACF PRO is installed
 */
function coverlay_check_acf() {
	//check if ACF PRO is installed and active. If not, send a warning
	if ( ! is_plugin_active( 'advanced-custom-fields-pro/acf.php' ) && ! is_plugin_active_for_network( 'advanced-custom-fields-pro/acf.php' ) ) {
		add_action( 'admin_notices', 'coverlay_acf_notice' );
	}

}
add_action( 'admin_init', 'coverlay_check_acf' );

/**
 * Displays a notice if ACF PRO isn't installed
 */
function coverlay_acf_notice() {
	echo '<div class="notice error"><p>';
	_e( 'ACF PRO not detected. Conditional Overlay will not function until ACF PRO is installed and enabled.', 'coverlay' );
	echo '</p></div>';
}

/**
 * Create the options page and the fields
 */
function coverlay_init() {
	if ( function_exists( 'acf_add_options_page' ) && function_exists( 'register_field_group' ) ) {

		acf_add_options_page( array(
			'page_title' => 'Conditional Overlay Settings',
			'menu_title' => 'Overlay',
			'menu_slug'  => 'conditional-overlay',
			'capability' => 'edit_theme_options',
			'icon_url'   => 'dashicons-screenoptions',
			'position'   => 3,
		) );

		include_once __DIR__ . '/fields.php';
	}
}
add_action( 'init', 'coverlay_init' );

/**
 * Stuff for the footer
 */
function coverlay_footer() {

	// Load up our CSS
	// We inline CSS because why waste HTTP connections?
	echo '<style id="coverlay-inline-css">';
	include_once('coverlay.css');
	echo '</style>';

	// Output our lightbox content
	$context = get_field( 'coverlay_visibility', 'options' );
	$max_width = get_field( 'coverlay_max_width', 'options' );
	$inner_style = ( $max_width ) ? ' style="max-width:' . $max_width . 'px;"' : '';
	if ( 'all' == $context || ( is_front_page() && 'home' == $context ) || ( !is_front_page() && 'all_but_homepage' == $context ) ) {
		echo '<div id="coverlay-content" style="display: none !important"><div id="coverlay-inner" ' . wp_kses( $inner_style, array('style') ) . '>';
		the_field( 'coverlay_content', 'options' );
		echo '</div></div>';
	}

}
add_action( 'wp_footer', 'coverlay_footer' );

/**
 * Load up our JS
 * We don't inline our JS because we need jQuery dependency
 */
function coverlay_js() {

	if ( ! is_admin() ) {
		wp_enqueue_script(
			'coverlay-js',
			plugins_url( '/coverlay.js', __FILE__ ),
			array( 'jquery' ),
			COVERLAY_VERSION,
			true
		);
	}

}
add_action( 'wp_enqueue_scripts', 'coverlay_js' );


/**
 * Outputs the theme options into a JS var for use
 */
function coverlay_echo_config() {
	$config = array(
		'context'   => get_field( 'coverlay_visibility', 'options' ),
		'suppress'  => get_field( 'coverlay_suppress', 'options' ),
		'trigger'   => get_field( 'coverlay_trigger', 'options' ),
		'amount'    => get_field( 'coverlay_trigger_amount', 'options' ),
		'max_width' => get_field( 'coverlay_max_width', 'options' ),
		'id'        => sanitize_title_with_dashes( get_field( 'coverlay_id', 'options' ) )
	);
	echo '<script>window.coverlay_opts = ' . json_encode( $config ) . ';</script>';
}
add_action( 'wp_head', 'coverlay_echo_config' );

