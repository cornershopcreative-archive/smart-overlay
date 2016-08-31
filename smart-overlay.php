<?php
/*
Plugin Name: Smart Overlay
Plugin URI: http://cornershopcreative.com/code/smart-overlay
Description: Show a highly-configurable lightbox overlay on your pages to encourage donations, actions. etc.
Version: 0.5.6
Author: Cornershop Creative
Author URI: http://cornershopcreative.com
License: GPLv2 or later
Text Domain: coverlay
*/

if ( ! defined( 'ABSPATH' ) ) { die('Direct access not allowed'); }

define( 'COVERLAY_VERSION', '0.5' );

// include CMB2 for custom metaboxes
require_once dirname( __FILE__ ) . '/fields.php';

// Register Custom Post Type
function smart_overlay_post_type() {

	$labels = array(
		'name'                  => _x( 'Smart Overlays', 'Post Type General Name', 'smart_overlay' ),
		'singular_name'         => _x( 'Smart Overlay', 'Post Type Singular Name', 'smart_overlay' ),
		'menu_name'             => __( 'Smart Overlay', 'smart_overlay' ),
		'name_admin_bar'        => __( 'Smart Overlay', 'smart_overlay' ),
		'archives'              => __( 'Smart Overlay Archives', 'smart_overlay' ),
		'parent_item_colon'     => __( 'Parent Item:', 'smart_overlay' ),
		'all_items'             => __( 'All Items', 'smart_overlay' ),
		'add_new_item'          => __( 'Add New Item', 'smart_overlay' ),
		'add_new'               => __( 'Create New Smart Overlay', 'smart_overlay' ),
		'new_item'              => __( 'New Item', 'smart_overlay' ),
		'edit_item'             => __( 'Edit Item', 'smart_overlay' ),
		'update_item'           => __( 'Update Item', 'smart_overlay' ),
		'view_item'             => __( 'View Item', 'smart_overlay' ),
		'search_items'          => __( 'Search Item', 'smart_overlay' ),
		'not_found'             => __( 'Not found', 'smart_overlay' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'smart_overlay' ),
		'featured_image'        => __( 'Featured Image', 'smart_overlay' ),
		'set_featured_image'    => __( 'Set featured image', 'smart_overlay' ),
		'remove_featured_image' => __( 'Remove featured image', 'smart_overlay' ),
		'use_featured_image'    => __( 'Use as featured image', 'smart_overlay' ),
		'insert_into_item'      => __( 'Insert into item', 'smart_overlay' ),
		'uploaded_to_this_item' => __( 'Uploaded to this item', 'smart_overlay' ),
		'items_list'            => __( 'Items list', 'smart_overlay' ),
		'items_list_navigation' => __( 'Items list navigation', 'smart_overlay' ),
		'filter_items_list'     => __( 'Filter items list', 'smart_overlay' ),
	);
	$args = array(
		'label'                 => __( 'Smart Overlay', 'smart_overlay' ),
		'description'           => __( 'Page overlays for a client\'s website', 'smart_overlay' ),
		'labels'                => $labels,
		'supports'              => array( 'title', 'editor', 'thumbnail', ),
		'hierarchical'          => false,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 5,
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => true,
		'exclude_from_search'   => true,
		'publicly_queryable'    => true,
		'capability_type'       => 'page',
	);
	register_post_type( 'smart_overlay', $args );

}
add_action( 'init', 'smart_overlay_post_type', 0 );



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

add_action( 'manage_smart_overlay_posts_custom_column' , 'smart_overlay_custom_columns', 10, 2 );

function smart_overlay_custom_columns( $column, $post_id ) {
	switch ( $column ) {
		case 'display_lightbox_on':
			$field = 'smart_overlay_display_lightbox_on';
			$display_options = array(
				'home' => __( 'Homepage', 'cmb2' ),
				'all'   => __( 'All Pages', 'cmb2' ),
				'all_but_homepage'     => __( 'All But Homepage', 'cmb2' ),
				'none' => __( 'Nowhere (disabled)', 'cmb2' )
			);
			esc_html_e( $display_options[get_post_meta($post_id, $field, true)], 'smart_overlay' );
			break;
		case 'trigger':
			$field = 'smart_overlay_trigger';
			$amount = get_post_meta($post_id, 'smart_overlay_trigger_amount', true);
			$display_options = array(
				'immediate' => __( 'Immediately on page load', 'cmb2' ),
				'delay' => __( sprintf('%s seconds after load', $amount), 'cmb2' ),
				'scroll' => __( sprintf('After page is scrolled %s pixels', $amount), 'cmb2' ),
				'scroll-half' => __( 'After page is scrolled halfway', 'cmb2' ),
				'scroll-full' => __( 'At bottom of page', 'cmb2' ),
				'minutes' => __( sprintf('After %s minutes spent on site this visit', $amount), 'cmb2' ),
				'pages' => __( sprintf('Once %s pages have been visited in last 90 days', $amount), 'cmb2' )
			);
			esc_html_e( $display_options[get_post_meta($post_id, $field, true)], 'smart_overlay' );
			break;
	}
}

function smart_overlay_add_columns( $columns ) {
	unset($columns['date']);
	$columns['display_lightbox_on'] = __( 'Display Lightbox On', 'smart_overlay' );
	$columns['trigger'] = __( 'Trigger', 'smart_overlay' );
	$columns['date'] = __( 'Date', 'smart_overlay' );
	return $columns;
}
add_filter( 'manage_smart_overlay_posts_columns' , 'smart_overlay_add_columns' );

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
	$query = new WP_Query(array(
		'post_type' => ''
	));
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
