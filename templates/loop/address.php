<?php
/**
 * loop address
 *
 * This template can be overridden by copying it to yourtheme/listings/loop/address.php.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$address = wre_meta( 'displayed_address' );
if( empty( $address ) )
	return;
?>

<div class="address"><?php echo esc_html( $address ); ?></div>