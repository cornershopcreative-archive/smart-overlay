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
		'object_types'  => $post_type, // Post type,
		'context'    => 'side',
		'priority'   => 'low'
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
			'never' => __( 'Never show again', 'cmb2' ),
			'session'   => __( 'Don\'t show again this browser session', 'cmb2' ),
			'wait-7'     => __( 'Wait a week before showing again', 'cmb2' ),
			'wait-30' => __( 'Wait 30 days before showing again', 'cmb2' ),
			'wait-90' => __( 'Wait 90 days before showing again', 'cmb2' ),
			'always' => __( 'Keep showing it', 'cmb2' )
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
		'name' => __( 'Trigger amount', 'cmb2' ),
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
/*if(function_exists("register_field_group")) {
	register_field_group(array (
		'id' => 'acf_lightbox-options',
		'title' => 'Lightbox Options',
		'fields' => array (
			array (
				'key' => 'field_555ca8052ed11',
				'label' => 'Display Lightbox On',
				'name' => 'coverlay_visibility',
				'type' => 'select',
				'choices' => array (
					'home' => 'Homepage',
					'all' => 'All Pages',
					'all_but_homepage' => 'All Pages But the Homepage',
					'none' => 'Nowhere'
				),
				'default_value' => '',
				'allow_null' => 0,
				'multiple' => 0,
			),
			array (
				'key' => 'field_555ca85e2ed12',
				'label' => 'Once Seen',
				'name' => 'coverlay_suppress',
				'type' => 'select',
				'instructions' => 'What should happen once a user has seen the lightbox once?',
				'choices' => array (
					'never' => 'Keep showing it',
					'session' => 'Don\'t show again this browser session',
					'wait-7' => 'Wait a week before showing again',
					'wait-30' => 'Wait 30 days before showing again',
					'wait-90' => 'Wait 90 days before showing again',
					'always' => 'Never show again',
				),
				'default_value' => 'wait-7',
				'allow_null' => 0,
				'multiple' => 0,
			),
			array (
				'key' => 'field_555ca9682ed13',
				'label' => 'Trigger',
				'name' => 'coverlay_trigger',
				'type' => 'select',
				'instructions' => 'When does the lightbox appear?',
				'choices' => array (
					'immediate' => 'Immediately on page load',
					'delay' => 'N seconds after load (specify)',
					'scroll' => 'After page is scrolled N pixels (specify)',
					'scroll-half' => 'After page is scrolled halfway',
					'scroll-full' => 'At bottom of page',
					'minutes' => 'After N minutes spent on site this visit (specify)',
					'pages' => 'Once N pages have been visited in last 90 days (specify)',
				),
				'default_value' => 'immediate',
				'allow_null' => 0,
				'multiple' => 0,
			),
			array (
				'key' => 'field_555caca92ed16',
				'label' => 'Trigger amount',
				'name' => 'coverlay_trigger_amount',
				'type' => 'number',
				'instructions' => 'Specify the precise quantity/time/amount/number for the above',
				'conditional_logic' => array (
					'status' => 1,
					'rules' => array (
						array (
							'field' => 'field_555ca9682ed13',
							'operator' => '!=',
							'value' => 'immediate',
						),
						array (
							'field' => 'field_555ca9682ed13',
							'operator' => '!=',
							'value' => 'scroll-half',
						),
						array (
							'field' => 'field_555ca9682ed13',
							'operator' => '!=',
							'value' => 'scroll-full',
						),
					),
					'allorany' => 'all',
				),
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'min' => '',
				'max' => '',
				'step' => 1,
			),
			array (
				'key' => 'field_555cabc52ed14',
				'label' => 'Content',
				'name' => 'coverlay_content',
				'type' => 'wysiwyg',
				'default_value' => '',
				'toolbar' => 'basic',
				'media_upload' => 'yes',
			),
			array (
				'key' => 'field_555cac132ed15',
				'label' => 'Overlay Identifier',
				'name' => 'overlay_id',
				'prefix' => '',
				'type' => 'text',
				'instructions' => 'Enter a name or number to uniquely identify the current overlay. Change this when revising the overlay content so as to reset users\' cookies.',
				'required' => 1,
				'conditional_logic' => 0,
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'maxlength' => 64,
				'readonly' => 0,
				'disabled' => 0,
			),
			array (
				'key' => 'field_5564c912cdd8d',
				'label' => 'Max Width',
				'name' => 'coverlay_max_width',
				'prefix' => '',
				'type' => 'number',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => 'px',
				'min' => '',
				'max' => '',
				'step' => 1,
				'readonly' => 0,
				'disabled' => 0,
			),
		),
		'location' => array (
			array (
				array (
					'param' => 'options_page',
					'operator' => '==',
					'value' => 'conditional-overlay',
					'order_no' => 0,
					'group_no' => 0,
				),
			),
		),
		'options' => array (
			'position' => 'normal',
			'layout' => 'default',
			'hide_on_screen' => array (
			),
		),
		'menu_order' => 0,
	));
}

*/