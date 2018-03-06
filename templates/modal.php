<?php
/*
 * Template for displaying the modal markup
 */

if ( ! defined( 'WPINC' ) ) {
	die( 'Direct access not allowed' );
}
?>



<?php // Load up our CSS - we inline CSS because why should plugins waste HTTP connections? ?>
<style id="smart-overlay-inline-css">
<?php require_once dirname( __FILE__ ) . '/../assets/smart-overlay.css'; ?>
</style>

<div id="smart-overlay-content" style="display: none !important">
	<div id="smart-overlay-inner" <?php echo wp_kses( $inner_style, array( 'style' ) ); ?> >
		<?php echo $content; ?>
	</div>
</div>