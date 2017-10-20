<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

add_filter('post_class', 'wre_listing_post_class', 20, 3);

/**
 * Content Wrappers.
 *
 */
add_action('wre_before_main_content', 'wre_output_content_wrapper', 10);
add_action('wre_after_main_content', 'wre_output_content_wrapper_end', 10);

/**
 * Sidebar.
 *
 */
add_action('wre_sidebar', 'wre_get_sidebar', 10);

/**
 * Before listings
 *
 */
add_action('wre_archive_page_content', 'wre_listing_archive_title', 10);
add_action('wre_archive_page_content', 'wre_listing_archive_content', 20);

add_action('wre_before_listings_loop', 'wre_comparison', 10);
add_action('wre_before_listings_loop', 'wre_ordering', 10);
add_action('wre_before_listings_loop', 'wre_view_switcher', 20);
add_action('wre_before_listings_loop', 'wre_pagination', 30);

add_action('wre_after_listings_loop', 'wre_pagination', 10);

/**
 * Listing Loop Items.
 *
 */
add_action('wre_before_listings_loop_item_summary', 'wre_template_loop_image', 10);
add_action('wre_before_listings_loop_item_wrapper', 'wre_before_listings_loop_item_wrapper', 10);
add_action('wre_after_listings_loop_item_wrapper', 'wre_after_listings_loop_item_wrapper', 10);

add_action('wre_listings_loop_item', 'wre_template_loop_title', 10);
add_action('wre_listings_loop_item', 'wre_template_loop_price', 20);
add_action('wre_listings_loop_item', 'wre_template_loop_address', 30);
add_action('wre_listings_loop_item', 'wre_template_loop_at_a_glance', 40);
add_action('wre_listings_loop_item', 'wre_template_loop_description', 50);
add_action('wre_listings_loop_item', 'wre_template_loop_compare', 60);

/**
 * Single Listing
 *
 */
add_action('wre_single_listing_gallery', 'wre_template_single_gallery', 10);

add_action('wre_single_listing_summary', 'wre_template_single_title', 10);
add_action('wre_single_listing_summary', 'wre_template_single_price', 20);
add_action('wre_single_listing_summary', 'wre_template_single_address', 30);
add_action('wre_single_listing_summary', 'wre_template_single_at_a_glance', 40);
add_action('wre_single_listing_summary', 'wre_template_single_sizes', 50);
add_action('wre_single_listing_summary', 'wre_template_single_mls_number', 60);
add_action('wre_single_listing_summary', 'wre_template_single_open_for_inspection', 70);

add_action('wre_single_listing_content', 'wre_template_single_tagline', 10);
add_action('wre_single_listing_content', 'wre_template_single_description', 20);
add_action('wre_single_listing_content', 'wre_template_single_internal_features', 30);
add_action('wre_single_listing_content', 'wre_template_single_external_features', 40);
add_action('wre_single_listing_content', 'wre_template_single_social_share', 50);

add_action('wre_single_listing_sidebar', 'wre_template_single_map', 10);
add_action('wre_single_listing_sidebar', 'wre_template_single_agent_details', 20);
add_action('wre_single_listing_sidebar', 'wre_template_single_contact_form', 30);

/**
 * Single Agent
 *
 */
add_action('wre_single_agent_intro', 'wre_template_agent_avatar', 10);
add_action('wre_single_agent_intro', 'wre_template_agent_social', 20);
add_action('wre_single_agent_summary', 'wre_template_agent_name', 10);
add_action('wre_single_agent_summary', 'wre_template_agent_title_position', 20);
add_action('wre_single_agent_summary', 'wre_template_agent_contact', 30);


add_action('wre_single_agent_content', 'wre_template_agent_description', 10);
add_action('wre_single_agent_content', 'wre_template_agent_specialties', 20);
add_action('wre_single_agent_content', 'wre_template_agent_awards', 30);

add_action('wre_single_agent_sidebar', 'wre_template_agent_listings', 10);

add_action('wre_single_agent_bottom', 'wre_template_agent_bottom', 10);