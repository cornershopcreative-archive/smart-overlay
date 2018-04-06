<?php
/**
 * Configures the meta fields for the overlay options
 * PHP Version 5
 *
 * @since   0.8.2
 * @package Smart_Overlay
 * @author  Cornershop Creative <devs@cshp.co>
 */

if ( ! defined( 'WPINC' ) ) {
	die( 'Direct access not allowed' );
}

class Smart_Overlay_Custom_Fields {

	/**
	 * All of the values for dimension units.
	 *
	 * @var array
	 */
	private $unit_values = [ 'px', '%' ];

	/**
	 * Initialize hooks
	 */
	public function init() {
		add_action( 'cmb2_render_single_dimension_and_unit', array( $this, 'cmb2_render_single_dimension_and_unit_cb' ), 10, 5 );
		add_filter( 'cmb2_sanitize_single_dimension_and_unit', array( $this, 'cmb2_sanitize_single_dimension_and_unit_cb' ), 10, 5 );
		add_filter( 'cmb2_after_form', array( $this, 'cmb2_after_form_do_js_validation' ), 10, 2 );
	}

	/**
	 * Creates a new field for displaying a number field next to select field. Used for specifying dimension and unit (1000px, 100rem, etc)
	 *
	 * @param string $field        The current CMB2_Field object.
	 * @param string $value        The value of this field passed through the escaping filter. It defaults to sanitize_text_field.
	 * @param int    $object_id       The id of the object you are working with. Most commonly, the post id.
	 * @param string $object_type  The type of object you are working with. Most commonly, post.
	 * @param object $field_type   This is an instance of the CMB2_Types object and gives you access to all of the methods that CMB2 uses to build its field types.
	 */
	public function cmb2_render_single_dimension_and_unit_cb( $field, $value, $object_id, $object_type, $field_type ) {

		$value = wp_parse_args(
			$value, array(
				'dimension_value' => '',
				'dimension_units' => '',
			)
		);
	?>
		<div class="alignleft"><p><label for="<?php echo $field_type->_id( 'dimension_value' ); // WPCS: XSS ok; ?>">Value</label></p>
			<?php
			echo $field_type->input(
				array(
					'name'  => $field_type->_name( '[dimension_value]' ), // WPCS: XSS ok;
					'id'    => $field_type->_id( '_dimension_value' ), // WPCS: XSS ok;
					'value' => $value['dimension_value'], // WPCS: XSS ok;
					'desc'  => '',
				)
			); // WPCS: XSS ok;
			?>
		</div>
		<div class="alignleft"><p><label for="<?php echo $field_type->_id( 'dimension_units' ); // WPCS: XSS ok; ?>">Units</label></p>
			<?php
			echo $field_type->select(
				array(
					'name'  => $field_type->_name( '[dimension_units]' ), // WPCS: XSS ok;
					'id'    => $field_type->_id( '_dimension_units' ), // WPCS: XSS ok;
					'value' => $value['dimension_units'], // WPCS: XSS ok;
					'options' => $this->cmb2_unit_options( $value['dimension_units'] ), // WPCS: XSS ok;
					'desc'  => '',
				)
			); // WPCS: XSS ok;
			?>
		</div>
		<br class="clear">
		<?php echo $field_type->_desc( true ); // WPCS: XSS ok; ?>

	<?php
	}

	/**
	 * Set a options for a select field
	 *
	 * @param bool $value Select Option Item Value/Display Value.
	 * @return string
	 */
	private function cmb2_unit_options( $value = false ) {
		$options = '';

		foreach ( $this->unit_values as $option ) {
			$options .= '<option value="' . $option . '" ' . selected( $value, $option, false ) . '>' . $option . '</option>';
		}
		return $options;
	}

	/**
	 * Sanitize the dimension and unit field
	 *
	 * @param string $override_value Sanitization override value to return. It is passed in as null, and is what we will modify to short-circuit CMB2's saving mechanism.
	 * @param string $value The actual field value.
	 * @param int    $object_id The id of the object you are working with. Most commonly, the post id.

	 * @param array  $field_args The field arguments.
	 * @param object $sanitizer_object This is an instance of the CMB2_Sanitize object and gives you access to all of the methods that CMB2 uses to sanitize its field values.
	 *
	 * @return mixed
	 */
	public function cmb2_sanitize_single_dimension_and_unit_cb( $override_value, $value, $object_id, $field_args, $sanitizer_object ) {
		// If no value was entered, set the units blank and bail.
		if ( empty( $value['dimension_value'] ) ) {
			$value['dimension_units'] = '';
			return $value;
		}
		$value['dimension_value'] = abs( $value['dimension_value'] );

		// If an unrecognized Unit comes through, set it as pixels
		if ( ! in_array( $value['dimension_units'], $this->unit_values, true ) ) {
			$value['dimension_units'] = 'px';
		}

		return $value;
	}

	/**
	 * Javascript validation to prevent a max height smaller than the min height from submitting
	 *
	 * @param array $cmb_id The current box ID.
	 * @param int   $obj_id The ID of the current object.
	 * @link https://github.com/CMB2/CMB2-Snippet-Library/blob/master/javascript/cmb2-js-validation-required.php
	 */
	public function cmb2_after_form_do_js_validation( $cmb_id, $obj_id ) {
		static $added = false;
		// Only add this to the page once (not for every metabox)
		if ( $added ) {
			return;
		}
		$added = true;
	?>
		<script type="text/javascript">
			jQuery(document).ready(function($){
				$form = $( document.getElementById( 'post' ) );
				$htmlbody = $( 'html, body' );


				function checkValidation( evt ){
					//Get the min, max and unit values
					var max_height = $('#smart_overlay_max_height_dimension_value').val();
					var min_height = $('#smart_overlay_min_height_dimension_value').val();
					var max_height_units = $('#smart_overlay_max_height_dimension_units').val();
					var min_height_units = $('#smart_overlay_min_height_dimension_units').val();

					// If any of the values are blank, no need to continue
					if( max_height === '' || min_height === '' ){
						return;
					}

					// Used for error checking
					var $first_error_row = null;

					// The outermost div of max/min input
					var $row = null;

					// Mark the field red and set the error flag
					function add_required( $row ) {
						$row.css({ 'background-color': 'rgb(255, 170, 170)' });
						$first_error_row = $first_error_row ? $first_error_row : $row;
					}

					// Unmark the field red, no need to unmark the error flag because its defined as null on every click
					function remove_required( $row ) {
						$row.css({ background: '' });
					}

					//Check if the max height is less than min height and if the units are the same
					if( Number( max_height ) < Number( min_height ) ) {
						add_required( $('.cmb2-id-smart-overlay-max-height') );
						add_required( $('.cmb2-id-smart-overlay-min-height') );
					}else{
						remove_required( $('.cmb2-id-smart-overlay-max-height') );
						remove_required( $('.cmb2-id-smart-overlay-min-height') );
					}

					// Check for errors
					if ( $first_error_row ) {
						evt.preventDefault();
						alert( '<?php _e( 'The max height cannot be less than the minimum height.', 'smart_overlay' ); // PHPCS: XSS ok. ?> ');
						$htmlbody.animate({
							scrollTop: ( $first_error_row.offset().top - 200 )
						}, 1000);
					}
				}
				$form.on( 'submit', checkValidation );
			});
		</script>
	<?php
	}

}
