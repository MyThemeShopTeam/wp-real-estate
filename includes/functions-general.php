<?php
if (!defined('ABSPATH')) {
	exit;
} // Exit if accessed directly.

/**
 * Get the id af any item (used only to localize address for shortcodes)
 */
function wre_get_ID() {
	$post_id = null;

	if (!$post_id)
		$post_id = wre_shortcode_att('id', 'wre_listing');

	if (!$post_id)
		$post_id = wre_shortcode_att('id', 'wre_agent');

	if (!$post_id)
		$post_id = get_the_ID();

	return $post_id;
}

/**
 * Get the meta af any item
 */
function wre_meta($meta, $post_id = 0) {
	if (!$post_id)
		$post_id = get_the_ID();

	$meta_key = '_wre_listing_' . $meta;
	if( $meta == 'content' ) {
		$meta_key = $meta;
	}
	
	if ($meta == 'type' || $meta == 'listing-type') {
		$data = wp_get_post_terms($post_id, 'listing-type', array("fields" => "slugs"));
		if (!empty($data))
			$data = $data[0];
	} else {
		$data = get_post_meta($post_id, $meta_key, true);
	}

	return $data;
}

/**
 * Get any option
 */
function wre_option($option) {
	$options = get_option('wre_options');
	$return = isset($options[$option]) ? $options[$option] : false;
	return $return;
}

/**
 * Return an attribute value from any shortcode
 */
function wre_shortcode_att($attribute, $shortcode) {

	global $post;

	if (!$attribute && !$shortcode)
		return;

	if (has_shortcode($post->post_content, $shortcode)) {
		$pattern = get_shortcode_regex();
		if (preg_match_all('/' . $pattern . '/s', $post->post_content, $matches) && array_key_exists(2, $matches) && in_array($shortcode, $matches[2])) {
			$key = array_search($shortcode, $matches[2], true);

			if ($matches[3][$key]) {
				$att = str_replace($attribute . '="', "", trim($matches[3][$key]));
				$att = str_replace('"', '', $att);

				if (isset($att)) {
					return $att;
				}
			}
		}
	}
}

/**
 * is_wre_admin - Returns true if on a listings page in the admin
 */
function is_wre_admin() {
	$post_type = get_post_type();
	$screen = get_current_screen();
	$return = false;

	if (in_array($post_type, array('listing', 'listing-enquiry'))) {
		$return = true;
	}

	if (in_array($screen->id, array('listing', 'edit-listing', 'listing-enquiry', 'edit-listing-enquiry', 'listing_page_wre_options', 'listing_page_wre-idx-listing'))) {
		$return = true;
	}

	return apply_filters('is_wre_admin', $return);
}

/**
 * is_wre - Returns true if a page uses wre templates
 */
function is_wre() {

	// include on agents page
	$is_agent = false;
	if (is_author()) {
		$user = new WP_User(wre_agent_ID());
		$user_roles = $user->roles;
		$listings_count = wre_agent_listings_count(wre_agent_ID());
		if (in_array('wre_agent', $user_roles) || $listings_count > 0) {
			$is_agent = true;
		}
	}

	$result = apply_filters('is_wre', ( is_wre_archive() || is_single_wre() || is_wre_search() || is_wre_active_widget() || $is_agent ) ? true : false );

	return $result;
}

/**
 * is_wre_archive - Returns true when viewing the listing type archive.
 */
if (!function_exists('is_wre_archive')) {

	function is_wre_archive() {
		return ( is_post_type_archive('listing') );
	}

}

/**
 * is_lisitng - Returns true when viewing a single listing.
 */
if (!function_exists('is_single_wre')) {

	function is_single_wre() {
		$result = false;
		if (is_singular('listing')) {
			$result = true;
		}

		return apply_filters('is_single_wre', $result);
	}

}
/**
 * is_lisitng - Returns true when viewing listings search results page
 */
if (!function_exists('is_wre_search')) {

	function is_wre_search() {
		if (!is_search())
			return false;
		$current_page = sanitize_post($GLOBALS['wp_the_query']->get_queried_object());
		if ($current_page)
			return $current_page->name == 'listing';

		return false;
	}

}

/**
 * is_wre_active_widget - Returns true when viewing wre widget.
 */
if (!function_exists('is_wre_active_widget')) {

	function is_wre_active_widget() {
		if (is_active_widget('', '', 'wre-agents') || is_active_widget('', '', 'wre-recent-listings') || is_active_widget('', '', 'wre-search-listings') || is_active_widget('', '', 'wre-nearby-listings')) {
			return true;
		}
		return false;
	}

}

add_action('init', 'wre_add_new_image_sizes', 11);

function wre_add_new_image_sizes() {
	add_theme_support('post-thumbnails');
	add_image_size('wre-lge', 1200, 900, array('center', 'center')); //main
	add_image_size('wre-sml', 400, 300, array('center', 'center')); //thumb
}

/*
 * Run date formatting through here
 */

function wre_format_date($date) {
	$timestamp = strtotime($date);
	$date = date_i18n(get_option('date_format'), $timestamp, false);
	return apply_filters('wre_format_date', $date, $timestamp);
}

function wre_map_key() {
	return $key = wre_option('maps_api_key') ? wre_option('maps_api_key') : false;
}

/*
 * Build Google maps URL
 */

function wre_google_maps_url() {
	$api_url = 'https://maps.googleapis.com/maps/api/js?v=3.exp&libraries=places';
	$key = wre_map_key();
	if ($key) {
		$api_url = $api_url . '&key=' . $key;
	}
	return $api_url;
}

/*
 * Build Google maps Geocode URL
 */

function wre_google_geocode_maps_url($address) {
	$api_url = "https://maps.google.com/maps/api/geocode/json?address={$address}";
	$key = wre_map_key();
	$country = wre_search_country();
	if (!empty($key)) {
		$api_url = $api_url . '&key=' . $key;
	}

	if (!empty($country)) {
		$api_url = $api_url . '&components=country:' . $country;
	}

	return apply_filters('wre_google_geocode_maps_url', $api_url);
}

/*
 * Get search country
 */

function wre_search_country() {
	$country = wre_option('search_country') ? wre_option('search_country') : '';
	return apply_filters('wre_search_country', $country);
}

/*
 * Get distance measurement
 */

function wre_distance_measurement() {
	$measurement = wre_option('distance_measurement') ? wre_option('distance_measurement') : 'kilometers';
	return apply_filters('wre_distance_measurement', $measurement);
}

/*
 * Get search radius
 */

function wre_search_radius() {
	$search_radius = wre_option('search_radius') ? wre_option('search_radius') : 20;
	return apply_filters('wre_search_radius', $search_radius);
}

/*
 * Validate Select value
 */

function wre_sanitize_select($input, $setting) {

	//input must be a slug: lowercase alphanumeric characters, dashes and underscores are allowed only
	$input = sanitize_key($input);

	//get the list of possible select options 
	$choices = $setting->manager->get_control($setting->id)->choices;

	//return input if valid or return default option
	return ( array_key_exists($input, $choices) ? $input : $setting->default );
}

function wre_nearby_listings_callback() {

	$content = '';
	$flag = false;

	$data = $_POST['data'];
	$max_listings = $data['number'];
	$compact = $data['compact'];

	$measurement = $data['measurement'] ? $data['measurement'] : 'kilometers';
	$distance = $data['radius'] ? $data['radius'] : 50;
	$lat = $data['current_lat'];
	$lng = $data['current_lng'];
	$radius = $measurement == 'kilometers' ? 6371 : 3950;

	// latitude boundaries
	$maxlat = $lat + rad2deg($distance / $radius);
	$minlat = $lat - rad2deg($distance / $radius);

	// longitude boundaries (longitude gets smaller when latitude increases)
	$maxlng = $lng + rad2deg($distance / $radius / cos(deg2rad($lat)));
	$minlng = $lng - rad2deg($distance / $radius / cos(deg2rad($lat)));
	$related_args = array(
		'post_type' => 'listing',
		'posts_per_page' => $max_listings,
		'post_status' => 'publish',
		'post__not_in' => array(wre_get_ID()),
		'fields' => 'ids'
	);
	$related_args['meta_query'][] = array(
		'key' => '_wre_listing_lat',
		'value' => array($minlat, $maxlat),
		'type' => 'DECIMAL(10,5)',
		'compare' => 'BETWEEN',
	);
	$related_args['meta_query'][] = array(
		'key' => '_wre_listing_lng',
		'value' => array($minlng, $maxlng),
		'type' => 'DECIMAL(10,5)',
		'compare' => 'BETWEEN',
	);
	$related_posts = get_posts($related_args);
	if (!empty($related_posts)) {
		$related_posts = implode(',', $related_posts);
		$content = do_shortcode('[wre_listings number="' . $max_listings . '" ids="' . $related_posts . '" order="desc" compact="' . $compact . '"]');
		$flag = true;
	} else {
		$content = __('Sorry, no listings were found.', 'wp-real-estate');
	}
	echo wp_send_json(array('flag' => true, 'data' => $content));
	die(0);
}

add_action('wp_ajax_wre_nearby_listings', 'wre_nearby_listings_callback');
add_action('wp_ajax_nopriv_wre_nearby_listings', 'wre_nearby_listings_callback');

if (!function_exists('wre_get_agent_attachment_url')) {

	function wre_get_agent_attachment_url($agent_id, $size = 'wre-sml') {
		global $wpdb;
		$upload_url = get_the_author_meta('wre_upload_meta', $agent_id);

		if ($upload_url) {

			$attachment = $wpdb->get_col($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE guid='%s';", $upload_url));
			if (!empty($attachment)) {
				$upload_url = wp_get_attachment_image_src($attachment[0], $size);
				$upload_url = $upload_url[0];
			} else
				$upload_url = '';
		}

		if (!$upload_url)
			$upload_url = get_avatar_url($agent_id, array('size' => 512));

		return $upload_url;
	}

}

if (!function_exists('wre_get_comparison_data')) {

	function wre_get_comparison_data() {

		$compared_listings = false;
		if (isset($_COOKIE['wre_compare_listings']) && !empty($_COOKIE['wre_compare_listings'])) {
			$compared_listings = (array) json_decode(stripslashes($_COOKIE['wre_compare_listings']));
		}
		return $compared_listings;
	}

}

if (!function_exists('wre_get_comparison_content')) {

	function wre_get_comparison_content($listing_id) {

		if (!$listing_id)
			return;

		ob_start();
		$listing_image = wre_get_first_image($listing_id);
		?>

		<li>
			<img src="<?php echo esc_url($listing_image['sml']); ?>" />
			<a href="#" class="remove-listing" data-id="<?php echo esc_attr($listing_id); ?>">
				<i class="wre-icon-close"></i>
			</a>
			<h3 class="wre-compare-title"><?php echo get_the_title($listing_id); ?></h3>
		</li>

		<?php
		return ob_get_clean();
	}

}

if (!function_exists('wre_is_theme_compatible')) {

	function wre_is_theme_compatible() {
		$wre_theme_compatible = wre_option('wre_theme_compatibility') ? wre_option('wre_theme_compatibility') : 'enable';

		if ($wre_theme_compatible == 'disable')
			return false;

		return true;
	}

}

function wre_compare_listings_callback() {

	$listing_id = $_POST['listing_id'];
	$compared_listings = '';
	$flag = false;
	$message = $image = '';
	$compared_listings = wre_get_comparison_data();

	if ($compared_listings) {

		if (count($compared_listings) > 3) {
			$message = __('You have reached the maximum of four listings per comparison.', 'wp-real-estate');
		} else {
			if (!in_array($listing_id, $compared_listings)) {
				array_push($compared_listings, $listing_id);
				$flag = true;
			}
		}
	} else {
		$compared_listings[] = $listing_id;
		$flag = true;
	}
	if ($flag) {
		$cookie = setcookie('wre_compare_listings', json_encode($compared_listings), '0', '/');
		$message = wre_get_comparison_content($listing_id);
	}

	echo wp_send_json(array('flag' => $flag, 'message' => $message));
	die(0);
}

add_action('wp_ajax_wre_compare_listings', 'wre_compare_listings_callback');
add_action('wp_ajax_nopriv_wre_compare_listings', 'wre_compare_listings_callback');

function wre_remove_compare_listings_callback() {

	$listing_id = $_POST['listing_id'];
	$compared_listings = wre_get_comparison_data();
	if ($compared_listings) {

		$compared_listings = array_diff($compared_listings, array($listing_id));
		$cookie = setcookie('wre_compare_listings', json_encode($compared_listings), '0', "/");
	}

	echo wp_send_json(array('flag' => $cookie));
	die(0);
}

add_action('wp_ajax_wre_remove_compare_listings', 'wre_remove_compare_listings_callback');
add_action('wp_ajax_nopriv_wre_remove_compare_listings', 'wre_remove_compare_listings_callback');


add_filter('the_content', 'wre_overwrite_content');

if (!function_exists('wre_overwrite_content')) {

	function wre_overwrite_content($content) {

		if ( wre_is_theme_compatible() && is_singular('listing') ) {

			ob_start();
			/**
			 * @hooked wre_output_content_wrapper (outputs opening divs for the content)
			 */
			do_action('wre_before_main_content');

				wre_get_part('content-single-listing.php');

			/*
			 * @hooked wre_output_content_wrapper_end (outputs closing divs for the content)
			 */
			do_action('wre_after_main_content');

			$content = ob_get_clean();
		}

		return $content;
	}

}

if (!function_exists('wre_get_contextual_query')) {

	function wre_get_contextual_query() {
		
		global $wp_query, $post;
		static $contextual_query;
		if (!is_archive()) {

			$archive_listings_page = wre_option('archives_page');

			if (is_page($archive_listings_page) || has_shortcode( $post->post_content, 'wre_archive_listings')) {
				$contextual_query = new WRE_Archive_Listings();
				$contextual_query = $contextual_query->build_query();
			}
			return $contextual_query;
		}

		return $wp_query;
	}

}

/*
 * Set the path to be used in the theme folder.
 * Templates in this folder will override the plugins frontend templates.
 */

function wre_template_path() {
	return apply_filters('wre_template_path', 'listings/');
}

function wre_get_part($part, $id = null) {

	if ($part) {

		// Look within passed path within the theme - this is priority.
		$template = locate_template(
				array(
					trailingslashit(wre_template_path()) . $part,
					$part,
				)
		);

		// Get template from plugin directory
		if (!$template) {

			$check_dirs = apply_filters('wre_template_directory', array(
				WRE_PLUGIN_DIR . 'templates/',
			));
			foreach ($check_dirs as $dir) {
				if (file_exists(trailingslashit($dir) . $part)) {
					$template = $dir . $part;
				}
			}
		}

		include( $template );
	}
}

/* Display a notice*/

add_action('admin_notices', 'wp_real_estate_admin_notice');

function wp_real_estate_admin_notice() {
    global $current_user ;
    $user_id = $current_user->ID;
    /* Check that the user hasn't already clicked to ignore the message */
    /* Only show the notice 2 days after plugin activation */
    if ( ! get_user_meta($user_id, 'wp_real_estate_ignore_notice') && time() >= (get_option( 'wp_real_estate_activated', 0 ) + (2 * 24 * 60 * 60)) ) {
        echo '<div class="updated notice-info wp-real-estate-notice" id="wprealestate-notice" style="position:relative;">';
			printf(__('<p><strong>WP Real Estate Pro</strong> offers advanced IDX integration, Paid Listings, User Subscriptions, Related Listings, Import/Export, Payment Gateways and much more... <br><a target="_blank" href="https://mythemeshop.com/plugins/wp-real-estate-pro/?utm_source=WP+Real+Estate&utm_medium=Notification+Link&utm_content=WP+Real+Estate+Pro+LP&utm_campaign=WordPressOrg"><strong>Grab your copy now!</strong></a></p><a class="notice-dismiss" href="%1$s"></a>'), '?wp_real_estate_admin_notice_ignore=0');
			echo "</div>";
    }
}

add_action('admin_init', 'wp_real_estate_admin_notice_ignore');

function wp_real_estate_admin_notice_ignore() {
    global $current_user;
        $user_id = $current_user->ID;
        /* If user clicks to ignore the notice, add that to their user meta */
        if ( isset($_GET['wp_real_estate_admin_notice_ignore']) && '0' == $_GET['wp_real_estate_admin_notice_ignore'] ) {
             add_user_meta($user_id, 'wp_real_estate_ignore_notice', 'true', true);
    }
}