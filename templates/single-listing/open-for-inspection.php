<?php
/**
 * Single listing open_for_inspection
 *
 * This template can be overridden by copying it to yourtheme/listings/single-listing/open-for-inspection.php.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$open = wre_meta( 'open' );

if( empty( $open ) )
	return;

?>
<div class="open-times">

	<h4><?php esc_html_e( 'Open For Inspection', 'wp-real-estate' ); ?></h4>

	<ul>

		<?php foreach ( $open as $index => $times ) {  ?>
				<li class="date-<?php echo esc_attr( stripslashes( strtolower( str_replace( ' ', '-', $times['day'] ) ) ) ); ?>">
					<?php echo wre_format_date( $times['day'] ); ?> <?php echo esc_html( $times['start'] ); ?> - <?php echo esc_html( $times['end'] ); ?>
				</li>
		<?php }  ?>

	</ul>

</div>