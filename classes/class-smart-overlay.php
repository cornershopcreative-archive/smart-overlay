<?php
/**
 * Create the custom post type, load dependencies and add hooks
 * PHP Version 5
 *
 * @since   0.5.5
 * @package Smart_Overlay
 * @author  Cornershop Creative <devs@cshp.co>
 */

if ( ! defined( 'WPINC' ) ) {
	die( 'Direct access not allowed' );
}

class Smart_Overlay {


	/**
	 * @var Holds standard class
	 */
	private $smart_overlay_config;

	/**
	 * @var Holds the inline styles for the modal window
	 */
	private $modal_outer_style;

	/**
	 * @var array Holds all of the available CSS styles
	 */
	private $modal_style_properties = ['max_width' => 'max-width', 'max_height' => 'max-height', 'min_height' => 'min-height' ];

	/**
	 * @var array Holds all of the CSS Styles the user set
	 */
	private $modal_set_style_properties = [];


	public function __construct() {
		$this->smart_overlay_config = new stdClass();
		$this->smart_overlay_post_query();
		$this->load_dependencies();
	}

	/**
	 * Do all the hooks
	 */
	public function init() {
		add_action( 'init', array( $this, 'smart_overlay_post_type' ), 10 );

		add_action( 'manage_smart_overlay_posts_custom_column', array( $this, 'smart_overlay_custom_columns' ), 10, 2 );
		add_filter( 'manage_smart_overlay_posts_columns', array( $this, 'smart_overlay_add_columns' ) );


		add_action( 'init', array( $this, 'smart_overlay_post_loop' ) );
		add_action( 'init', array( $this, 'smart_overlay_assemble_styles' ), 20 );

		add_action( 'init', array( $this, 'smart_overlay_get_set_styles' ), 10 );

		add_action( 'init', array( $this, 'smart_overlay_display_options' ), 20 );
		add_action( 'init', array( $this, 'smart_overlay_set_js_options' ), 22 );


		add_action( 'wp_footer', array( $this, 'smart_overlay_footer' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'smart_overlay_assets' ) );
		add_action( 'admin_notices', array( $this, 'smart_overlay_admin_notices' ) );
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
		include_once dirname( __FILE__ ) . '/class-smart-overlay-custom-fields.php';

		// Include CMB2 configuration
		include_once dirname( __FILE__ ) . '/class-smart-overlay-admin-fields.php';

		// Initialize CMB2 Custom FIelds
		$fields = new Smart_Overlay_Custom_Fields();
		$fields->init();

		// Initialize CMB2 configuration
		$fields = new Smart_Overlay_Admin_Fields();
		$fields->init();
	}



	/**
	 * Register Custom Post Type for Overlays
	 */
	public function smart_overlay_post_type() {
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



	/**
	 * Implement content displayed in custom columns for the post list admin page.
	 */
	public function smart_overlay_custom_columns( $column, $post_id ) {

		switch ( $column ) {

			case 'displayed_on':
				$field = $this->smart_overlay_config->prefix . 'display_lightbox_on';
				$display_options = array(
					'home'             => __( 'Homepage', 'smart_overlay' ),
					'all'              => __( 'All Pages', 'smart_overlay' ),
					'all_but_homepage' => __( 'All But Homepage', 'smart_overlay' ),
					'none'             => __( 'Nowhere (disabled)', 'smart_overlay' ),
				);
				esc_html_e( $display_options[ get_post_meta( $post_id, $field, true ) ], 'smart_overlay' );
				break;

			case 'trigger':
				$field = $this->smart_overlay_config->prefix . 'trigger';
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



	/**
	 * Declare columns for the post list admin page.
	 *
	 * @param array $columns admin columns.
	 * @return array
	 */
	public function smart_overlay_add_columns( $columns ) {
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
	public function smart_overlay_assets() {

		if ( ! is_admin() ) {
			wp_enqueue_script(
				'smart-overlay-js',
				plugins_url( '/assets/smart-overlay.js', dirname( __FILE__ ) ),
				array( 'jquery' ),
				SMART_OVERLAY_VERSION,
				true
			);

			// Check if we should add our JS
			if ( true === $this->smart_overlay_check_display() ) {
				wp_add_inline_script( 'smart-overlay-js', 'window.smart_overlay_opts = ' . wp_json_encode( $this->smart_overlay_config->js_config ) . ';' );
			}

			wp_enqueue_style(
				'smart-overlay',
				plugins_url( '/assets/smart-overlay.css', dirname( __FILE__ ) ),
				'',
				SMART_OVERLAY_VERSION
			);
			wp_add_inline_style( 'smart-overlay', $this->modal_outer_style );
		}
	}



	/**
	 * Perform the query for Smart Overlays. Also sets some config properties
	 */
	public function smart_overlay_post_query() {
		$this->smart_overlay_config->current_id = '';
		$this->smart_overlay_config->prefix = apply_filters( 'smart_overlay_prefix', 'smart_overlay_' );

		$query_args = array(
			'post_type'      => 'smart_overlay',
			'posts_per_page' => -1,
			'order'          => 'DESC',
			'orderby'        => 'modified',
			'meta_query'     => array(
				array(
					'key'     => $this->smart_overlay_config->prefix . 'display_lightbox_on',
					'value'   => 'none',
					'compare' => '!=',
				),
			),
		);

		$this->smart_overlay_config->overlays = new WP_Query( $query_args );
	}



	/**
	 * Loop through Smart Overlays.
	 */
	public function smart_overlay_post_loop() {
		// Obviously we can only do this if there are some overlay posts defined...
		if ( $this->smart_overlay_config->overlays->have_posts() ) :
			while ( $this->smart_overlay_config->overlays->have_posts() ) :

				$this->smart_overlay_config->overlays->the_post();

				// Get the meta values from the current overlay.
				$this->smart_overlay_config->current_id = get_the_ID();
			endwhile;
		endif;

		wp_reset_postdata();
		wp_reset_query();
	}


	/**
	 * What page is Popup showing on and on mobile?
	 */
	public function smart_overlay_display_options(){
		$this->smart_overlay_config->display_filter    = get_post_meta( $this->smart_overlay_config->current_id, $this->smart_overlay_config->prefix . 'display_lightbox_on', true );
		$this->smart_overlay_config->disable_on_mobile = get_post_meta( $this->smart_overlay_config->current_id, $this->smart_overlay_config->prefix . 'disable_on_mobile', true );
	}


	/**
	 * Check if we should display on the current page
	 */
	public function smart_overlay_check_display(){

		if ( 'all' === $this->smart_overlay_config->display_filter
			 || ( is_front_page() && 'home' === $this->smart_overlay_config->display_filter  )
			 || ( ! is_front_page() && 'all_but_homepage' === $this->smart_overlay_config->display_filter  ) ) {

			return true;

		}else{

			return false;
		}
	}



	/**
	 * Set up the JS Config object
	 */
	public function smart_overlay_set_js_options(){

		//Hold all the meta Keys for the JS Object
		$metas = [ 'bg_image', 'suppress', 'trigger', 'trigger_amount', 'max_width', 'overlay_identifier' ];

		//Prepare
		$this->smart_overlay_config->js_config = array(
			'context'    => $this->smart_overlay_config->display_filter,
			'onMobile'   => ! $this->smart_overlay_config->disable_on_mobile,
		);

		foreach( $metas as $meta_key ) {
			$meta = get_post_meta( $this->smart_overlay_config->current_id, $this->smart_overlay_config->prefix . $meta_key, true );

			if( ! empty( $meta ) ) {

				//Grrr the bg_image meta is named `background` in the JS object
				if( 'bg_image' === $meta_key ) {
					$meta_key = 'background';
				}

				$this->smart_overlay_config->js_config[ $meta_key ] = $meta;
			}
		}
	}




	/**
	 * Output the actual overlays contents chosen for this page into the footer.
	 */
	public function smart_overlay_footer() {
		// Don't do anything if there's no overlay on this page.
		if ( ! $this->smart_overlay_config->current_id ) {
			return;
		}

		// Variables for the modal template
		$content  = apply_filters( 'the_content', get_post_field( 'post_content', $this->smart_overlay_config->current_id ) );

		// Load the modal markup
		include_once dirname(__DIR__) . '/templates/modal.php';
	}

	/**
	 * Loop through the possible CSS Rules to check if there's post_meta for it
	 */
	public function smart_overlay_get_set_styles(){

		foreach( $this->modal_style_properties as $modal_style_property_meta_key => $modal_style_property ){
			$property_meta = get_post_meta( $this->smart_overlay_config->current_id, $this->smart_overlay_config->prefix . $modal_style_property_meta_key , true );

			// If there is meta, i.e., the user set a css property, add it to an array
			if( !empty( $property_meta ) ) {
				$this->modal_set_style_properties[ $modal_style_property ] = $property_meta;
			}
		}
	}

	/**
	 * Assemble a string of CSS rules for use inside of a style tag
	 *
	 */
	public function smart_overlay_assemble_styles( ) {
		$this->modal_outer_style .= "\t.smart-overlay .smart-overlay-content{" . PHP_EOL;

		foreach( $this->modal_set_style_properties as $style_property => $style_value ){
			//Is this a single input CSS value or a dual input (one where you supply the units) ?
			if(  is_array( $style_value ) ) {
				// Make the value for dimensions isn't 0
				if( ! empty( $style_value['dimension_value'] ) ) {
					$this->modal_outer_style .= "\t\t" . $style_property . ':' . $style_value['dimension_value'] . $style_value['dimension_units'] . ';' . PHP_EOL;
				}
			}else{
				$this->modal_outer_style .= "\t\t" . $style_property . ':' . $style_value . 'px;' . "\n";
			}
		}

		$this->modal_outer_style .= "\t}";
	}



	/**
	 * Admin notice to explain collisions if there's more than one overlay.
	 */
	public function smart_overlay_admin_notices() {

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
}
