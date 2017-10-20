<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

/**
 * All Stylesheets And Scripts
 * 
 * @return void
 */
function wre_enqueue_styles_scripts() {

	$url = WRE_PLUGIN_URL;
	$ver = WRE_VERSION;

	$css_dir = 'assets/css/';
	$js_dir = 'assets/js/';

	if ( is_wre() ) {


		wp_register_style('wp-real-estate-lightslider', $url . $css_dir . 'lightslider.css', array(), $ver, 'all');
		wp_register_script('wp-real-estate-lightslider', $url . $js_dir . 'lightslider.js', array('jquery'), $ver, true);

		if( is_singular( 'listing' ) ) {
			wp_enqueue_style('wp-real-estate-lightslider');
			wp_enqueue_script('wp-real-estate-lightslider');
		}

		wp_enqueue_script('wp-real-estate', $url . $js_dir . 'wp-real-estate.js', array('jquery'), $ver, true);

		/*
		 * Localize our script
		 */
		$localized_array = array();

		if (is_single_wre()) {
			$localized_array = array(
				'map_width' => wre_option('map_width'),
				'map_height' => wre_option('map_height'),
				'map_zoom' => wre_option('map_zoom'),
				'lat' => wre_meta('lat', wre_get_ID()),
				'lng' => wre_meta('lng', wre_get_ID()),
				'address' => wre_meta('displayed_address', wre_get_ID())
			);

			$slider_localize_data = array(
				'gallery_mode' => 'slide',
				'auto_slide' => true,
				'slide_delay' => 5000,
				'slide_duration' => 1500,
				'thumbs_shown' => 6,
				'gallery_prev' => '<i class="prev wre-icon-arrow-2"></i>',
				'gallery_next' => '<i class="next wre-icon-arrow-2"></i>',
			);
			wp_localize_script('wp-real-estate-lightslider', 'wre_slider', apply_filters('wre_localized_script', $slider_localize_data));
		}
		$localized_array['ajax_url'] = admin_url('admin-ajax.php');

		wp_localize_script('wp-real-estate', 'wre', apply_filters('wre_localized_script', $localized_array));
		wp_enqueue_style('wp-real-estate', $url . $css_dir . 'wp-real-estate.css', array(), $ver, 'all');
		if (is_rtl()) {
			wp_enqueue_style('wp-real-estate-rtl', $url . $css_dir . 'wp-real-estate-rtl.css', array(), $ver, 'all');
		}
	}
}

add_action('wp_enqueue_scripts', 'wre_enqueue_styles_scripts', 10);