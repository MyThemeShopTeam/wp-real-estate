<?php
/**
 * Single listing at a glance
 *
 * This template can be overridden by copying it to yourtheme/listings/single-listing/at-a-glance.php.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$bedrooms = wre_meta( 'bedrooms' );
$bathrooms = wre_meta( 'bathrooms' );
$cars = wre_meta( 'car_spaces' );

if( empty( $bedrooms ) &&  empty( $bathrooms ) &&  empty( $cars ) )
	return;
?>

<div class="glance">

	<?php if( $bedrooms ) { ?>
		<div class="beds">
			<span class="count" itemprop="numberOfRooms"><?php echo esc_html( $bedrooms ); ?></span>
			<i class="wre-icon-bed-1"></i>
		</div>
	<?php } ?>

	<?php if( $bathrooms ) { ?>
		<div class="baths">
			<span class="count"><?php echo esc_html( $bathrooms ); ?></span>
			<i class="wre-icon-bath-1"></i>
		</div>
	<?php } ?>

	<?php if( $cars ) { ?>
		<div class="cars">
			<span class="count"><?php echo esc_html( $cars ); ?></span>
			<i class="wre-icon-car-1"></i>
		</div>
	<?php } ?>

</div>