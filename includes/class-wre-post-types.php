<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

/**
 * The main class
 *
 * @since 1.0.0
 */
class WRE_Post_Types {

	/**
	 * Main constructor
	 *
	 * @since 1.0.0
	 *
	 */
	public function __construct() {
		// Hook into actions & filters
		$this->hooks();
	}

	/**
	 * Hook in to actions & filters
	 *
	 * @since 1.0.0
	 */
	public function hooks() {
		add_action('init', array($this, 'register_post_type'));
	}

	/**
	 * Registers and sets up the custom post types
	 *
	 * @since 1.0
	 * @return void
	 */
	public function register_post_type() {

		// get the slug for a single listing
		$listing_slug = wre_option('single_url') ? wre_option('single_url') : 'listing';

		$listing_labels = apply_filters('wre_listing_labels', array(
			'name' => _x('%2$s', 'listing post type name', 'wp-real-estate'),
			'singular_name' => _x('%1$s', 'singular listing post type name', 'wp-real-estate'),
			'add_new' => __('New %1s', 'wp-real-estate'),
			'add_new_item' => __('Add New %1$s', 'wp-real-estate'),
			'edit_item' => __('Edit %1$s', 'wp-real-estate'),
			'new_item' => __('New %1$s', 'wp-real-estate'),
			'all_items' => __('%2$s', 'wp-real-estate'),
			'view_item' => __('View %1$s', 'wp-real-estate'),
			'search_items' => __('Search %2$s', 'wp-real-estate'),
			'not_found' => __('No %2$s found', 'wp-real-estate'),
			'not_found_in_trash' => __('No %2$s found in Trash', 'wp-real-estate'),
			'parent_item_colon' => '',
			'menu_name' => _x('%2$s', 'listing post type menu name', 'wp-real-estate'),
			'filter_items_list' => __('Filter %2$s list', 'wp-real-estate'),
			'items_list_navigation' => __('%2$s list navigation', 'wp-real-estate'),
			'items_list' => __('%2$s list', 'wp-real-estate'),
				));

		foreach ($listing_labels as $key => $value) {
			$listing_labels[$key] = sprintf($value, __('Listing', 'wp-real-estate'), __('Listings', 'wp-real-estate'));
		}
		if( wre_is_theme_compatible() ) {
			$listing_archive = false;
		} else {
			$listing_archive = ( $archive_page = wre_option('archives_page') ) && get_post($archive_page) ? get_page_uri($archive_page) : 'listings';
		}
		$listing_args = array(
			'labels' => $listing_labels,
			'public' => true,
			'publicly_queryable' => true,
			'show_ui' => true,
			'show_in_menu' => true,
			'menu_icon' => 'dashicons-admin-multisite',
			'menu_position' => 56,
			'query_var' => true,
			'rewrite' => array('slug' => untrailingslashit($listing_slug), 'with_front' => false, 'feeds' => true),
			'capability_type' => 'listing',
			'map_meta_cap' => true,
			'has_archive' => $listing_archive,
			'hierarchical' => false,
			'supports' => apply_filters('wre_listing_supports', array('title', 'revisions', 'author')),
            'show_in_rest' => true,
		);
		register_post_type('listing', apply_filters('wre_listing_post_type_args', $listing_args));

		flush_rewrite_rules(true);

		$enquiry_labels = apply_filters('wre_enquiry_labels', array(
			'name' => _x('%2$s', 'enquiry post type name', 'wp-real-estate'),
			'singular_name' => _x('%1$s', 'singular enquiry post type name', 'wp-real-estate'),
			'add_new' => __('New %1s', 'wp-real-estate'),
			'add_new_item' => __('Add New %1$s', 'wp-real-estate'),
			'edit_item' => __('Edit %1$s', 'wp-real-estate'),
			'new_item' => __('New %1$s', 'wp-real-estate'),
			'all_items' => __('%2$s', 'wp-real-estate'),
			'view_item' => __('View %1$s', 'wp-real-estate'),
			'search_items' => __('Search %2$s', 'wp-real-estate'),
			'not_found' => __('No %2$s found', 'wp-real-estate'),
			'not_found_in_trash' => __('No %2$s found in Trash', 'wp-real-estate'),
			'parent_item_colon' => '',
			'menu_name' => _x('%2$s', 'enquiry post type menu name', 'wp-real-estate'),
			'filter_items_list' => __('Filter %2$s list', 'wp-real-estate'),
			'items_list_navigation' => __('%2$s list navigation', 'wp-real-estate'),
			'items_list' => __('%2$s list', 'wp-real-estate'),
				));

		foreach ($enquiry_labels as $key => $value) {
			$enquiry_labels[$key] = sprintf($value, __('Enquiry', 'wp-real-estate'), __('Enquiries', 'wp-real-estate'));
		}

		$enquiry_args = array(
			'labels' => $enquiry_labels,
			'public' => false,
			'publicly_queryable' => false,
			'exclude_from_search' => true,
			'show_in_nav_menus' => false,
			'show_ui' => true,
			'show_in_menu' => 'edit.php?post_type=listing',
			'show_in_admin_bar' => false,
			'menu_icon' => 'dashicons-email',
			'menu_position' => 56,
			'query_var' => true,
			'capability_type' => 'post',
			'capabilities' => array(
				'create_posts' => false, // Removes support for the "Add New" function ( use 'do_not_allow' instead of false for multisite set ups )
			),
			'map_meta_cap' => true, // Set to `false`, if users are not allowed to edit/delete existing posts
			//'has_archive'			=> '',
			'hierarchical' => false,
			'supports' => apply_filters('wre_enquiry_supports', array('title', 'revisions')),
            'show_in_rest' => true,
		);
		register_post_type('listing-enquiry', apply_filters('wre_enquiry_post_type_args', $enquiry_args));

		// Add new taxonomy, make it hierarchical (like categories)
		$labels = array(
			'name' => _x('Listing Type', 'taxonomy general name', 'wp-real-estate'),
			'singular_name' => _x('Listing Type', 'taxonomy singular name', 'wp-real-estate'),
			'search_items' => __('Search Listing Type', 'wp-real-estate'),
			'all_items' => __('All Types', 'wp-real-estate'),
			'parent_item' => __('Parent Listing Type', 'wp-real-estate'),
			'parent_item_colon' => __('Parent Listing Type:', 'wp-real-estate'),
			'edit_item' => __('Edit Listing Type', 'wp-real-estate'),
			'update_item' => __('Update Listing Type', 'wp-real-estate'),
			'add_new_item' => __('Add New Listing Type', 'wp-real-estate'),
			'new_item_name' => __('New Listing Type', 'wp-real-estate'),
			'menu_name' => __('Listing Type', 'wp-real-estate'),
		);

		$args = array(
			'hierarchical' => true,
			'labels' => $labels,
			'show_ui' => true,
			'show_admin_column' => true,
			'query_var' => true,
			'rewrite' => array('slug' => 'listing-type'),
			'show_in_quick_edit' => false,
			'meta_box_cb' => false
		);

		register_taxonomy('listing-type', array('listing'), $args);
	}

}

return new WRE_Post_Types();