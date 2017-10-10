<?php
/**
 * Loop comparison
 *
 * This template can be overridden by copying it to yourtheme/listings/loop/comparison.php.
 */
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

$listing_id = wre_get_ID();
$wrapper_class = '';
$anchor_class = '';
$listings = wre_get_comparison_data();
if (!empty($listings) && in_array($listing_id, $listings)) {
	$wrapper_class = 'in';
	$anchor_class = 'hide';
}
?>
<div class="compare-wrapper">
	<div class="compare-output <?php echo esc_attr( $wrapper_class ); ?>">
		<i class="wre-icon-compare"></i>
		<?php _e('Added to Compare', 'wp-real-estate'); ?>
	</div>

	<a href="#" class="add-to-compare <?php echo esc_attr( $anchor_class ); ?>" data-id="<?php echo esc_attr(wre_get_ID()); ?>">
		<i class="wre-icon-compare"></i>
		<?php _e('Add to Compare', 'wp-real-estate'); ?>
	</a>
</div>