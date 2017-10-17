<?php
/**
 * Ordering
 *
 * This template can be overridden by copying it to yourtheme/listings/loop/orderby.php.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$contextual_query = wre_get_contextual_query();
if ( 1 === $contextual_query->found_posts ) {
	return;
}

$orderby = isset( $_GET['wre-orderby'] ) ? $_GET['wre-orderby'] : 'date';
$orderby_options = apply_filters( 'wre_listings_orderby', array(
	'date'			=> __( 'Newest Listings', 'wp-real-estate' ),
	'date-old'		=> __( 'Oldest Listings', 'wp-real-estate' ),
	'price'			=> __( 'Price (Low to High)', 'wp-real-estate' ),
	'price-high'	=> __( 'Price (High to Low)', 'wp-real-estate' )
) );
$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

?>
<form class="wre-ordering" method="get">

	<div class="wre-select-wrap">
		<select name="wre-orderby" class="listings-orderby">
			<?php foreach ( $orderby_options as $id => $name ) : ?>
				<option value="<?php echo esc_attr( $id ); ?>" <?php selected( $orderby, $id ); ?>><?php echo esc_html( $name ); ?></option>
			<?php endforeach; ?>
		</select>
	</div>
	<?php
	// Keep query string vars intact
	foreach ( $_GET as $key => $val ) {

		if ( 'wre-orderby' === $key || 'submit' === $key ) {
			continue;
		}
		if ( is_array( $val ) ) {
			foreach( $val as $innerVal ) {
				echo '<input type="hidden" name="' . esc_attr( $key ) . '[]" value="' . esc_attr( $innerVal ) . '" />';
			}
		} else {
			echo '<input type="hidden" name="' . esc_attr( $key ) . '" value="' . esc_attr( $val ) . '" />';
		}

	}
	?>
	<input type="hidden" name="paged" value="<?php echo esc_attr( $paged ); ?>" />
</form>