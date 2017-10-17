<?php
/**
 * Single listing sizes
 *
 * This template can be overridden by copying it to yourtheme/listings/single-listing/sizes.php.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$building_size	= wre_meta( 'building_size' );
$building_unit	= wre_meta( 'building_unit' );
$land_size		= wre_meta( 'land_size' );
$land_unit		= wre_meta( 'land_unit' );

if( empty( $building_size ) &&  empty( $land_size ) )
	return;
?>

<div class="sizes">
	<?php if( $land_size ) { ?>
	<div class="land">
		<?php echo esc_html( $land_size ); ?> <?php echo esc_html( $land_unit ); ?>
	</div>
	<?php } ?>
	<?php if( $building_size ) { ?>
	<div class="building">
		<?php echo esc_html( $building_size ); ?> <?php echo esc_html( $building_unit ); ?>
	</div>
	<?php } ?>
</div>