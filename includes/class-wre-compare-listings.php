<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

class WRE_Compare_Listings_Shortcodes {

	public function __construct() {

		add_filter('wp', array($this, 'has_shortcode'));
		add_shortcode('wre_compare_listings', array($this, 'wre_compare_listings'));
	}

	/**
	 * Check if we have the shortcode displayed
	 */
	public function has_shortcode() {
		global $post;
		if (is_a($post, 'WP_Post') && has_shortcode($post->post_content, 'wre_compare_listings')) {
			add_filter('is_wre', array($this, 'is_wre'));
		}
	}

	/**
	 * Add this as a listings_wp page
	 *
	 * @param bool $return
	 * @return bool
	 */
	public function is_wre($return) {
		return true;
	}

	/**
	 * The shortcode
	 *
	 * @param array $atts
	 * @return string
	 */
	public function wre_compare_listings($atts) {

		ob_start();

		$listings = wre_get_comparison_data();
		$comparison_attributes = array('price', 'type', 'status', 'purpose', 'bedrooms', 'bathrooms', 'car_spaces', 'building_size', 'land_size');
		$option = get_option('wre_options');
		$internal_features = isset($option['internal_feature']) ? $option['internal_feature'] : '';
		$external_features = isset($option['external_feature']) ? $option['external_feature'] : '';
		?>

		<div class="wre-compare-wrapper">

			<?php if (!empty($listings)) { ?>

				<div class="listing-column listings-header">
					<div class="property-thumbnail wre-row"></div>
					<?php foreach ($comparison_attributes as $comparison_attribute) { ?>
						<div class="wre-row">
							<?php echo str_replace('_', ' ', $comparison_attribute); ?>
						</div>
					<?php } ?>

					<?php
					if ($internal_features) {
						foreach ($internal_features as $internal_feature) {
							?>

							<div class="wre-row">
								<?php echo $internal_feature; ?>
							</div>

							<?php
						}
					}

					if ($external_features) {
						foreach ($external_features as $external_feature) {
							?>

							<div class="wre-row">
								<?php echo $external_feature; ?>
							</div>

							<?php
						}
					}
					?>

				</div>

				<?php foreach ($listings as $listing) { ?>

					<div class="listing-column">
						<div class="property-thumbnail wre-row">
							<?php
							$image = wre_get_first_image($listing);
							$address = wre_meta('displayed_address', $listing);
							$listings_internal_features = wre_meta('internal_features', $listing);
							$listings_external_features = wre_meta('external_features', $listing);
							?>
							<p>
								<a href="<?php echo get_the_permalink( $listing ); ?>">
									<img src="<?php echo esc_url($image['sml']); ?>" />
								</a>
							</p>
							<p><?php esc_html_e($address); ?></p>
						</div>
						<?php foreach ($comparison_attributes as $comparison_attribute) { ?>
							<div class="wre-row">
								<?php
								$value = wre_meta($comparison_attribute, $listing);
								if ($value) {
									if ($comparison_attribute == 'price') {
										$suffix = wre_meta('price_suffix', $listing);
										$value = wre_format_price($value) . ' ' . $suffix;
									} else if ($comparison_attribute == 'building_size') {
										$value = esc_html($value) . ' ' . esc_html(wre_meta('building_unit', $listing));
									} else if ($comparison_attribute == 'land_size') {
										$value = esc_html($value) . ' ' . esc_html(wre_meta('land_unit', $listing));
									}
									echo $value;
								} else {
									echo '-';
								}
								?>
							</div>
						<?php } ?>

						<?php
						if ($internal_features) {
							foreach ($internal_features as $internal_feature) {
								$value = '-';
								if (!empty($listings_internal_features) && in_array($internal_feature, $listings_internal_features)) {
									$value = '<i class="wre-icon-tick-2"></i>';
								}
								?>
								<div class="wre-row features-list">
									<?php echo $value; ?>
								</div>
								<?php
							}
						}
						?>

						<?php
						if ($external_features) {
							foreach ($external_features as $external_feature) {
								$value = '-';
								if (!empty($listings_external_features) && in_array($external_feature, $listings_external_features)) {
									$value = '<i class="wre-icon-tick-2"></i>';
								}
								?>
								<div class="wre-row features-list">
									<?php echo $value; ?>
								</div>
								<?php
							}
						}
						?>

					</div>

				<?php } ?>

			<?php } else { ?>
				<p><?php _e( 'No Listings Found!', 'wp-real-estate' ); ?></p>
			<?php } ?>
		</div>

		<?php
		return ob_get_clean();
	}

}

return new WRE_Compare_Listings_Shortcodes();