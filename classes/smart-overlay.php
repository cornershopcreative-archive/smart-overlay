<?php
//namespace smartoverlay;

if ( ! defined( 'WPINC' ) ) {
	die( 'Direct access not allowed' );
}

class Smart_Overlay{

	private $smart_overlay_config;

	public function __construct() {
		$this->set_smart_overlay_variables();
		$this->dependencies();
	}

	public function on_loaded(){
		add_action( 'init', array( $this, 'smart_overlay_post_type' ), 10 );
		add_action( 'manage_smart_overlay_posts_custom_column' , array( $this, 'smart_overlay_custom_columns' ), 10, 2 );
		add_filter( 'manage_smart_overlay_posts_columns' , array( $this, 'smart_overlay_add_columns' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'smart_overlay_js' ) );
		add_action( 'init', array( $this, 'set_smart_overlay_variables' ) );
		add_action( 'wp_head', array( $this, 'smart_overlay_js_config' ) );
		add_action( 'wp_footer', array( $this, 'smart_overlay_footer' ) );
		add_action( 'admin_notices', array( $this, 'smart_overlay_admin_notices' ) );
	}

	//load dependencies
	public function dependencies(){

		if ( file_exists( dirname( __FILE__ ) . '/cmb2/init.php' ) ) {
			require_once dirname( __FILE__ ) . '/cmb2/init.php';
		} elseif ( file_exists( dirname( __FILE__ ) . '/CMB2/init.php' ) ) {
			require_once dirname( __FILE__ ) . '/CMB2/init.php';
		}

		if ( file_exists( dirname( __FILE__ ) . '/CMB2-conditionals/cmb2-conditionals.php' ) ) {
			require_once dirname( __FILE__ ) . '/CMB2-conditionals/cmb2-conditionals.php';
		}

		// include CMB2 for custom metaboxes
		require_once dirname( __FILE__ ) . '/fields.php';


		//LEFT OFF HERE

		$fields = new CMB2_fields();
		$fields->on_loaded();
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

		$this->smart_overlay_config;

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
	public function smart_overlay_js() {

		if ( ! is_admin() ) {
			wp_enqueue_script(
				'smart-overlay-js',
				plugins_url( '/../assets/smart-overlay.js', __FILE__ ),
				array( 'jquery' ),
				SMART_OVERLAY_VERSION,
				true
			);
		}

	}



	/**
	 * Built the config object.
	 */
	public function set_smart_overlay_variables() {

		$this->smart_overlay_config = new stdClass();
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

		return $this->smart_overlay_config->overlays = new WP_Query( $query_args );
	}




	/**
	 * Based on the current page/thing being displayed, and the overlays available, output into the page <head>
	 * a JS object with the appropriate overlay settings.
	 */
	public function smart_overlay_js_config() {

		// Obviously we can only do this if there are some overlay posts defined...
		if ( $this->smart_overlay_config->overlays->have_posts() ) :
			while ( $this->smart_overlay_config->overlays->have_posts() ) :

				$this->smart_overlay_config->overlays->the_post();

				// Get the meta values from the current overlay.
				$smart_overlay_id  = get_the_ID();
				$display_filter    = get_post_meta( $smart_overlay_id, $this->smart_overlay_config->prefix . 'display_lightbox_on' )[0];
				$disable_on_mobile = get_post_meta( $smart_overlay_id, $this->smart_overlay_config->prefix . 'disable_on_mobile', 1 );

				// Prepare our config object
				$config = array(
					'background' => get_post_meta( $smart_overlay_id, $this->smart_overlay_config->prefix . 'bg_image' )[0],
					'context'    => $display_filter,
					'suppress'   => get_post_meta( $smart_overlay_id, $this->smart_overlay_config->prefix . 'suppress' )[0],
					'trigger'    => get_post_meta( $smart_overlay_id, $this->smart_overlay_config->prefix . 'trigger' )[0],
					'amount'     => get_post_meta( $smart_overlay_id, $this->smart_overlay_config->prefix . 'trigger_amount' )[0],
					'maxWidth'   => get_post_meta( $smart_overlay_id, $this->smart_overlay_config->prefix . 'max_width' )[0],
					'id'         => sanitize_title_with_dashes( get_post_meta( $smart_overlay_id, $this->smart_overlay_config->prefix . 'overlay_identifier' )[0] ),
					'onMobile'   => ! $disable_on_mobile,
				);

				$script_tag = '<script id="smart-overlay-options">window.smart_overlay_opts = ' . wp_json_encode( $config ) . ';</script>';

				if (
					'all' === $display_filter
					|| ( is_front_page() && 'home' === $display_filter )
					|| ( ! is_front_page() && 'all_but_homepage' === $display_filter )
				)	{

					echo $script_tag;

					$this->smart_overlay_config->current_id = $smart_overlay_id;

					// Once we get a single smart overlay, we can stop.
					break;

				}//end if

			endwhile;
		endif;

		wp_reset_postdata();
		wp_reset_query();
	}



	/**
	 * Ouput the actual overlays contents chosen for this page into the footer.
	 */
	public function smart_overlay_footer() {

		$this->smart_overlay_config;

		// Don't do anything if there's no overlay on this page.
		if ( ! $this->smart_overlay_config->current_id ) {
			return;
		}

		// Load up our CSS - we inline CSS because why shoud plugins waste HTTP connections?
		echo '<style id="smart-overlay-inline-css">';
		require_once dirname( __FILE__ ) . '/../assets/smart-overlay.css';
		echo '</style>';

		$display_filter = get_post_meta( $this->smart_overlay_config->current_id, $this->smart_overlay_config->prefix . 'display_lightbox_on' )[0];
		$max_width      = get_post_meta( $this->smart_overlay_config->current_id, $this->smart_overlay_config->prefix . 'max_width' )[0];
		$content        = apply_filters( 'the_content', get_post_field( 'post_content', $this->smart_overlay_config->current_id ) );

		// Output our lightbox content
		$inner_style = ( $max_width ) ? ' style="max-width:' . $max_width . 'px;"' : '';
		echo '<div id="smart-overlay-content" style="display: none !important"><div id="smart-overlay-inner" ' . wp_kses( $inner_style, array( 'style' ) ) . '>';
		echo $content;
		echo '</div></div>';

	}



	/**
	 * Admin notice to explain collisions if there's more than one overlay.
	 */
	public function smart_overlay_admin_notices() {

		$overlay_count = wp_count_posts( 'smart_overlay' );
		$current_screen = get_current_screen();

		if ( 'edit-smart_overlay' == $current_screen->id && $overlay_count->publish > 1 ) :
			?>
			<div class="notice notice-warning notice-alt">
				<p><?php _e( 'Note: If more than one overlay is eligible to appear on a given page, only the most recent will be shown to visitors.', 'smart_overlay' ); ?></p>
			</div>
			<?php
		endif;
	}
}











