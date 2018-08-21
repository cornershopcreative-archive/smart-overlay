<?php
/**
 * Configures the meta fields for the overlay options
 * PHP Version 5
 *
 * @since   1.0
 * @package Smart_Popup
 * @author  Cornershop Creative <devs@cshp.co>
 */

if ( ! defined( 'WPINC' ) ) {
	die( 'Direct access not allowed' );
}

/**
 * Sets all of standard CMB2 fields for the popup
 *
 * Class Smart_Overlay_Admin_Fields
 */
class Admin_Fields {

	/**
	 * Holds the all the non-style fields
	 *
	 * @var object
	 */
	private $config_fields;



	/**
	 * Holds all the fields for the popup mask styles
	 *
	 * @var object
	 */
	private $outer_style_fields;



	/**
	 * Holds all the fields for the popup styles
	 *
	 * @var object
	 */
	private $inner_style_fields;



	/**
	 * Set the post type for CMB2
	 *
	 * @var array
	 */
	private $post_type;



	/**
	 * Prefix for CMB2
	 *
	 * @var String
	 */
	private $prefix;


	/**
	 * Sets up some variables on instantiation.
	 */
	public function __construct() {
		$this->post_type = array( 'smart_overlay' );
		$this->prefix = 'smart_overlay_';
	}



	/**
	 * Initialize hooks
	 */
	public function init() {
		add_action( 'cmb2_admin_init', array( $this, 'setup_CMB2_fields' ) );
	}



	/**
	 * Define the custom fields for each Smart Overlay post.
	 */
	public function setup_CMB2_fields() {
		// Set up the Display Options Box
		$this->config_fields = new_cmb2_box(
			array(
				'id'            => $this->prefix . 'options',
				'title'         => __( 'Smart Popup Display Options', 'smart_overlay' ),
				'object_types'  => $this->post_type,
				'context'       => 'side',
				'priority'      => 'low',
			)
		);

		// Set up the Outer Styles Box
		$this->outer_style_fields = new_cmb2_box(
			array(
				'id'            => $this->prefix . '_outer_styles',
				'title'         => __( 'Smart Popup Outer Styles', 'smart_overlay' ),
				'object_types'  => $this->post_type,
				'context'       => 'side',
				'priority'      => 'low',
			)
		);

		// Set up the Inner Styles Box
		$this->inner_style_fields = new_cmb2_box(
			array(
				'id'            => $this->prefix . '_inner_fields',
				'title'         => __( 'Smart Popup Inner Styles', 'smart_overlay' ),
				'object_types'  => $this->post_type,
				'context'       => 'side',
				'priority'      => 'low',
			)
		);

		// Identifier
		$this->config_fields->add_field(
			array(
				'name' => __( 'Popup Identifier', 'smart_overlay' ),
				'desc' => __( 'Enter a name or number to uniquely identify this popup. Change this when revising the popup content to reset users’ cookies.', 'smart_overlay' ),
				'id'   => $this->prefix . 'overlay_identifier',
				'type' => 'text_small',
				'sanitization_cb' => [$this, 'smart_overlay_dashes'],
			)
		);

		// Page Display
		$this->config_fields->add_field(
			array(
				'name'    => __( 'Display Popup On', 'smart_overlay' ),
				'desc'    => __( 'Select page(s) on which to show this popup.', 'smart_overlay' ),
				'id'      => $this->prefix . 'display_lightbox_on',
				'type'    => 'select',
				'options' => array(
					'home'             => __( 'Homepage', 'smart_overlay' ),
					'all'              => __( 'All Pages', 'smart_overlay' ),
					'all_but_homepage' => __( 'All But Homepage', 'smart_overlay' ),
					'none'             => __( 'Nowhere (disabled)', 'cmb2' ),
				),
			)
		);

		// Display Frequency
		$this->config_fields->add_field(
			array(
				'name'    => __( 'Once Seen', 'smart_overlay' ),
				'desc'    => __( 'What should happen after a user sees this popup? Note: This setting may be overridden when a user clears their cookies.', 'smart_overlay' ),
				'id'      => $this->prefix . 'suppress',
				'type'    => 'select',
				'options' => array(
					'always'  => __( 'Never show it to that user again', 'smart_overlay' ),
					'session' => __( 'Don\'t show again during the user\'s current browser session', 'smart_overlay' ),
					'wait-7'  => __( 'Wait a week before showing it again', 'smart_overlay' ),
					'wait-30' => __( 'Wait 30 days before showing it again', 'smart_overlay' ),
					'wait-90' => __( 'Wait 90 days before showing it again', 'smart_overlay' ),
					'never'   => __( 'Keep showing it', 'cmb2' ),
				),
			)
		);

		// Trigger
		$this->config_fields->add_field(
			array(
				'name'    => __( 'Trigger', 'smart_overlay' ),
				'desc'    => __( 'When does the popup appear?', 'smart_overlay' ),
				'id'      => $this->prefix . 'trigger',
				'type'    => 'select',
				'options' => array(
					'immediate'   => __( 'Immediately on page load', 'smart_overlay' ),
					'delay'       => __( 'N seconds after load (specify)', 'smart_overlay' ),
					'scroll'      => __( 'After page is scrolled N pixels (specify)', 'smart_overlay' ),
					'scroll-half' => __( 'After page is scrolled halfway', 'smart_overlay' ),
					'scroll-full' => __( 'After page is scrolled to bottom', 'smart_overlay' ),
					'minutes'     => __( 'After N minutes spent on site this visit (specify)', 'smart_overlay' ),
					'pages'       => __( 'Once N pages have been visited in last 90 days (specify)', 'smart_overlay' ),
				),
			)
		);

		// Trigger Amount
		$this->config_fields->add_field(
			array(
				'name'            => __( 'Trigger Amount', 'smart_overlay' ),
				'desc'            => __( 'Specify the precise quantity/time/amount/number ("N") for the trigger.', 'smart_overlay' ),
				'id'              => $this->prefix . 'trigger_amount',
				'type'            => 'text_small',
				'sanitization_cb' => array( $this, 'smart_overlay_abs' ),
				'escape_cb'       => array( $this, 'smart_overlay_abs'),
				'attributes'      => array(
					'required'               => true,
					'data-conditional-id'    => $this->prefix . 'trigger',
					'data-conditional-value' => wp_json_encode( array( 'delay', 'scroll', 'minutes', 'pages' ) ),
					'type'                   => 'number',
					// we're making it numeric via https://gist.github.com/jtsternberg/c09f5deb7d818d0d170b
					'pattern'                => '\d*',
					'min'                    => '0',
					'step'                   => '0.1',
				),
			)
		);

		// Mobile
		$this->config_fields->add_field(
			array(
				'name'    => 'Disable On Mobile',
				'desc'    => 'Check this box to suppress this popup on mobile devices. (Recommended)',
				'id'      => $this->prefix . 'disable_on_mobile',
				'type'    => 'checkbox',
				'default' => $this->set_checkbox_default_for_new_post( true ),
			)
		);

		// Background Image
		$this->inner_style_fields->add_field(
			array(
				'name'    => 'Background Image',
				'desc'    => 'Upload / Choose an image to be used for popup background. Best size depends on your popup’s content, but probably at least 300x300px.',
				'id'      => $this->prefix . 'bg_image',
				'type'    => 'file',
				// Optional:
				'options' => array(
					'url' => false,
					// Hide the text input for the url
				),
				'text'    => array(
					'add_upload_file_text' => 'Add Image',
					// Change upload button text. Default: "Add or Upload File"
				),
				// query_args are passed to wp.media's library query.
				'query_args' => array(
					'type' => array(
						'type' => 'image',
					),
					// Make library only display images.
				),
			)
		);

		// Background Color
		$this->inner_style_fields->add_field(
			array(
				'name'              => __( 'Background Color', 'smart_overlay' ),
				'desc'              => __( 'Background color of the popup.', 'smart_overlay' ),
				'id'                => $this->prefix . 'background_color',
				'type'              => 'colorpicker',
				'options'           => array(
					'alpha'         => true,
				),
			)
		);

		// Max Width
		$this->inner_style_fields->add_field(
			array(
				'name'            => __( 'Max Width', 'smart_overlay' ),
				'desc'            => __( 'Maximum width (in pixels) of the popup displayed to users. If blank or zero, popup will stretch to accommodate content.', 'smart_overlay' ),
				'id'              => $this->prefix . 'max_width',
				'type'            => 'text_small',
				'sanitization_cb' => array( $this, 'smart_overlay_absint' ),
				'escape_cb'       => array( $this, 'smart_overlay_absint' ),
				'attributes'      => array(
					'type'    => 'number',
					// we're making it numeric via https://gist.github.com/jtsternberg/c09f5deb7d818d0d170b
					'pattern' => '\d*',
					'min'     => '0',
				),
			)
		);

		// Max Height
		$this->inner_style_fields->add_field(
			array(
				'name'            => __( 'Max Height', 'smart_overlay' ),
				'desc'            => __( 'Maximum height of the popup displayed to users. If blank or zero, popup will stretch to accommodate content.', 'smart_overlay' ),
				'id'              => $this->prefix . 'max_height',
				'type'            => 'single_dimension_and_unit',
				'attributes'      => array(
					'type'    => 'number',
					// we're making it numeric via https://gist.github.com/jtsternberg/c09f5deb7d818d0d170b
					'pattern' => '\d*',
					'min'     => '0',
				),
			)
		);

		// Min Height
		$this->inner_style_fields->add_field(
			array(
				'name'            => __( 'Min Height', 'smart_overlay' ),
				'desc'            => __( 'Minimum height of the popup displayed to users. If blank or zero, popup will only be as tall as content, plus any padding.', 'smart_overlay' ),
				'id'              => $this->prefix . 'min_height',
				'type'            => 'single_dimension_and_unit',
				'attributes'      => array(
					'type'    => 'number',
					// we're making it numeric via https://gist.github.com/jtsternberg/c09f5deb7d818d0d170b
					'pattern' => '\d*',
					'min'     => '0',
				),
			)
		);

		// Padding
		$this->inner_style_fields->add_field(
			array(
				'name'              => __( 'Padding', 'smart_overlay' ),
				'desc'              => __( 'Padding (in pixels) of the popup.', 'smart_overlay' ),
				'id'                => $this->prefix . 'padding',
				'type'              => 'text_small',
				'sanitization_cb'   => array( $this, 'smart_overlay_absint' ),
				'escape_cb'         => array( $this, 'smart_overlay_absint' ),
				'attributes'        => array(
					'type'          => 'number',
					'pattern'       => '\d*',
					'min'           => '0',
				),
			)
		);

		// Border Width
		$this->inner_style_fields->add_field(
			array(
				'name'              => __( 'Border Width', 'smart_overlay' ),
				'desc'              => __( 'Border width (in pixels) of the popup.', 'smart_overlay' ),
				'id'                => $this->prefix . 'border_width',
				'type'              => 'text_small',
				'sanitization_cb'   => array( $this, 'smart_overlay_absint' ),
				'escape_cb'         => array( $this, 'smart_overlay_absint' ),
				'attributes'        => array(
					'type'          => 'number',
					'pattern'       => '\d*',
					'min'           => '0',
				),
			)
		);

		// Border Radius
		$this->inner_style_fields->add_field(
			array(
				'name'              => __( 'Border Radius', 'smart_overlay' ),
				'desc'              => __( 'Border radius (in pixels) of the popup.', 'smart_overlay' ),
				'id'                => $this->prefix . 'border_radius',
				'type'              => 'text_small',
				'sanitization_cb'   => array( $this, 'smart_overlay_absint' ),
				'escape_cb'         => array( $this, 'smart_overlay_absint' ),
				'attributes'        => array(
					'type'          => 'number',
					'pattern'       => '\d*',
					'min'           => '0',
				),
			)
		);

		// Border Color
		$this->inner_style_fields->add_field(
			array(
				'name'              => __( 'Border Color', 'smart_overlay' ),
				'desc'              => __( 'Border color of the popup.', 'smart_overlay' ),
				'id'                => $this->prefix . 'border_color',
				'type'              => 'colorpicker',
				'options'           => array(
					'alpha'         => true,
				),
			)
		);

		// Opacity
		$this->inner_style_fields->add_field(
			array(
				'name'              => __( 'Opacity', 'smart_overlay' ),
				'desc'              => __( 'The opacity of the popup. 0 is invisible, 1 is full color.', 'smart_overlay' ),
				'id'                => $this->prefix . 'opacity',
				'type'              => 'range_slider',
				'sanitization_cb'   => array( $this, 'smart_overlay_abs' ),
				'escape_cb'         => array( $this, 'smart_overlay_abs' ),
				'attributes'        => array(
					'pattern'       => '\d*',
					'min'           => '0',
				),
				'default'           => 1,
			)
		);

		// Background Color
		$this->outer_style_fields->add_field(
			array(
				'name'              => __( 'Background Color', 'smart_overlay' ),
				'desc'              => __( 'Background color of the mask behind the popup.', 'smart_overlay' ),
				'id'                => $this->prefix . 'background_color_mask',
				'type'              => 'colorpicker',
				'options'           => array(
					'alpha'         => true,
				),
			)
		);

	}


	/**
	 * Only return default value if we don't have a post ID (in the 'post' query variable)
	 * From https://github.com/CMB2/CMB2/wiki/Tips-&-Tricks#setting-a-default-value-for-a-checkbox
	 *
	 * @param  bool $default On/Off (true/false).
	 * @return mixed          Returns true or '', the blank default.
	 */
	public function set_checkbox_default_for_new_post( $default ) {
		// phpcs:ignore WordPress.CSRF.NonceVerification.NoNonceVerification
		return isset( $_GET['post'] ) ? '' : ( $default ? (string) $default : '' );
	}


	/**
	 * Wrapper around absint() that can take 3 arguments, because of how CMB2 invokes callbacks
	 *
	 * @return null|int
	 */
	public function smart_overlay_absint( $value ) {
		// If no value was submitted, return nothing to avoid a 0 being saved to it.
		if ( empty ( $value ) ) {
			return null;
		}
		return absint( $value );
	}


	/**
	 * Wrapper around abs() that can take 3 arguments, because of how CMB2 invokes callbacks
	 *
	 * @return null|float
	 */
	public function smart_overlay_abs( $value ) {
		// If no value was submitted, return nothing to avoid a 0 being saved to it.
		if ( empty ( $value ) ) {
			return null;
		}

		return abs( $value );
	}

	/**
	 * Replace whitespaces for dashes
	 *
	 * @param string $value
	 *
	 * @return string
	 */
	public function smart_overlay_dashes( $value ) {
		return sanitize_title_with_dashes( $value, 'save' );
	}
}
