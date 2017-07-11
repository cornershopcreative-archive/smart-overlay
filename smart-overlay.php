<?php
/*
Plugin Name: Smart Overlay
Plugin URI: http://cornershopcreative.com/code/smart-overlay
Description: Show a highly-configurable lightbox overlay on your pages to encourage donations, actions. etc.
Version: 0.5.6
Author: Cornershop Creative
Author URI: http://cornershopcreative.com
License: GPLv2 or later
Text Domain: smart-overlay
*/

if ( ! defined( 'ABSPATH' ) ) { die('Direct access not allowed'); }

define( 'SMART_OVERLAY_VERSION', '0.5' );

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
		'supports'              => array( 'title', 'editor' ),
		'hierarchical'          => false,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 30,
		'menu_icon'             => 'dashicons-slides',
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => false,
		'exclude_from_search'   => true,
		'publicly_queryable'    => false,
		'capability_type'       => 'page',
	);
	register_post_type( 'smart_overlay', $args );

}
add_action( 'init', 'smart_overlay_post_type', 0 );


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
			'smart-overlay-js',
			plugins_url( '/smart-overlay.js', __FILE__ ),
			array( 'jquery' ),
			SMART_OVERLAY_VERSION,
			true
		);
	}

}
add_action( 'wp_enqueue_scripts', 'coverlay_js' );

function set_smart_overlay_variables() {
	
	global $get_smart_overlays;
	global $smart_overlay_prefix;
	global $current_smart_overlay_id;

	$current_smart_overlay_id = '';

	$smart_overlay_prefix = 'smart_overlay_';

	$args = array(
		'post_type' => 'smart_overlay',
		'posts_per_page' => -1,
		'order' => 'DESC',
		'orderby' => 'modified',
		'meta_query' => array(
			array(
				'key' => $smart_overlay_prefix .'display_lightbox_on',
				'value' => 'none',
				'compare' => '!='
			),
		)
	);

	$get_smart_overlays = new WP_Query( $args );
}

add_action( 'init', 'set_smart_overlay_variables' );

function smart_overlay_display( ) {
	global $get_smart_overlays;
	global $smart_overlay_prefix;
	global $current_smart_overlay_id;

	if ( $get_smart_overlays->have_posts() ): while( $get_smart_overlays->have_posts() ):
		$get_smart_overlays->the_post();

		$smart_overlay_id = get_the_ID();
		$display_filter = get_post_meta( $smart_overlay_id, $smart_overlay_prefix.'display_lightbox_on')[0];
		$disable_on_mobile = get_post_meta( $smart_overlay_id, $smart_overlay_prefix.'disable_on_mobile', 1 );

		$home_page_overlay = false;

		if ( $display_filter === 'home' && is_front_page() ) {
			$home_page_overlay = true;
		}

		$config = array(
			'background' => get_post_meta( $smart_overlay_id, $smart_overlay_prefix.'bg_image')[0],
			'context'   => $display_filter,
			'suppress'  => get_post_meta( $smart_overlay_id, $smart_overlay_prefix.'suppress')[0],
			'trigger'   => get_post_meta( $smart_overlay_id, $smart_overlay_prefix.'trigger')[0],
			'amount'    => get_post_meta( $smart_overlay_id, $smart_overlay_prefix.'trigger_amount')[0],
			'max_width' => get_post_meta( $smart_overlay_id, $smart_overlay_prefix.'max_width')[0],
			'id'        => sanitize_title_with_dashes( get_post_meta( $smart_overlay_id, $smart_overlay_prefix.'overlay_identifier')[0] )
		);

		if ( is_front_page() ) { // Only Homepage

			if ( $display_filter === 'home' || ( $display_filter === 'all' && $home_page_overlay === false ) ) {

				if ( ! $disable_on_mobile || ( $disable_on_mobile && ! wp_is_mobile() ) ) {

					echo '<script>window.coverlay_opts = ' . json_encode( $config ) . ';</script>';

					$current_smart_overlay_id = $smart_overlay_id;

					break;

				}

			}

		} else { // Not Homepage

			if ( $display_filter === 'all_but_homepage' || $display_filter === 'all' ) {

				if ( ! $disable_on_mobile || ( $disable_on_mobile && ! wp_is_mobile() ) ) {

					echo '<script>window.coverlay_opts = ' . json_encode( $config ) . ';</script>';

					$current_smart_overlay_id = $smart_overlay_id;

					break;

				}

			}

		}

	endwhile;endif;

	wp_reset_postdata();
	wp_reset_query();
}

add_action( 'wp_head', 'smart_overlay_display' );

/**
 * Stuff for the footer
 */
function coverlay_footer() {

	global $smart_overlay_prefix;
	global $current_smart_overlay_id;


	// Load up our CSS
	// We inline CSS because why waste HTTP connections?
	echo '<style id="smart-overlay-inline-css">';
	include_once('smart-overlay.css');
	echo '</style>';
	

		$display_filter = get_post_meta( $current_smart_overlay_id, $smart_overlay_prefix.'display_lightbox_on')[0];
		$max_width = get_post_meta( $current_smart_overlay_id, $smart_overlay_prefix.'max_width')[0];
		$content = apply_filters('the_content', get_post_field('post_content', $current_smart_overlay_id));

		// Output our lightbox content
		$inner_style = ( $max_width ) ? ' style="max-width:' . $max_width . 'px;"' : '';
		if ( $current_smart_overlay_id !== '' ) {
			echo '<div id="smart-overlay-content" style="display: none !important"><div id="smart-overlay-inner" ' . wp_kses( $inner_style, array('style') ) . '>';
				echo $content;
			echo '</div></div>';
		}

}
add_action( 'wp_footer', 'coverlay_footer' );
