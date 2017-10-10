<?php
/**
 * Loop price
 *
 * This template can be overridden by copying it to yourtheme/listings/loop/price.php.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$price = wre_meta( 'price' );
?>

<div class="price"><?php echo wre_price( $price ); ?></div>