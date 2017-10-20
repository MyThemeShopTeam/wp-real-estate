<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

class WRE_Maps_Shortcodes {

	public function __construct() {

		add_filter('wp', array($this, 'has_shortcode'));
		add_shortcode('wre_map', array($this, 'wre_map'));
		add_action( 'wp_enqueue_scripts', array($this, 'wre_enqueue_map_scripts') );
	}
	
	public function wre_enqueue_map_scripts() {
		/*
		 * Google map scripts
		 */
		$key = wre_map_key();
		$api_url = wre_google_maps_url();
		if (!empty($key)) {
			
			$url = WRE_PLUGIN_URL;
			$ver = WRE_VERSION;

			$css_dir = 'assets/css/';
			$js_dir = 'assets/js/';
			
			wp_enqueue_script('wre-google-maps', $api_url);
			wp_enqueue_script('wre-geocomplete', $url . 'includes/admin/assets/js/jquery.geocomplete.min.js', array(), $ver, true);

			wp_enqueue_script('wre-gm-markers-js', $url . $js_dir . 'wre-gm-markers.js', array(), $ver, true);
			wp_enqueue_style('wre-gm-markers', $url . $css_dir . 'wre-google-map.css', array(), $ver, 'all');
		}
	}

	/**
	 * Check if we have the shortcode displayed
	 */
	public function has_shortcode() {
		global $post;
		if (is_a($post, 'WP_Post') && has_shortcode($post->post_content, 'wre_map')) {
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
	public function wre_map($atts) {

		$meta_query = array();
		$tax_query = array();
		$atts = shortcode_atts(array(
			'number' => -1,
			'include' => '', // comma separated
			'exclude' => '', // comma separated
			'height' => '400',
			'type' => '', // Custom types
			'purpose' => '', // Rent/Sell
			'status' => '', // Custom statuses
			'agent' => '', // id of the agent
			'relation' => 'AND', // relation between the type, purpose, status and agent
			// JS specific otpions
			'fit' => 'true', // true/false fit to bounds
			'zoom' => '14', // int only applicable if fit is set to false
			'center' => '35.652832, 139.839478', // lat/lng only applicable if fit is set to false
			'search' => 'true', // true/false show search box
			'search_zoom' => '12' // int only applicable if search is set to true
				), $atts);

		$key = wre_map_key();
		if (!$key)
			return false;

		$listings_data = array();
		$atts['center'] = array_map('trim', explode(',', $atts['center']));
		$listings_data['map_settings'] = array(
			'fit' => $atts['fit'],
			'zoom' => $atts['zoom'],
			'center' => $atts['center'],
			'search' => $atts['search'],
			'search_zoom' => $atts['search_zoom'],
		);

		// start the query
		$query_args = array(
			'post_type' => 'listing',
			'post_status' => 'publish',
			'ignore_sticky_posts' => 1,
			'posts_per_page' => $atts['number'],
		);

		// include only these listings
		if (!empty($atts['include'])) {
			$query_args['post__in'] = array_map('trim', explode(',', $atts['include']));
		}
		// exclude these listings
		if (!empty($atts['exclude'])) {
			$query_args['post__not_in'] = array_map('trim', explode(',', $atts['exclude']));
		}

		// do our meta queries
		if (!empty($atts['type']) || !empty($atts['purpose']) || !empty($atts['status']) || !empty($atts['agent'])) {

			$tax_query = $this->type_query($atts, $tax_query);
			$meta_query = $this->purpose_query($atts, $meta_query);
			$meta_query = $this->status_query($atts, $meta_query);
			$meta_query = $this->agent_query($atts, $meta_query);

			if ($meta_query > 1) {
				$meta_query['relation'] = $atts['relation'];
			}

			$query_args['meta_query'] = $meta_query;
			if($tax_query > 1) {
				$query_args['tax_query'] = $tax_query;
			}
		}
		$listings = new WP_Query(apply_filters('wre_maps_query', $query_args, $atts));

		if ($listings->have_posts()) :

			while ($listings->have_posts()) : $listings->the_post();

				$listing_id = get_the_ID();
				$lat = wre_meta('lat');
				$lng = wre_meta('lng');
				$listing_types = wp_get_post_terms($listing_id, 'listing-type', array('fields' => 'ids'));
				$marker_image = '';
				if (!empty($listing_types)) {
					$marker_image = get_term_meta($listing_types[0], '_wre_marker_image', true);
				}

				if ($lat && $lng) {
					$content = wp_trim_words(esc_html(wre_meta('content')), 20, '...');
					$content = preg_replace("/[^ \w]+/", "", $content);
					$listings_data['listings'][] = apply_filters('wre_maps_listing_data', array(
						'title' => get_the_title(),
						'permalink' => get_the_permalink(),
						'lat' => $lat,
						'lng' => $lng,
						'price' => wre_price(wre_meta('price')),
						'content' => $content,
						'thumbnail' => wre_get_first_image(),
						'icon' => $marker_image
					));
				}

			endwhile;

			
			$map = $this->output_the_map($atts, $listings_data);
		else :
			$map = '<p>'._e( 'Sorry, no listings were found.', 'wp-real-estate' ).'</p>';
		endif;
		wp_reset_postdata();

		return $map;
	}

	/**
	 * Display a listing map.
	 *
	 * @param array $atts
	 * @return string
	 */
	public function output_the_map($atts, $listings_data) {

		$output = '';
		ob_start();
		?>
		<div class="wre-map-wrapper">
		<?php if ($atts['search'] == 'true') { ?>
				<div class="search-panel form-group">
					<input class="form-control search-input" id="wre-map-address" type="text" value="" placeholder="<?php _e('City, Street, Landmark...', 'listings-wp-maps'); ?>" />
					<input class="form-control button btn"  id="wre-map-submit" type="submit" value="" />
				</div>
		<?php } ?>

			<ul class="map-controls list-unstyled">
				<li><a href="#" class="control zoom-in" id="wre-zoom-in">&#x254B;</a></li>
				<li><a href="#" class="control zoom-out" id="wre-zoom-out">&#9472;</a></li>
				<li><a href="#" class="control map-type" id="wre-map-type">
						&#x26F6;
						<ul class="list-unstyled">
							<li id="wre-map-type-roadmap" class="map-type"><?php _e('Roadmap', 'wp-real-estate'); ?></li>
							<li id="wre-map-type-satellite" class="map-type"><?php _e('Satellite', 'wp-real-estate'); ?></li>
							<li id="wre-map-type-hybrid" class="map-type"><?php _e('Hybrid', 'wp-real-estate'); ?></li>
							<li id="wre-map-type-terrain" class="map-type"><?php _e('Terrain', 'wp-real-estate'); ?></li>
						</ul>
					</a></li>
				<li><a href="#" id="wre-current-location" class="control"><?php _e('My Location', 'wp-real-estate'); ?></a></li>
			</ul>

			<div id="wre-advanced-map" class="wre-google-map" data-listings-data='<?php echo json_encode($listings_data, true); ?>' style="height: <?php echo (int) $atts['height']; ?>px">
				<div class="wre-loader-container">
					<div class="svg-loader"></div>
				</div>
			</div>
		</div>
		<?php
		$output = ob_get_contents();
		ob_end_clean();

		return apply_filters('wre_maps_output_map', $output);
	}

	/**
	 * Add to the meta query
	 *
	 */
	public function type_query($atts, $tax_query) {
		// show only a certain type(s)
		if (!empty($atts['type'])) {
			$type_array = array(
				'taxonomy'	=> 'listing-type',
				'field'		=> 'term_id',
				'terms'		=> $atts['type']
			);

			array_push($tax_query, $type_array);
		}
		return $tax_query;
	}

	/**
	 * Add to the meta query
	 *
	 */
	public function status_query($atts, $meta_query) {
		// show only a certain status(s)
		if (!empty($atts['status'])) {
				$status_array[] = array(
					'key' => '_wre_listing_status',
					'value' => $atts['status'],
					'compare' => '='
				);

			array_push($meta_query, $status_array);
		}
		return $meta_query;
	}

	/**
	 * Add to the meta query
	 *
	 */
	public function purpose_query($atts, $meta_query) {
		// show only a certain purpose(s)
		if (!empty($atts['purpose'])) {
				$purpose_array[] = array(
					'key' => '_wre_listing_purpose',
					'value' => $atts['purpose'],
					'compare' => '='
				);

			array_push($meta_query, $purpose_array);
		}
		return $meta_query;
	}

	/**
	 * Add to the meta query
	 *
	 */
	public function agent_query($atts, $meta_query) {
		// show only a certain agent(s) listings
		if (!empty($atts['agent'])) {
			$agent_array = array();
			$agents = array_map( 'trim', explode( ',', $atts['agent'] ) );
			$agent_array[] = array(
				'key' => '_wre_listing_agent',
				'value' => $agents,
				'compare' => 'IN'
			);

			array_push($meta_query, $agent_array);
		}
		return $meta_query;
	}

}

return new WRE_Maps_Shortcodes();