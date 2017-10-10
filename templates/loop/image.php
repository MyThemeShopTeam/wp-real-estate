<?php
/**
 * Loop single image
 *
 * This template can be overridden by copying it to yourtheme/listings/loop/image.php.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$image 	= wre_get_first_image();
$status = wre_get_status();
?>

<div class="image">
	<a href="<?php esc_url( the_permalink() ); ?>" title="<?php esc_attr( the_title() ); ?>">

		<?php if( $status ) { ?>
				<span class="status <?php echo esc_attr( strtolower( str_replace( ' ', '-', $status['status']) ) ); ?>">
					<i class="wre-icon-house"></i>
					<?php echo esc_html( $status['status'] ); ?>
				</span>
		<?php } ?>

		<img alt="<?php echo esc_attr( $image['alt'] ); ?>" src="<?php echo esc_url( $image['sml'] ); ?>" />
	</a>
</div>