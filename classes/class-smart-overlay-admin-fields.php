<?php
/**
 * Configures the meta fields for the overlay options
 * PHP Version 5
 *
 * @since   0.5.5
 * @package Smart_Overlay
 * @author  Cornershop Creative <devs@cshp.co>
 */

if ( ! defined( 'WPINC' ) ) {
	die( 'Direct access not allowed' );
}

class Smart_Overlay_Admin_Fields {

	/**
	 * Holds the CMB2 fields
	 *
	 * @var object
	 */
	private $smart_overlay_fields;


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
		add_action( 'cmb2_admin_init', array( $this, 'smart_overlay_custom_fields' ) );
	}


	/**
	 * Define the custom fields for each Smart Overlay post.
	 */
	public function smart_overlay_custom_fields() {
		$this->smart_overlay_fields = new_cmb2_box(
			array(
				'id'            => $this->prefix . 'options',
				'title'         => __( 'Smart Overlay Options', 'smart_overlay' ),
				'object_types'  => $this->post_type,
				'context'       => 'side',
				'priority'      => 'low',
			)
		);

		$this->smart_overlay_fields->add_field(
			array(
				'name' => __( 'Overlay Identifier', 'smart_overlay' ),
				'desc' => __( 'Enter a name or number to uniquely identify this overlay. Change this when revising the overlay content to reset users’ cookies.', 'smart_overlay' ),
				'id'   => $this->prefix . 'overlay_identifier',
				'type' => 'text_small',
			)
		);

		$this->smart_overlay_fields->add_field(
			array(
				'name'    => 'Background Image',
				'desc'    => 'Upload / Choose an image to be used for overlay background. Best size depends on your overlay’s content, but probably at least 300x300px.',
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

		$this->smart_overlay_fields->add_field(
			array(
				'name'    => __( 'Display Lightbox On', 'smart_overlay' ),
				'desc'    => __( 'Select page(s) on which to show this overlay.', 'smart_overlay' ),
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

		$this->smart_overlay_fields->add_field(
			array(
				'name'    => __( 'Once Seen', 'smart_overlay' ),
				'desc'    => __( 'What should happen after a user sees this overlay?', 'smart_overlay' ),
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

		$this->smart_overlay_fields->add_field(
			array(
				'name'    => __( 'Trigger', 'smart_overlay' ),
				'desc'    => __( 'When does the lightbox appear?', 'smart_overlay' ),
				'id'      => $this->prefix . 'trigger',
				'type'    => 'select',
				'options' => array(
					'immediate'   => __( 'Immediately on page load', 'smart_overlay' ),
					'delay'       => __( 'N seconds after load (specify)', 'smart_overlay' ),
					'scroll'      => __( 'After page is scrolled N pixels (specify)', 'smart_overlay' ),
					'scroll-half' => __( 'After page is scrolled halfway', 'smart_overlay' ),
					'scroll-full' => __( 'After page is scrolled to bottom', 'smart_overlay' ),
					'minutes'     => __( 'After N minutes spent on site this visit (specify)', 'smart_overlay' ),
					'pages'       => __( 'Once N pages have been visited in last 90 days (specify)', 'cmb2' ),
				),
			)
		);

		$this->smart_overlay_fields->add_field(
			array(
				'name'            => __( 'Trigger Amount', 'smart_overlay' ),
				'desc'            => __( 'Specify the precise quantity/time/amount/number ("N") for the trigger.', 'smart_overlay' ),
				'id'              => $this->prefix . 'trigger_amount',
				'type'            => 'text_small',
				'sanitization_cb' => 'smart_overlay_abs',
				'escape_cb'       => 'smart_overlay_abs',
				'attributes'      => array(
					'required'               => true,
					'data-conditional-id'    => $this->prefix . 'trigger',
					'data-conditional-value' => wp_json_encode( array( 'delay', 'scroll', 'minutes', 'pages' ) ),
					'type'                   => 'number',
					// we're making it numeric via https://gist.github.com/jtsternberg/c09f5deb7d818d0d170b
														'pattern'                => '\d*',
					'min'                    => '0',
				),
			)
		);

		$this->smart_overlay_fields->add_field(
			array(
				'name'            => __( 'Max Width', 'smart_overlay' ),
				'desc'            => __( 'Maximum width (in pixels) of the lightbox displayed to users. If blank or zero, lightbox will stretch to accomodate content.', 'smart_overlay' ),
				'id'              => $this->prefix . 'max_width',
				'type'            => 'text_small',
				'sanitization_cb' => 'smart_overlay_absint',
				'escape_cb'       => 'smart_overlay_absint',
				'attributes'      => array(
					'type'    => 'number',
					// we're making it numeric via https://gist.github.com/jtsternberg/c09f5deb7d818d0d170b
					'pattern' => '\d*',
					'min'     => '0',
				),
			)
		);

		$this->smart_overlay_fields->add_field(
			array(
				'name'            => __( 'Max Height', 'smart_overlay' ),
				'desc'            => __( 'Maximum height of the lightbox displayed to users. If blank or zero, lightbox will stretch to accomodate content.', 'smart_overlay' ),
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

		$this->smart_overlay_fields->add_field(
			array(
				'name'            => __( 'Min Height', 'smart_overlay' ),
				'desc'            => __( 'Minimum height of the lightbox displayed to users. If blank or zero, lightbox will only be as tall as content, plus any padding.', 'smart_overlay' ),
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

		$this->smart_overlay_fields->add_field(
			array(
				'name'    => 'Disable On Mobile',
				'desc'    => 'Check this box to suppress this overlay on mobile devices. (Recommended)',
				'id'      => $this->prefix . 'disable_on_mobile',
				'type'    => 'checkbox',
				'default' => $this->smart_overlay_set_checkbox_default_for_new_post( true ),
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
	public function smart_overlay_set_checkbox_default_for_new_post( $default ) {
		return isset( $_GET['post'] ) ? '' : ( $default ? (string) $default : '' ); // WPCS: CSRF ok.
	}


	/**
	 * Wrapper around abs() that can take 3 arguments, because of how CMB2 invokes callbacks
	 *
	 * @return absolute value
	 */
	public function smart_overlay_abs( $value ) {
		return abs( $value );
	}


	/**
	 * Wrapper around absint() that can take 3 arguments, because of how CMB2 invokes callbacks
	 *
	 * @return absolute value
	 */
	public function smart_overlay_absint( $value ) {
		return absint( $value );
	}
}
