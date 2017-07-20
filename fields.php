<?php

if ( file_exists( dirname( __FILE__ ) . '/cmb2/init.php' ) ) {
	require_once dirname( __FILE__ ) . '/cmb2/init.php';
} elseif ( file_exists( dirname( __FILE__ ) . '/CMB2/init.php' ) ) {
	require_once dirname( __FILE__ ) . '/CMB2/init.php';
}

add_action( 'cmb2_admin_init', 'smart_overlay_custom_fields' );

/**
 * Define the custom fields for each Smart Overlay post.
 */
function smart_overlay_custom_fields() {
	$post_type = array('smart_overlay');
	$prefix = 'smart_overlay_';

	$smart_overlay_fields = new_cmb2_box( array(
		'id'            => $prefix . 'options',
		'title'         => __( 'Smart Overlay Options', 'smart_overlay' ),
		'object_types'  => $post_type,
		'context'       => 'side',
		'priority'      => 'low'
	) );

	$smart_overlay_fields->add_field( array(
		'name'    => 'Background Image',
		'desc'    => 'Upload / Choose an image to be used for overlay background',
		'id'      => $prefix . 'bg_image',
		'type'    => 'file',
		// Optional:
		'options' => array(
			'url' => false, // Hide the text input for the url
		),
		'text'    => array(
			'add_upload_file_text' => 'Add Image' // Change upload button text. Default: "Add or Upload File"
		),
		// query_args are passed to wp.media's library query.
		'query_args' => array(
			'type' => array( 'type' => 'image' ) // Make library only display images.
		),
	) );

	$smart_overlay_fields->add_field( array(
		'name'    => __( 'Display Lightbox On', 'smart_overlay' ),
		'desc'    => __( 'Select page(s) on which to show this overlay.', 'smart_overlay' ),
		'id'      => $prefix . 'display_lightbox_on',
		'type'    => 'select',
		'options' => array(
			'home'             => __( 'Homepage', 'smart_overlay' ),
			'all'              => __( 'All Pages', 'smart_overlay' ),
			'all_but_homepage' => __( 'All But Homepage', 'smart_overlay' ),
			'none'             => __( 'Nowhere (disabled)', 'cmb2' )
		)
	) );

	$smart_overlay_fields->add_field( array(
		'name'    => __( 'Once Seen', 'smart_overlay' ),
		'desc'    => __( 'What should happen after a user sees this overlay?', 'smart_overlay' ),
		'id'      => $prefix . 'suppress',
		'type'    => 'select',
		'options' => array(
			'always'  => __( 'Never show it to that user again', 'smart_overlay' ),
			'session' => __( 'Don\'t show again during the user\'s current browser session', 'smart_overlay' ),
			'wait-7'  => __( 'Wait a week before showing it again', 'smart_overlay' ),
			'wait-30' => __( 'Wait 30 days before showing it again', 'smart_overlay' ),
			'wait-90' => __( 'Wait 90 days before showing it again', 'smart_overlay' ),
			'never'   => __( 'Keep showing it', 'cmb2' )
		)
	) );

	$smart_overlay_fields->add_field( array(
		'name'    => __( 'Trigger', 'smart_overlay' ),
		'desc'    => __( 'When does the lightbox appear', 'smart_overlay' ),
		'id'      => $prefix . 'trigger',
		'type'    => 'select',
		'options' => array(
			'immediate'   => __( 'Immediately on page load', 'smart_overlay' ),
			'delay'       => __( 'N seconds after load (specify)', 'smart_overlay' ),
			'scroll'      => __( 'After page is scrolled N pixels (specify)', 'smart_overlay' ),
			'scroll-half' => __( 'After page is scrolled halfway', 'smart_overlay' ),
			'scroll-full' => __( 'At bottom of page', 'smart_overlay' ),
			'minutes'     => __( 'After N minutes spent on site this visit (specify)', 'smart_overlay' ),
			'pages'       => __( 'Once N pages have been visited in last 90 days (specify)', 'cmb2' )
		)
	) );

	$smart_overlay_fields->add_field( array(
		'name' => __( 'Trigger Amount', 'smart_overlay' ),
		'desc' => __( 'Specify the precise quantity/time/amount/number ("N") for the trigger, if necessary.', 'smart_overlay' ),
		'id'   => $prefix . 'trigger_amount',
		'type' => 'text_small',
	) );

	$smart_overlay_fields->add_field( array(
		'name' => __( 'Overlay Identifier', 'smart_overlay' ),
		'desc' => __( 'Enter a name or number to uniquely identify this overlay. Change this when revising the overlay content so as to reset users\' cookies.', 'smart_overlay' ),
		'id'   => $prefix . 'overlay_identifier',
		'type' => 'text_small',
	) );

	$smart_overlay_fields->add_field( array(
		'name' => __( 'Max Width', 'smart_overlay' ),
		'desc' => __( 'Maximum width (in pixels) of the lightbox when displayed to users.', 'smart_overlay' ),
		'id'   => $prefix . 'max_width',
		'type' => 'text_small',
	) );

	$smart_overlay_fields->add_field( array(
		'name' => 'Disable On Mobile',
		'desc' => 'Check this box to suppress this overlay on mobile devices. (Recommended)',
		'id'   => $prefix . 'disable_on_mobile',
		'type' => 'checkbox',
	) );

}
add_action( 'quick_edit_custom_box', 'smart_overlay_quick_edit', 10, 2 );
