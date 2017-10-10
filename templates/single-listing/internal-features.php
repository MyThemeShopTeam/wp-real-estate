<?php
/**
 * Single listing internal features
 *
 * This template can be overridden by copying it to yourtheme/listings/single-listing/internal-features.php.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$features = wre_meta( 'internal_features' );

if( empty( $features ) )
	return;
?>
<div class="int-features">
	<h3><?php esc_html_e( 'Internal Features', 'wp-real-estate' ); ?></h3>

	<ul>
		<?php foreach ( $features as $index => $feature ) {  ?>
			<li class="<?php echo esc_attr( strtolower( str_replace( ' ', '-', $feature ) ) ); ?>">
				<?php echo wre_tick(); ?> <?php echo esc_html( $feature ); ?>
			</li>
		<?php } ?>
	</ul>
</div>