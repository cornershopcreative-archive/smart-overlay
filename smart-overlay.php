<?php
/*
Plugin Name: Smart Overlay
Plugin URI: http://cornershopcreative.com/code/smart-overlay
Description: Show a highly-configurable lightbox overlay on your pages to encourage donations, actions. etc.
Version: 0.6
Author: Cornershop Creative
Author URI: http://cornershopcreative.com
License: GPLv2 or later
Text Domain: smart-overlay
*/

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access not allowed' ); }

define( 'SMART_OVERLAY_VERSION', '0.6' );


// include CMB2 for custom metaboxes
require_once dirname( __FILE__ ) . '/fields.php';


// Register Custom Post Type
function smart_overlay_post_type() {

	$labels = array(
		'name'                  => _x( 'Overlays', 'Post Type General Name', 'smart_overlay' ),
		'singular_name'         => _x( 'Overlay', 'Post Type Singular Name', 'smart_overlay' ),
		'menu_name'             => __( 'Smart Overlay', 'smart_overlay' ),
		'name_admin_bar'        => __( 'Smart Overlay', 'smart_overlay' ),
		'archives'              => __( 'Overlay Archives', 'smart_overlay' ),
		'parent_item_colon'     => __( 'Parent Item:', 'smart_overlay' ),
		'all_items'             => __( 'All Overlays', 'smart_overlay' ),
		'add_new_item'          => __( 'Add New Overlay', 'smart_overlay' ),
		'add_new'               => __( 'Create New Overlay', 'smart_overlay' ),
		'new_item'              => __( 'New Overlay', 'smart_overlay' ),
		'edit_item'             => __( 'Edit Overlay', 'smart_overlay' ),
		'update_item'           => __( 'Update Overlay', 'smart_overlay' ),
		'view_item'             => __( 'View Overlay', 'smart_overlay' ),
		'search_items'          => __( 'Search Overlays', 'smart_overlay' ),
		'not_found'             => __( 'Not found', 'smart_overlay' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'smart_overlay' ),
		'featured_image'        => __( 'Featured Image', 'smart_overlay' ),
		'insert_into_item'      => __( 'Insert into overlay', 'smart_overlay' ),
		'uploaded_to_this_item' => __( 'Uploaded to this overlay', 'smart_overlay' ),
		'items_list'            => __( 'Overlay list', 'smart_overlay' ),
		'items_list_navigation' => __( 'Overlay list navigation', 'smart_overlay' ),
		'filter_items_list'     => __( 'Filter overlays', 'smart_overlay' ),
	);
	$args = array(
		'label'                 => __( 'Smart Overlay', 'smart_overlay' ),
		'description'           => __( 'Lightboxes to potentially display on website', 'smart_overlay' ),
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


// Implement content displayed in custom columns for the post list admin page.
function smart_overlay_custom_columns( $column, $post_id ) {

	switch ( $column ) {

		case 'displayed_on':
			$field = 'smart_overlay_display_lightbox_on';
			$display_options = array(
				'home'             => __( 'Homepage', 'smart_overlay' ),
				'all'              => __( 'All Pages', 'smart_overlay' ),
				'all_but_homepage' => __( 'All But Homepage', 'smart_overlay' ),
				'none'             => __( 'Nowhere (disabled)', 'smart_overlay' ),
			);
			esc_html_e( $display_options[ get_post_meta( $post_id, $field, true ) ], 'smart_overlay' );
			break;

		case 'trigger':
			$field = 'smart_overlay_trigger';
			$amount = get_post_meta( $post_id, 'smart_overlay_trigger_amount', true );
			$display_options = array(
				'immediate'   => __( 'Immediately on page load', 'smart_overlay' ),
				'delay'       => __( sprintf( '%s seconds after load', $amount ), 'smart_overlay' ),
				'scroll'      => __( sprintf( 'After page is scrolled %s pixels', $amount ), 'smart_overlay' ),
				'scroll-half' => __( 'After page is scrolled halfway', 'smart_overlay' ),
				'scroll-full' => __( 'At bottom of page', 'smart_overlay' ),
				'minutes'     => __( sprintf( 'After %s minutes spent on site this visit', $amount ), 'smart_overlay' ),
				'pages'       => __( sprintf( 'Once %s pages have been visited in last 90 days', $amount ), 'smart_overlay' ),
			);
			esc_html_e( $display_options[ get_post_meta( $post_id, $field, true ) ], 'smart_overlay' );
			break;

	}//end switch

}
add_action( 'manage_smart_overlay_posts_custom_column' , 'smart_overlay_custom_columns', 10, 2 );


// Declare columns for the post list admin page.
function smart_overlay_add_columns( $columns ) {
	unset( $columns['date'] );
	$columns['displayed_on'] = __( 'Displayed On', 'smart_overlay' );
	$columns['trigger'] = __( 'Trigger', 'smart_overlay' );
	$columns['date'] = __( 'Date', 'smart_overlay' );
	return $columns;
}
add_filter( 'manage_smart_overlay_posts_columns' , 'smart_overlay_add_columns' );


/**
 * Load up our JS
 * We don't inline our JS because we need jQuery dependency
 */
function smart_overlay_js() {

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
add_action( 'wp_enqueue_scripts', 'smart_overlay_js' );


/**
 * Built the global config object.
 */
function set_smart_overlay_variables() {

	global $smart_overlay_config;

	$smart_overlay_config = new stdClass();
	$smart_overlay_config->current_id = '';
	$smart_overlay_config->prefix = apply_filters( 'smart_overlay_prefix', 'smart_overlay_' );

	$query_args = array(
		'post_type'      => 'smart_overlay',
		'posts_per_page' => -1,
		'order'          => 'DESC',
		'orderby'        => 'modified',
		'meta_query'     => array(
			array(
				'key'     => $smart_overlay_prefix . 'display_lightbox_on',
				'value'   => 'none',
				'compare' => '!=',
			),
		),
	);

	$smart_overlay_config->overlays = new WP_Query( $args );
}

add_action( 'init', 'set_smart_overlay_variables' );


/**
 * Based on the current page/thing being displayed, and the overlays available, output into the page <head>
 * a JS object with the appropriate overlay settings.
 */
function smart_overlay_display() {

	global $smart_overlay_config;
	$home_page_overlay_found = false;

	// Obviously we can only do this if there are some overlay posts defined...
	if ( $smart_overlay_config->overlays->have_posts() ) :
		while ( $smart_overlay_config->overlays->have_posts() ) :

			$smart_overlay_config->overlays->the_post();

			// Get the meta values from the current overlay.
			$smart_overlay_id  = get_the_ID();
			$display_filter    = get_post_meta( $smart_overlay_id, $smart_overlay_config->prefix . 'display_lightbox_on' )[0];
			$disable_on_mobile = get_post_meta( $smart_overlay_id, $smart_overlay_config->prefix . 'disable_on_mobile', 1 );

			$home_page_overlay = false;

			// Set a special flag for overlays set to just the homepage when we're on the homepage, so they overrule any overlays set to 'all'
			if ( 'home' === $display_filter && is_front_page() ) {
				$home_page_overlay_found = true;
			}

			// Prepare our config object
			$config = array(
				'background' => get_post_meta( $smart_overlay_id, $smart_overlay_config->prefix . 'bg_image' )[0],
				'context'    => $display_filter,
				'suppress'   => get_post_meta( $smart_overlay_id, $smart_overlay_config->prefix . 'suppress' )[0],
				'trigger'    => get_post_meta( $smart_overlay_id, $smart_overlay_config->prefix . 'trigger' )[0],
				'amount'     => get_post_meta( $smart_overlay_id, $smart_overlay_config->prefix . 'trigger_amount' )[0],
				'max_width'  => get_post_meta( $smart_overlay_id, $smart_overlay_config->prefix . 'max_width' )[0],
				'id'         => sanitize_title_with_dashes( get_post_meta( $smart_overlay_id, $smart_overlay_config->prefix . 'overlay_identifier' )[0] ),
			);

					$script_tag = '<script id="smart-overlay-options">window.smart_overlay_opts = ' . json_encode( $config ) . ';</script>';

			if ( is_front_page() ) {
				// Only Homepage
				if ( 'home' === $display_filter || ( 'all' === $display_filter  && ! $home_page_overlay_found ) ) {

					if ( ! $disable_on_mobile || ( $disable_on_mobile && ! wp_is_mobile() ) ) {

						echo $script_tag;

						$smart_overlay_config->current_id = $smart_overlay_id;

						break;
						// Once we get a single smart overlay, we can stop.
					}
				}
			} else {
				// Not Homepage
				if ( 'all_but_homepage' === $display_filter || 'all' === $display_filter ) {

					if ( ! $disable_on_mobile || ( $disable_on_mobile && ! wp_is_mobile() ) ) {

						echo $script_tag;

						$smart_overlay_config->current_id = $smart_overlay_id;

						break;

					}
				}
			}//end if

	endwhile;
endif;

	wp_reset_postdata();
	wp_reset_query();
}
add_action( 'wp_head', 'smart_overlay_display' );


/**
 * Ouput the actual overlays contents chosen for this page into the footer.
 */
function smart_overlay_footer() {

	global $smart_overlay_config;

	// Don't do anything if there's no overlay on this page.
	if ( ! $smart_overlay_config->current_id ) {
		return;
	}

	// Load up our CSS - we inline CSS because why shoud plugins waste HTTP connections?
	echo '<style id="smart-overlay-inline-css">';
	include_once( 'smart-overlay.css' );
	echo '</style>';

	$display_filter = get_post_meta( $smart_overlay_config->current_id, $smart_overlay_config->prefix . 'display_lightbox_on' )[0];
	$max_width      = get_post_meta( $smart_overlay_config->current_id, $smart_overlay_config->prefix . 'max_width' )[0];
	$content        = apply_filters( 'the_content', get_post_field( 'post_content', $smart_overlay_config->current_id ) );

	// Output our lightbox content
	$inner_style = ( $max_width ) ? ' style="max-width:' . $max_width . 'px;"' : '';
	echo '<div id="smart-overlay-content" style="display: none !important"><div id="smart-overlay-inner" ' . wp_kses( $inner_style, array( 'style' ) ) . '>';
		echo $content;
	echo '</div></div>';

}
add_action( 'wp_footer', 'smart_overlay_footer' );
