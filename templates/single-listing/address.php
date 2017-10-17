<?php
/**
 * Single listing address
 *
 * This template can be overridden by copying it to yourtheme/listings/single-listing/address.php.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$address = wre_meta( 'displayed_address' );
$lat = wre_meta('lat');
$lng = wre_meta('lng');
if( empty( $address ) )
	return;

?>

<div class="address" itemprop="address" itemscope="" itemtype="http://schema.org/PostalAddress">
	<span itemprop="streetAddress"><?php echo esc_html( $address ); ?></span>
	<?php echo $lat; ?>
</div>
<?php if( $lat && $lng ) { ?>
	<span itemprop="geo" itemscope="" itemtype="http://schema.org/GeoCoordinates">
		<meta itemprop="latitude" content="<?php echo esc_attr($lat); ?>">
		<meta itemprop="longitude" content="<?php echo esc_attr($lng); ?>">
	</span>
<?php }
