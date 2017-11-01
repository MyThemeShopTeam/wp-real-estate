<?php
/**
 * Single listing MLS number
 *
 * This template can be overridden by copying it to yourtheme/listings/single-listing/mls.php.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$mls_number = wre_meta( 'mls' );

if( empty( $mls_number ) )
	return;
?>
<div class="mls-wrapper">
	<?php echo _e( 'Mls#', 'wp-real-estate' ).' '.esc_html( $mls_number ) ?>
</div>