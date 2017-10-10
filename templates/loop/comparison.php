<?php
/**
 * View Switcher
 *
 * This template can be overridden by copying it to yourtheme/listings/loop/view-switcher.php.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$class = '';
$listings = wre_get_comparison_data();
if( !empty($listings) ) {
	$class = 'in';
}
?>
<div class="wre-compare-listings <?php echo esc_attr( $class ); ?>">

	<h4><?php _e('Compare Listings', 'wp-real-estate'); ?></h4>
	<ul class="wre-compare-lists">
		<?php
			if( !empty($listings) ) {
				foreach( $listings as $listing ) {
					echo wre_get_comparison_content( $listing );
				}
			}
		?>
	</ul>
	<a href="<?php echo esc_url( get_permalink( wre_option( 'compare_listings' ) ) ); ?>" class="compare-lists-btn"><?php _e('Compare', 'wp-real-estate'); ?></a>
	<p class="wre-compare-error"></p>
</div>