<?php
/**
 * Single listing price
 *
 * This template can be overridden by copying it to yourtheme/listings/single-listing/price.php.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$price = wre_meta( 'price' );
?>
<h4 class="price"><?php echo wre_price( $price ); ?></h4>