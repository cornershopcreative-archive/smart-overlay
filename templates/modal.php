<?php
/*
 * Template for displaying the modal markup
 */

if ( ! defined( 'WPINC' ) ) {
	die( 'Direct access not allowed' );
}
?>

<div id="smart-overlay-content" style="display: none !important;">
	<div id="smart-overlay-inner">
		<?php echo $content; ?>
	</div>
</div>
