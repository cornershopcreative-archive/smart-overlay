<?php
/**
 * Create the custom post type, load dependencies and add hooks
 * PHP Version 5
 *
 * @since 1.0
 * @package Smart_Popup
 * @author Cornershop Creative <devs@cshp.co>
 */

if ( ! defined( 'WPINC' ) ) {
	die( 'Direct access not allowed' );
}

/**
 * Performs 95% of the plugin functionality.
 *
 * Class Smart_Overlay
 */
class Smart_Popup {


	/**
	 * Configuration object for the current popup
	 *
	 * @var StdClass
	 */
	private $config;

	/**
	 * Styles for the popup window and background
	 *
	 * @var string
	 */
	private $modal_styles_output;

	/**
	 * Styles for the modal background mask
	 *
	 * @var string
	 */
	private $modal_outer_style_properties;

	/**
	 * All of the available CSS styles
	 *
	 * @var array
	 */
	private $modal_inner_style_properties;

	/**
	 * All of the CSS Styles the user set for the inner modal
	 *
	 * @var bool
	 */
	private $modal_inner_has_set_style_properties;

	/**
	 * All of the CSS Styles the user set for the outer modal
	 *
	 * @var bool
	 */
	private $modal_outer_has_set_style_properties;

	/**
	 * Assign a stdClass to the config property, query all of the smart popups, load dependencies and set all of the
	 * possible CSS properties
	 */
	public function __construct() {
		$this->modal_inner_style_properties = [
			/* The Background Image is not set here, it's setup as inline JS */
			[
				'id' => 'max_width',
				'property' => 'max-width',
				// 'px' means we're "hard-coding" pixels as the units
				'units' => 'px'
			],
			[
				'id' => 'max_height',
				'property' => 'max-height',
				// true means the units are stored in the CMB2 field
				'units' => true
			],
			[
				'id' => 'min_height',
				'property' => 'min-height',
				// true means the units are stored in the CMB2 field
				'units' => true
			],
			[
				'id' => 'padding',
				'property' => 'padding',
				// 'px' means we're "hard-coding" pixels as the units
				'units' => 'px'
			],
			[
				'id' => 'border_width',
				'property' => 'border-width',
				// 'px' means we're "hard-coding" pixels as the units
				'units' => 'px'
			],
			[
				'id' => 'border_radius',
				'property' => 'border-radius',
				// 'px' means we're "hard-coding" pixels as the units
				'units' => 'px'
			],
			[
				'id' => 'border_color',
				'property' => 'border-color',
				// false means it doesn't require units like a hex
				'units' => false
			],
			[
				'id' => 'opacity',
				'property' => 'opacity',
				// false means it doesn't require units like a hex
				'units' => false
			],
			[
				'id' => 'background_color',
				'property' => 'background-color',
				// false means it doesn't require units like a hex
				'units' => false
			]
		];

		$this->modal_outer_style_properties = [
			[
				'id' => 'background_color_mask',
				'property' => 'background-color',
				'units' => false
			]
		];

		$this->config = new stdClass();
		$this->post_query();
		$this->load_dependencies();
	}

	/**
	 * Do all the hooks
	 */
	public function init() {
		add_action( 'init', array( $this, 'post_type' ), 10 );

		add_action( 'manage_smart_overlay_posts_custom_column', array( $this, 'admin_custom_columns' ), 10, 2 );
		add_filter( 'manage_smart_overlay_posts_columns', array( $this, 'admin_add_columns' ) );

		add_action( 'wp', array( $this, 'post_loop' ), 15 );
		add_action( 'wp', array( $this, 'get_inner_set_styles' ), 50 );
		add_action( 'wp', array( $this, 'get_outer_set_styles' ), 50 );
		add_action( 'wp', array( $this, 'set_js_options' ), 30 );

		add_action( 'wp_footer', array( $this, 'footer' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'assets' ) );
		add_action( 'admin_notices', array( $this, 'multiple_instances_admin_notice' ) );
		add_action( 'post_updated_messages', array( $this, 'cache_admin_notice' ), 10, 1 );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
	}



	/**
	 * Load dependencies
	 */
	private function load_dependencies() {
		// Load CMB2 Library
		if ( file_exists( dirname( __DIR__ ) . '/includes/cmb2/init.php' ) ) {
			include_once dirname( __DIR__ ) . '/includes/cmb2/init.php';
		} elseif ( file_exists( dirname( __DIR__ ) . '/includes/CMB2/init.php' ) ) {
			include_once dirname( __DIR__ ) . '/includes/CMB2/init.php';
		}

		if ( file_exists( dirname( __DIR__ ) . '/includes/CMB2-conditionals/cmb2-conditionals.php' ) ) {
			include_once dirname( __DIR__ ) . '/includes/CMB2-conditionals/cmb2-conditionals.php';
		}

		// Include CMB2 Custom Fields
		include_once dirname( __FILE__ ) . '/class-custom-fields.php';

		// Include CMB2 configuration
		include_once dirname( __FILE__ ) . '/class-admin-fields.php';

		// Initialize CMB2 Custom FIelds
		$fields = new Custom_Fields();
		$fields->init();

		// Initialize CMB2 configuration
		$fields = new Admin_Fields();
		$fields->init();
	}



	/**
	 * Register Custom Post Type for Overlays
	 */
	public function post_type() {
		$labels = array(
			'name'                  => _x( 'Popups', 'Post Type General Name', 'smart_overlay' ),
			'singular_name'         => _x( 'Popup', 'Post Type Singular Name', 'smart_overlay' ),
			'menu_name'             => __( 'Smart Popup', 'smart_overlay' ),
			'name_admin_bar'        => __( 'Smart Popup', 'smart_overlay' ),
			'archives'              => __( 'Popup Archives', 'smart_overlay' ),
			'parent_item_colon'     => __( 'Parent Item:', 'smart_overlay' ),
			'all_items'             => __( 'All Popups', 'smart_overlay' ),
			'add_new_item'          => __( 'Add New Popup', 'smart_overlay' ),
			'add_new'               => __( 'Create New Popup', 'smart_overlay' ),
			'new_item'              => __( 'New Popup', 'smart_overlay' ),
			'edit_item'             => __( 'Edit Popup', 'smart_overlay' ),
			'update_item'           => __( 'Update Popup', 'smart_overlay' ),
			'view_item'             => __( 'View Popup', 'smart_overlay' ),
			'search_items'          => __( 'Search Popups', 'smart_overlay' ),
			'not_found'             => __( 'Not found', 'smart_overlay' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'smart_overlay' ),
			'featured_image'        => __( 'Featured Image', 'smart_overlay' ),
			'insert_into_item'      => __( 'Insert into popup', 'smart_overlay' ),
			'uploaded_to_this_item' => __( 'Uploaded to this popup', 'smart_overlay' ),
			'items_list'            => __( 'Popup list', 'smart_overlay' ),
			'items_list_navigation' => __( 'Popup list navigation', 'smart_overlay' ),
			'filter_items_list'     => __( 'Filter popups', 'smart_overlay' ),
		);
		$args = array(
			'label'                 => __( 'Smart Popup', 'smart_overlay' ),
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



	/**
	 * Implement content displayed in custom columns for the post list admin page.
	 */
	public function admin_custom_columns( $column, $post_id ) {

		switch ( $column ) {

			case 'displayed_on':
				$field = $this->config->prefix . 'display_lightbox_on';
				$display_options = array(
					'home'             => __( 'Homepage', 'smart_overlay' ),
					'all'              => __( 'All Pages', 'smart_overlay' ),
					'all_but_homepage' => __( 'All But Homepage', 'smart_overlay' ),
					'none'             => __( 'Nowhere (disabled)', 'smart_overlay' ),
				);
				// phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText
				esc_html_e( $display_options[ get_post_meta( $post_id, $field, true ) ], 'smart_overlay' );
				break;

			case 'trigger':
				$field = $this->config->prefix . 'trigger';
				$amount = get_post_meta( $post_id, 'smart_overlay_trigger_amount', true );
				$display_options = array(
					'immediate'   => __( 'Immediately on page load', 'smart_overlay' ),
					// phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText
					'delay'       => __( sprintf( '%s seconds after load', $amount ), 'smart_overlay' ),
					// phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText
					'scroll'      => __( sprintf( 'After page is scrolled %s pixels', $amount ), 'smart_overlay' ),
					'scroll-half' => __( 'After page is scrolled halfway', 'smart_overlay' ),
					'scroll-full' => __( 'At bottom of page', 'smart_overlay' ),
					// phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText
					'minutes'     => __( sprintf( 'After %s minutes spent on site this visit', $amount ), 'smart_overlay' ),
					// phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText
					'pages'       => __( sprintf( 'Once %s pages have been visited in last 90 days', $amount ), 'smart_overlay' ),
				);
				// phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText
				esc_html_e( $display_options[ get_post_meta( $post_id, $field, true ) ], 'smart_overlay' );
				break;

		}//end switch

	}



	/**
	 * Declare columns for the post list admin page.
	 *
	 * @param array $columns admin columns.
	 * @return array
	 */
	public function admin_add_columns( $columns ) {
		unset( $columns['date'] );
		$columns['displayed_on'] = __( 'Displayed On', 'smart_overlay' );
		$columns['trigger'] = __( 'Trigger', 'smart_overlay' );
		$columns['date'] = __( 'Date', 'smart_overlay' );
		return $columns;
	}



	/**
	 * Load up our JS
	 * We don't inline our JS because we need jQuery dependency
	 */
	public function assets() {

		if ( ! is_admin() ) {
			wp_enqueue_script(
				'smart-overlay-js',
				plugins_url( '/assets/smart-overlay.js', dirname( __FILE__ ) ),
				array( 'jquery' ),
				SMART_POPUP_VERSION,
				true
			);

			// Check if we should add our JS
			if ( $this->config->display_filter ) {
				wp_add_inline_script( 'smart-overlay-js', 'window.smart_overlay_opts = ' . wp_json_encode( $this->config->js_config ) . ';' );
			}

			wp_enqueue_style(
				'smart-overlay',
				plugins_url( '/assets/smart-overlay.css', dirname( __FILE__ ) ),
				'',
				SMART_POPUP_VERSION
			);

			// Check if any styles were set, if so print them on the page.
			if ( false !== $this->modal_inner_has_set_style_properties || false !== $this->modal_outer_has_set_style_properties ) {
				wp_add_inline_style( 'smart-overlay', $this->modal_styles_output );
			}
		}//end if
	}



	/**
	 * Perform the query for Smart Overlays. Also sets some config properties
	 */
	public function post_query() {
		$this->config->current_id = '';
		$this->config->prefix     = apply_filters( 'smart_overlay_prefix', 'smart_overlay_' );

		$query_args = array(
			'post_type'      => 'smart_overlay',
			'posts_per_page' => -1,
			'order'          => 'DESC',
			'orderby'        => 'modified',
			'meta_query'     => array(
				array(
					'key'     => $this->config->prefix . 'display_lightbox_on',
					'value'   => 'none',
					'compare' => '!=',
				),
			),
		);

		$this->config->overlays = new WP_Query( $query_args );
	}



	/**
	 * Loop through Smart Overlays to find one to display
	 */
	public function post_loop() {
		// Obviously we can only do this if there are some overlay posts defined...
		if ( $this->config->overlays->have_posts() ) :
			while ( $this->config->overlays->have_posts() ) :

				$this->config->overlays->the_post();

				$id = get_the_ID();

				// If we found an overlay to display, keep the overlay's ID, check its mobile display and break the loop
				if ( $this->maybe_display( $id ) ) {
					$this->maybe_mobile_display( $id );
					$this->config->current_id = $id;
					break;
				}

			endwhile;
		endif;

		wp_reset_postdata();
	}

	/**
	 * Checks a Smart Overlay's Meta to determine if it shows on the current page.
	 *
	 * @param int $smart_overlay_id ID for a Smart Overlay Post.
	 *
	 * @return bool
	 */
	public function maybe_display( $smart_overlay_id ) {
		// Get the meta to know which page to display this popup on
		$meta = get_post_meta( $smart_overlay_id, $this->config->prefix . 'display_lightbox_on', true );

		// Does this meta value match the current page?
		if ( $this->check_display( $meta ) ) {
			$this->config->display_filter = true;

			// Minor cleanup
			unset( $meta );
			return true;
		}

		return false;
	}

	/**
	 * Set the mobile display option for an overlay. This method only runs when the correct overlay is found
	 *
	 * @param int $smart_overlay_id ID for a Smart Overlay Post.
	 */
	public function maybe_mobile_display( $smart_overlay_id ) {
		$meta                            = get_post_meta( $smart_overlay_id, $this->config->prefix . 'disable_on_mobile', true );
		$this->config->disable_on_mobile = $meta;
		unset( $meta );
	}

	/**
	 * Helper function that compares the display_lightbox_on meta value
	 *
	 * @param string $meta The Meta key for `this->smart_overlay_config->prefix . display_lightbox_on` has only
	 * 3 possible values to determine which page a smart overlay should display on.
	 * @return bool
	 */
	public function check_display( $meta ) {
		if ( 'all' === $meta || is_front_page() && 'home' === $meta || ! is_front_page() && 'all_but_homepage' === $meta ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Set up the JS Config object
	 */
	public function set_js_options() {

		// Hold all the meta Keys for the JS Object
		$metas = [ 'bg_image', 'suppress', 'trigger', 'trigger_amount', 'max_width', 'overlay_identifier' ];

		// Prepare
		$this->config->js_config = array(
			'context'    => $this->config->display_filter,
			'onMobile'   => ! $this->config->disable_on_mobile,
		);

		foreach ( $metas as $meta_key ) {
			$meta = get_post_meta( $this->config->current_id, $this->config->prefix . $meta_key, true );

			if ( ! empty( $meta ) ) {

				// Grrr the bg_image meta is named `background` in the JS object
				if ( 'bg_image' === $meta_key ) {
					$meta_key = 'background';
				}

				$this->config->js_config[ $meta_key ] = $meta;
			}
		}
	}

	/**
	 * Output the actual overlays contents chosen for this page into the footer.
	 */
	public function footer() {
		// Don't do anything if there's no overlay on this page.
		if ( ! $this->config->current_id ) {
			return;
		}

		// Variables for the modal template
		$content  = apply_filters( 'the_content', get_post_field( 'post_content', $this->config->current_id ) );

		// Load the modal markup
		include_once dirname( __DIR__ ) . '/templates/modal.php';
	}

	/**
	 * Loop through the possible CSS Rules to check if there's post_meta for it
	 */
	public function get_inner_set_styles() {
		// Call the helper function to get_post_meta(), and pass it all the possible inner styles
		$styles = $this->get_style_metas( $this->modal_inner_style_properties );

		// We have CSS. Assemble the styles.
		if ( $styles ) {
			$this->modal_inner_has_set_style_properties = true;
			$this->assemble_styles( $styles, '.smart-overlay .smart-overlay-content' );
			return;
		}

		// This property gets checked if its empty or not inside of the smart_overlay_assets method
		unset( $this->modal_inner_has_set_style_properties );
	}

	/**
	 * Loop through the preset CSS Rules to check if there's post_meta for it
	 */
	public function get_outer_set_styles() {
		// Call the helper function to get_post_meta(), and pass it all the possible inner styles
		$styles = $this->get_style_metas( $this->modal_outer_style_properties );
		// We have CSS. Assemble the styles.
		if ( $styles ) {
			$this->modal_outer_has_set_style_properties = true;
			$this->assemble_styles( $styles );
			return;
		}

		// This property gets checked if its empty or not inside of the smart_overlay_assets method
		unset( $this->modal_outer_has_set_style_properties );
	}

	/**
	 * Helper function for getting CSS out of post meta
	 *
	 * @param array $properties an array of possible CSS properties that could be set.
	 *
	 * @return bool|array $style returns false if no CSS properties are set in the post meta
	 */
	public function get_style_metas( $properties ) {
		$styles = false;

		foreach ( $properties as $style_property ) {
			$property_meta = get_post_meta( $this->config->current_id, $this->config->prefix . $style_property['id'] , true );

			// If there is meta, i.e., the user set a css property, add it to an array
			if ( ! empty( $property_meta ) ) {
				// Skip any property that are empty arrays
				if ( is_array( $property_meta ) && '' === $property_meta['dimension_value'] ) {
					continue;
				}

				// Check if the property requires units or not (like a hexcolor)
				switch ( $style_property['units'] ):
					case 1:
						// Check if the CMB2 field is one that came with a value and units or just a value
						if( is_array( $property_meta ) ) {
							$units = $property_meta['dimension_units'] . ';';
							$value = $property_meta['dimension_value'];
						} else {
							//It's a CMB2 field that came with just a value (probably a legacy field that was for pixels only)
							$units = 'px;';
							$value = $property_meta;
						}
						break;
					case 'px':
						// A legacy field that only ever uses pixels
						$units = 'px;';
						$value = $property_meta;
						break;
					case null:
						// A field like opacity or a hex value
						$units= ';';
						$value = $property_meta;
						break;
				endswitch;

				// something like $styles['max-width'] = 400px;
				$styles[ $style_property['property'] ] =  $value .  $units;
			}
		}

		return $styles;
	}

	/**
	 * Assemble a string of CSS rules for use inside of a style tag
	 *
	 * @param array  $properties array of CSS properties.
	 * @param string $selector the CSS selector to apply these styles to.
	 */
	public function assemble_styles( $properties, $selector = '.smart-overlay' ) {
		$this->modal_styles_output .= "\t" . $selector . '{' . PHP_EOL;

		foreach ( $properties as $style_property => $style_value ) {
			// Assemble one style property like max-height
			$this->modal_styles_output .= "\t\t" . $style_property . ': ' . $style_value . PHP_EOL;
		}

		// If they defined a border, set a solid style for it
		if ( array_key_exists( 'border-width', $properties ) ) {
			$this->modal_styles_output .= "\t\tborder-style:solid;" . PHP_EOL;
		}

		$this->modal_styles_output .= "\t}" . PHP_EOL;
	}

	/**
	 * Admin notice to explain collisions if there's more than one overlay.
	 */
	public function multiple_instances_admin_notice() {

		$overlay_count = wp_count_posts( 'smart_overlay' );
		$current_screen = get_current_screen();

		if ( 'edit-smart_overlay' === $current_screen->id && $overlay_count->publish > 1 ) :
			?>
		 <div class="notice notice-warning notice-alt">
		  <p><?php esc_html_e( 'Note: If more than one overlay is eligible to appear on a given page, only the most recent will be shown to visitors.', 'smart_overlay' ); ?></p>
		 </div>
			<?php
		endif;
	}


	/**
	 * Displays an admin notice when a smart overlay post is updated.
	 *
	 * @param array $messages holds all of the WP admin Messages.
	 *
	 * @return mixed
	 */
	public function cache_admin_notice( $messages ) {
		$post = get_post();
		$post_type = get_post_type( $post );

		// If we are editing another type of post, return the default messages
		if ( 'smart_overlay' !== $post_type ) {
			return $messages;
		}

		// Use the existing `update` message for posts.
		// Otherwise we have to define all 10 messages for the smart_overlay post type.
		// i.e., $message['smart_overlay'][1], $message['smart_overlay'][2] ...
		$messages['post'][1] = __( 'Post updated. Please clear your cache to see your changes.', 'smart_overlay' );

		return $messages;
	}

	/**
	 * Check if a post is being edited to load some styles
	 *
	 * @param string $hook the current page of the admin.
	 */
	public function admin_scripts( $hook ) {
		if ( 'post.php' !== $hook ) {
			return;
		}
		wp_enqueue_style( 'admin-styles', plugins_url( '/assets/smart-overlay-admin.css', dirname( __FILE__ ) ) );
	}
}
