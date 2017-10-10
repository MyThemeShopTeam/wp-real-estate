<?php
/**
 * Single listing gallery
 *
 * This template can be overridden by copying it to yourtheme/listings/single-listing/fallery.php.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$gallery = wre_meta( 'image_gallery' );

if( empty( $gallery ) )
	return;
?>

<ul id="image-gallery">

	<?php
	foreach ( $gallery as $id => $img_url ) {

		$img = get_post( $id );
		$lge = wp_get_attachment_image_url( $id, 'wre-lge' );
		$sml = wp_get_attachment_image_url( $id, 'wre-sml' );

	?>
		<li data-thumb="<?php echo esc_url( $sml ); ?>" data-src="<?php echo esc_url( $lge ); ?>">
			<img src="<?php echo esc_url( $lge ); ?>" />
		</li>

	<?php } ?>

</ul>