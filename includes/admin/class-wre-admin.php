<?php
/**
 * WRE Admin
 *
 * @class    WRE_Admin
 * @author   WRE
 * @category Admin
 * @package  WRE/Admin
 * @version  1.0.0
 */
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

/**
 * WRE_Admin class.
 */
class WRE_Admin {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action('init', array($this, 'includes'));
		add_filter('admin_body_class', array($this, 'admin_body_class'));
		add_action('after_wp_tiny_mce', array($this, 'wre_tinymce_extra_vars'));
	}

	/**
	 * Include any classes we need within admin.
	 */
	public function includes() {

		// option pages
		include_once( 'class-wre-admin-options.php' );

		// metaboxes
		include_once( 'class-wre-admin-metaboxes.php' );
		include_once( 'metaboxes/class-wre-metaboxes.php' );
		include_once( 'metaboxes/functions.php' );

		include_once( 'class-wre-admin-enqueues.php' );
		include_once( 'class-wre-admin-menu.php' );
		include_once( 'class-wre-admin-columns.php' );
		include_once( 'class-wre-admin-enquiry-columns.php' );
		include_once( 'class-wre-admin-agent-columns.php' );

		//Abort early if the user will never see TinyMCE
		if (!current_user_can('edit_posts') && !current_user_can('edit_pages') && get_user_option('rich_editing') == 'true')
			return;

		//Add a callback to regiser our tinymce plugin   
		add_filter("mce_external_plugins", array($this, 'wre_register_tinymce_plugin'));

		// Add a callback to add our button to the TinyMCE toolbar
		add_filter('mce_buttons', array($this, 'wre_add_tinymce_dropdown'));
	}

	/**
	 * Adds one or more classes to the body tag in the dashboard.
	 *
	 * @param  String $classes Current body classes.
	 * @return String          Altered body classes.
	 */
	public function admin_body_class($classes) {

		if (is_wre_admin()) {
			return "$classes wre";
		}
	}

	//This callback registers our plug-in
	public function wre_register_tinymce_plugin($plugin_array) {
		$js_dir = WRE_PLUGIN_URL . 'includes/admin/assets/js/';
		$plugin_array['wre_shortcodes_dropdown'] = $js_dir . 'wre-shortcodes.js';
		return $plugin_array;
	}

	//This callback adds our button to the toolbar
	public function wre_add_tinymce_dropdown($buttons) {
		$buttons[] = "wre_shortcodes_dropdown";
		return $buttons;
	}

	public function wre_tinymce_extra_vars() {
		$agents = get_users(array('role__in ' => array( 'admininstrator', 'wre_agent' ), 'fields' => array('ID', 'user_login')));
		$listing_meta_fields_values = $agent_lists = array();
		$args = array(
			'posts_per_page' => -1,
			'offset' => 0,
			'post_type' => 'listing',
			'post_status' => 'publish',
		);
		$listings = get_posts($args);

		if (!empty($listings)) {
			foreach ($listings as $listing) {
				$listing_meta_fields_values[] = array('text' => $listing->post_title, 'value' => $listing->ID);
			}
		}

		$listing_meta_fields = array(
			'id' => array(
				'type' => 'listbox',
				'label' => __('Listing', 'wp-real-estate'),
				'value' => '',
				'tooltip' => __('The ID of the listing you want to display.', 'wp-real-estate'),
				'values' => $listing_meta_fields_values,
			)
		);

		if (!empty($agents)) {
			$agent_lists[] = array('text' => __('Select Agent', 'wp-real-estate'), 'value' => '');
			foreach ($agents as $agent) {
				$agent_lists[] = array('text' => $agent->user_login, 'value' => $agent->ID);
			}
		}

		$agent_meta_fields = array(
			'id' => array(
				'type' => 'listbox',
				'label' => __('Agent', 'wp-real-estate'),
				'value' => '',
				'tooltip' => __('Show a single agent', 'wp-real-estate'),
				'values' => $agent_lists,
			)
		);

		$search_fields = array(
			'placeholder' => array(
				'type' => 'textbox',
				'label' => __('Placeholder', 'wp-real-estate'),
				'value' => __('Address, Suburb, Region, Zip or Landmark', 'wp-real-estate'),
				'tooltip' => __('Text to display as the placeholder text in the text input', 'wp-real-estate')
			),
			'submit_btn' => array(
				'type' => 'textbox',
				'label' => __('Submit Button', 'wp-real-estate'),
				'value' => __('Search', 'wp-real-estate'),
				'tooltip' => __('Text to display on the search button', 'wp-real-estate')
			),
			'exclude' => array(
				'type' => 'textbox',
				'label' => __('Exclude (Comma separated list of fields)', 'wp-real-estate'),
				'value' => 'type, min_beds, max_beds, min_price, max_price',
				'tooltip' => __('Comma separated list of fields that you don\'t want to include on the search box.', 'wp-real-estate')
			),
			'show_map' => array(
				'type' => 'listbox',
				'label' => __('Show Map', 'wp-real-estate'),
				'values' => [
					array('text' => __('Yes', 'wp-real-estate'), 'value' => 'yes'),
					array('text' => __('No', 'wp-real-estate'), 'value' => 'no')
				],
				'value' => 'no',
			),
		);

		$nearby_listings_fields = array(
			'distance' => array(
				'type' => 'listbox',
				'label' => __('Distance', 'wp-real-estate'),
				'values' => [
					array('text' => __('Miles', 'wp-real-estate'), 'value' => 'miles'),
					array('text' => __('Kilometers', 'wp-real-estate'), 'value' => 'kilometers')
				],
				'value' => 'miles',
				'tooltip' => __('Choose miles or kilometers for the radius.', 'wp-real-estate')
			),
			'radius' => array(
				'type' => 'textbox',
				'label' => __('Radius', 'wp-real-estate'),
				'value' => 50,
				'tooltip' => __('Show listings that are within this distance (mi or km as selected above).', 'wp-real-estate')
			),
			'view' => array(
				'type' => 'listbox',
				'label' => __('List View', 'wp-real-estate'),
				'values' => [
					array('text' => __('List View', 'wp-real-estate'), 'value' => 'list-view'),
					array('text' => __('Grid View', 'wp-real-estate'), 'value' => 'grid-view')
				],
				'value' => 'list-view',
			),
			'columns' => array(
				'type' => 'listbox',
				'label' => __('Number of Columns', 'wp-real-estate'),
				'values' => [
					array('text' => __('2 columns', 'wp-real-estate'), 'value' => '2'),
					array('text' => __('3 columns', 'wp-real-estate'), 'value' => '3'),
					array('text' => __('4 columns', 'wp-real-estate'), 'value' => '4')
				],
				'value' => '2',
				'tooltip' => __('The number of columns to display, when viewing listings in grid mode.', 'wp-real-estate'),
			),
			'compact' => array(
				'type' => 'listbox',
				'label' => __('Compact', 'wp-real-estate'),
				'values' => [
					array('text' => __('True', 'wp-real-estate'), 'value' => 'true'),
					array('text' => __('False', 'wp-real-estate'), 'value' => 'false')
				],
				'value' => 'true',
			),
			'number' => array(
				'type' => 'textbox',
				'label' => __('Number of listings to show', 'wp-real-estate'),
				'value' => 10,
				'tooltip' => __('Number of listings to show', 'wp-real-estate')
			)
		);

		$listings_fields = array(
			'orderby' => array(
				'type' => 'listbox',
				'label' => __('OrderBy', 'wp-real-estate'),
				'values' => [
					array('text' => __('Date', 'wp-real-estate'), 'value' => 'date'),
					array('text' => __('Title', 'wp-real-estate'), 'value' => 'title'),
					array('text' => __('Price', 'wp-real-estate'), 'value' => 'price'),
				],
				'value' => 'date'
			),
			'order' => array(
				'type' => 'listbox',
				'label' => __('Order', 'wp-real-estate'),
				'values' => [
					array('text' => __('Asc', 'wp-real-estate'), 'value' => 'asc'),
					array('text' => __('Desc', 'wp-real-estate'), 'value' => 'desc')
				],
				'value' => 'asc'
			),
			'number' => array(
				'type' => 'textbox',
				'label' => __('Number of listings to show', 'wp-real-estate'),
				'value' => 10,
				'tooltip' => __('Number of listings to show', 'wp-real-estate')
			),
			'agent' => array(
				'type' => 'listbox',
				'label' => __('Agent Id (only show listings by this agent ID)', 'wp-real-estate'),
				'values' => $agent_lists,
				'value' => '',
				'tooltip' => __('Show Listings of particular agent', 'wp-real-estate')
			),
			'ids' => array(
				'type' => 'textbox',
				'label' => __('Listing Ids (only show these listings)', 'wp-real-estate'),
				'value' => '',
				'tooltip' => __('Comma seperated ids of Listings to display on front-end', 'wp-real-estate')
			),
			'compact' => array(
				'type' => 'listbox',
				'label' => __('Compact', 'wp-real-estate'),
				'values' => [
					array('text' => __('True', 'wp-real-estate'), 'value' => 'true'),
					array('text' => __('False', 'wp-real-estate'), 'value' => 'false')
				],
				'value' => 'true',
			)
		);

		$agents_fields = array(
			'view' => array(
				'type' => 'listbox',
				'label' => __('View', 'wp-real-estate'),
				'values' => [
					array('text' => __('Lists', 'wp-real-estate'), 'value' => 'lists'),
					array('text' => __('Carousel', 'wp-real-estate'), 'value' => 'carousel')
				],
				'value' => 'lists',
				'tooltip' => __('Select option to display agents either in a carousel or listing view.', 'wp-real-estate')
			),
			'agents-view' => array(
				'type' => 'listbox',
				'label' => __('Agents View', 'wp-real-estate'),
				'values' => [
					array('text' => __('List View', 'wp-real-estate'), 'value' => 'list-view'),
					array('text' => __('Grid View', 'wp-real-estate'), 'value' => 'grid-view')
				],
				'value' => 'list-view',
			),
			'agent-columns' => array(
				'type' => 'listbox',
				'label' => __('Agent Columns', 'wp-real-estate'),
				'values' => [
					array('text' => __('1 column', 'wp-real-estate'), 'value' => '1'),
					array('text' => __('2 columns', 'wp-real-estate'), 'value' => '2'),
					array('text' => __('3 columns', 'wp-real-estate'), 'value' => '3'),
					array('text' => __('4 columns', 'wp-real-estate'), 'value' => '4')
				],
				'value' => '2',
				'tooltip' => __('The number of columns to display, when viewing agents in grid view.', 'wp-real-estate'),
			),
			'divider' => array(
				'type' => 'container',
				'label' => '',
				'value' => __('Carousel Settings', 'wp-real-estate')
			),
			'autoplay' => array(
				'type' => 'listbox',
				'label' => __('Autoplay', 'wp-real-estate'),
				'values' => [
					array('text' => __('True', 'wp-real-estate'), 'value' => 'true'),
					array('text' => __('False', 'wp-real-estate'), 'value' => 'false')
				],
				'value' => 'true',
				'tooltip' => __('If true, the Slider will automatically start to play.', 'wp-real-estate')
			),
			'dots' => array(
				'type' => 'listbox',
				'label' => __('Show Dots', 'wp-real-estate'),
				'values' => [
					array('text' => __('True', 'wp-real-estate'), 'value' => 'true'),
					array('text' => __('False', 'wp-real-estate'), 'value' => 'false')
				],
				'value' => 'true',
				'tooltip' => __('Show dots below the slider.', 'wp-real-estate')
			),
			'controls' => array(
				'type' => 'listbox',
				'label' => __('Controls', 'wp-real-estate'),
				'values' => [
					array('text' => __('True', 'wp-real-estate'), 'value' => 'true'),
					array('text' => __('False', 'wp-real-estate'), 'value' => 'false')
				],
				'value' => 'true',
				'tooltip' => __('If false, prev/next buttons will not be displayed.', 'wp-real-estate')
			),
			'loop' => array(
				'type' => 'listbox',
				'label' => __('Loop', 'wp-real-estate'),
				'values' => [
					array('text' => __('True', 'wp-real-estate'), 'value' => 'true'),
					array('text' => __('False', 'wp-real-estate'), 'value' => 'false')
				],
				'value' => 'true',
				'tooltip' => __('If false, will disable the ability to loop back to the beginning of the slide when on the last element.', 'wp-real-estate')
			),
			'items' => array(
				'type' => 'textbox',
				'label' => __('How many agents to show at once.', 'wp-real-estate'),
				'value' => 2,
				'tooltip' => __('How many agents to show at once if carousel option is selected above.', 'wp-real-estate')
			),
			'number' => array(
				'type' => 'textbox',
				'label' => __('Number of agents to show.', 'wp-real-estate'),
				'value' => 10,
				'tooltip' => __('Number of agents to show.', 'wp-real-estate')
			)
		);

		$status_data[] = array('text' => __('Select Status', 'wp-real-estate'), 'value' => '');
		$listing_statuses = wre_option('listing_status');
		if (!empty($listing_statuses)) {
			foreach ($listing_statuses as $listing_status) {
				$status_slug = strtolower(str_replace(' ', '-', $listing_status));
				$status_data[] = array('text' => $listing_status, 'value' => $status_slug);
			}
		}
		$listing_types = get_terms('listing-type', array('fields' => 'id=>name'));
		$types_data[] = array('text' => __('Select Type', 'wp-real-estate'), 'value' => '');
		if (!empty($listing_types)) {
			foreach ($listing_types as $key => $listing_type) {
				$types_data[] = array('text' => $listing_type, 'value' => $key);
			}
		}
		$map_fields = array(
			'number' => array(
				'type' => 'textbox',
				'label' => __('Number of listings to show', 'wp-real-estate'),
				'value' => 100,
				'tooltip' => __('Number of listings to show', 'wp-real-estate')
			),
			'include' => array(
				'type' => 'textbox',
				'label' => __('Include Ids (only show these listings)', 'wp-real-estate'),
				'value' => '',
				'tooltip' => __('Comma seperated ids of Listings to display', 'wp-real-estate')
			),
			'exclude' => array(
				'type' => 'textbox',
				'label' => __('Exclude Ids (exclude these listings)', 'wp-real-estate'),
				'value' => '',
				'tooltip' => __('Comma seperated ids of Listings to exclude', 'wp-real-estate')
			),
			'purpose' => array(
				'type' => 'listbox',
				'label' => __('Listings Purpose', 'wp-real-estate'),
				'values' => [
					array('text' => __('Both', 'wp-real-estate'), 'value' => ''),
					array('text' => __('Rent', 'wp-real-estate'), 'value' => 'rent'),
					array('text' => __('Sell', 'wp-real-estate'), 'value' => 'sell')
				],
				'value' => ''
			),
			'status' => array(
				'type' => 'listbox',
				'label' => __('Listings Status', 'wp-real-estate'),
				'values' => $status_data,
				'value' => ''
			),
			'type' => array(
				'type' => 'listbox',
				'label' => __('Listings Type', 'wp-real-estate'),
				'values' => $types_data,
				'value' => ''
			),
			'relation' => array(
				'type' => 'listbox',
				'label' => __('Relation', 'wp-real-estate'),
				'values' => [
					array('text' => __('AND', 'wp-real-estate'), 'value' => 'and'),
					array('text' => __('OR', 'wp-real-estate'), 'value' => 'or')
				],
				'value' => 'and',
				'tooltip' => __('This is the relationship between purpose, status and agent.', 'wp-real-estate')
			),
			
			'agent ' => array(
				'type' => 'textbox',
				'label' => __('Agent Ids', 'wp-real-estate'),
				'value' => '',
				'tooltip' => __('Comma separated list of Agent ids for showing only listings by those agents.', 'wp-real-estate')
			),

			'divider' => array(
				'type' => 'container',
				'label' => '',
				'value' => __('Map Settings', 'wp-real-estate')
			),
			
			'height ' => array(
				'type' => 'textbox',
				'label' => __('Height (Height of the map in pixels.)', 'wp-real-estate'),
				'value' => 400,
				'tooltip' => __('Height of the map in pixels.', 'wp-real-estate')
			),
			
			'fit' => array(
				'type' => 'listbox',
				'label' => __('Fit', 'wp-real-estate'),
				'values' => [
					array('text' => __('TRUE', 'wp-real-estate'), 'value' => 'true'),
					array('text' => __('FALSE', 'wp-real-estate'), 'value' => 'false')
				],
				'value' => 'true',
				'tooltip' => __(' This will automatically adjust center and zoom so that all listings fit within the map viewport.', 'wp-real-estate')
			),
			'zoom' => array(
				'type' => 'textbox',
				'label' => __('Zoom (A number between 1-20)', 'wp-real-estate'),
				'value' => 13,
				'tooltip' => __('A number between 1-20. Only works if fit is set to false.', 'wp-real-estate')
			),
			'center ' => array(
				'type' => 'textbox',
				'label' => __('Center (Latitude and longitude of the center of the map)', 'wp-real-estate'),
				'value' => '35.652832, 139.839478',
				'tooltip' => __('Comma seperated lat and lng. Only works if fit is set to false', 'wp-real-estate')
			),
			'search' => array(
				'type' => 'listbox',
				'label' => __('Search', 'wp-real-estate'),
				'values' => [
					array('text' => __('TRUE', 'wp-real-estate'), 'value' => 'true'),
					array('text' => __('FALSE', 'wp-real-estate'), 'value' => 'false')
				],
				'value' => 'true',
				'tooltip' => __(' Show the search box or not on the map.', 'wp-real-estate')
			),
			'search_zoom ' => array(
				'type' => 'textbox',
				'label' => __('Search Zoom (A number between 1-20)', 'wp-real-estate'),
				'value' => 13,
				'tooltip' => __('A number between 1-20. Only works if search is set to false.', 'wp-real-estate')
			),
		);
		?>
		<script type="text/javascript">
			var wre_tinyMCE_object = <?php
		echo json_encode(
				array(
					'agent_fields' => $agent_meta_fields,
					'listing_fields' => $listing_meta_fields,
					'search_fields' => $search_fields,
					'nearby_listing_fields' => $nearby_listings_fields,
					'listings_fields' => $listings_fields,
					'wre_agents_fields' => $agents_fields,
					'wre_map_fields' => $map_fields
				)
		);
		?>
		</script><?php
	}

}

return new WRE_Admin();
