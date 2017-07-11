<?php

if ( file_exists( dirname( __FILE__ ) . '/cmb2/init.php' ) ) {
	require_once dirname( __FILE__ ) . '/cmb2/init.php';
} elseif ( file_exists( dirname( __FILE__ ) . '/CMB2/init.php' ) ) {
	require_once dirname( __FILE__ ) . '/CMB2/init.php';
}

add_action( 'cmb2_admin_init', 'smart_overlay_custom_fields' );

function smart_overlay_custom_fields() {
	$post_type = array('smart_overlay');
	$prefix = 'smart_overlay_';

	$smart_overlay_fields = new_cmb2_box( array(
		'id'            => $prefix . 'options',
		'title'         => __( 'Smart Overlay Options', 'cmb2' ),
		'object_types'  => $post_type,
		'context'    => 'side',
		'priority'   => 'low'
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
		'name'             => __( 'Display Lightbox On', 'cmb2' ),
		'desc'             => __( 'Select which page to show the lightbox on', 'cmb2' ),
		'id'               => $prefix . 'display_lightbox_on',
		'type'             => 'select',
		'options'          => array(
			'home' => __( 'Homepage', 'cmb2' ),
			'all'   => __( 'All Pages', 'cmb2' ),
			'all_but_homepage'     => __( 'All But Homepage', 'cmb2' ),
			'none' => __( 'Nowhere (disabled)', 'cmb2' )
		)
	) );

	$smart_overlay_fields->add_field( array(
		'name'             => __( 'Once Seen', 'cmb2' ),
		'desc'             => __( 'What should happen once a user has seen the lightbox once?', 'cmb2' ),
		'id'               => $prefix . 'suppress',
		'type'             => 'select',
		'options'          => array(
			'always' => __( 'Never show again', 'cmb2' ),
			'session'   => __( 'Don\'t show again this browser session', 'cmb2' ),
			'wait-7'     => __( 'Wait a week before showing again', 'cmb2' ),
			'wait-30' => __( 'Wait 30 days before showing again', 'cmb2' ),
			'wait-90' => __( 'Wait 90 days before showing again', 'cmb2' ),
			'never' => __( 'Keep showing it', 'cmb2' )
		)
	) );

	$smart_overlay_fields->add_field( array(
		'name'             => __( 'Trigger', 'cmb2' ),
		'desc'             => __( 'When does the lightbox appear', 'cmb2' ),
		'id'               => $prefix . 'trigger',
		'type'             => 'select',
		'options'          => array(
			'immediate' => __( 'Immediately on page load', 'cmb2' ),
			'delay' => __( 'N seconds after load (specify)', 'cmb2' ),
			'scroll' => __( 'After page is scrolled N pixels (specify)', 'cmb2' ),
			'scroll-half' => __( 'After page is scrolled halfway', 'cmb2' ),
			'scroll-full' => __( 'At bottom of page', 'cmb2' ),
			'minutes' => __( 'After N minutes spent on site this visit (specify)', 'cmb2' ),
			'pages' => __( 'Once N pages have been visited in last 90 days (specify)', 'cmb2' )
		)
	) );

	$smart_overlay_fields->add_field( array(
		'name' => __( 'Trigger Amount', 'cmb2' ),
		'desc' => __( 'Specify the precise quantity/time/amount/number for the above', 'cmb2' ),
		'id'   => $prefix . 'trigger_amount',
		'type' => 'text_small',
	) );

	$smart_overlay_fields->add_field( array(
		'name' => __( 'Overlay Identifier', 'cmb2' ),
		'desc' => __( 'Enter a name or number to uniquely identify the current overlay. Change this when revising the overlay content so as to reset users\' cookies', 'cmb2' ),
		'id'   => $prefix . 'overlay_identifier',
		'type' => 'text_small',
	) );

	$smart_overlay_fields->add_field( array(
		'name' => __( 'Max Width', 'cmb2' ),
		'desc' => __( 'Maximum width of the lightbox when displayed on the front end (in pixels)', 'cmb2' ),
		'id'   => $prefix . 'max_width',
		'type' => 'text_small',
		'after_field'  => 'px',
	) );

	$smart_overlay_fields->add_field( array(
		'name' => 'Disable On Mobile',
		'desc' => 'Check this box to disable overlay on mobile devices',
		'id'   => $prefix . 'disable_on_mobile',
		'type' => 'checkbox',
	) );

}

add_action( 'quick_edit_custom_box', 'smart_overlay_quick_edit', 10, 2 );

function smart_overlay_quick_edit( $column_name, $post_type ) {

	if ($post_type === 'smart_overlay') {
		echo $column_name;
		$printNonce = true;
		if ( $printNonce ) {
			$printNonce = false;
			wp_nonce_field( plugin_basename( __FILE__ ), 'smart_overlay_edit_nonce' );
		}

		?>
		<fieldset class="inline-edit-col-right inline-edit-smart_overlay">
			<div class="inline-edit-col column-<?php echo $column_name; ?>">
				<label class="inline-edit-group">
					<?php
					switch ( $column_name ) {
						case 'book_author':
							?><span class="title">Author</span><input name="book_author"/><?php
							break;
						case 'inprint':
							?><span class="title">In Print</span><input name="inprint" type="checkbox"/><?php
							break;
					}
					?>
				</label>
			</div>
		</fieldset>
		<?php
	}
}