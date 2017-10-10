<?php
/**
 * Single listing contact-form
 *
 * This template can be overridden by copying it to yourtheme/listings/single-listing/contact-form.php.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>
<h2 class="widget-title"><?php echo __( 'Quick Contact', 'wp-real-estate' ); ?></h2>
<div class="wre-contact-form" id="wre-contact">
	<div class="message-wrapper"></div>
	<?php echo do_shortcode( '[wre_contact_form]' ); ?>
</div>