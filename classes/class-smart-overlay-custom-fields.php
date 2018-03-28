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
	 * Initialize hooks
	 */
	public function init() {
		add_action( 'cmb2_render_single_dimension_and_unit', array( $this, 'cmb2_render_single_dimension_and_unit_cb' ), 10, 5 );
		add_filter( 'cmb2_sanitize_single_dimension_and_unit', array( $this, 'cmb2_sanitize_single_dimension_and_unit_cb' ), 10, 5 );
	}

	/**
	 * Creates a new field for displaying a number field next to select field. Used for specifying dimension and unit (1000px, 100rem, etc)
	 *
	 * @param $field
	 * @param $value
	 * @param $object_id
	 * @param $object_type
	 * @param $field_type
	 */
	public function cmb2_render_single_dimension_and_unit_cb( $field, $value, $object_id, $object_type, $field_type ){
		$value = wp_parse_args( $value, array(
			'dimension_value' => '',
			'dimension_units' => '',
		));
	?>
		<div class="alignleft"><p><label for="<?php echo $field_type->_id('dimension_value')?>">Value</label></p>
			<?php echo $field_type->input( array(
				'name'  => $field_type->_name( '[dimension_value]' ),
				'id'    => $field_type->_id( '_dimension_value' ),
				'value' => $value['dimension_value'],
				'desc'  => '',
			) ); ?>
		</div>
		<div class="alignleft"><p><label for="<?php echo $field_type->_id('dimension_units')?>">Units</label></p>
			<?php echo $field_type->select( array(
				'name'  => $field_type->_name( '[dimension_units]' ),
				'id'    => $field_type->_id( '_dimension_units' ),
				'value' => $value['dimension_units'],
				'options'=> '<option value="px">px</option><option value="em">em</option><option value="rem">rem</option><option value="percent">%</option>',
				'desc'  => '',
			) ); ?>
		</div>
		<br class="clear">
		<?php echo $field_type->_desc( true ); ?>

	<?php
	}

	public function cmb2_sanitize_single_dimension_and_unit_cb( $override_value, $value, $object_id, $field_args, $sanitizer_object ){

		$value['dimension_value'] = abs( $value['dimension_value'] );

		// If an unrecognized Unit comes through, set it as pixels
		if( ! in_array( $value['dimension_units'], ['px, em, rem, percent'], true ) ){
			$value['dimension_units'] = 'px';
		}

		return $value;
	}


}