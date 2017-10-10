<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

if (!class_exists('WRE_Admin_Metaboxes')) :

	/**
	 * CMB2 Theme Options
	 * @version 0.1.0
	 */
	class WRE_Admin_Metaboxes {

		/**
		 * Constructor
		 * @since 0.1.0
		 */
		public function __construct() {
			add_action('cmb2_admin_init', array($this, 'register_metaboxes'));
			add_filter('cmb2-taxonomy_meta_boxes', array($this, 'wre_listing_type_metaboxes'));
		}

		/**
		 * Add the options metabox to the array of metaboxes
		 * @since  0.1.0
		 */
		public function register_metaboxes() {

			/**
			 * Load the metaboxes for listing post type
			 */
			$listing_metaboxes = new WRE_Metaboxes();
			$listing_metaboxes->get_instance();
		}

		/**
		 * Define the metabox and field configurations.
		 *
		 * @param  array $meta_boxes
		 * @return array
		 */
		function wre_listing_type_metaboxes(array $meta_boxes) {

			// Start with an underscore to hide fields from custom fields list
			$prefix = '_wre_';

			/**
			 * Sample metabox to demonstrate each field type included
			 */
			$meta_boxes['marker_metabox'] = array(
				'id' => 'marker_image_metabox',
				'title' => __('Marker Image', 'wp-real-estate'),
				'object_types' => array('listing-type'), // Taxonomy
				'context' => 'normal',
				'priority' => 'high',
				'show_names' => true, // Show field names on the left
				// 'cmb_styles' => false, // false to disable the CMB stylesheet
				'fields' => array(
					array(
						'name' => __('Marker Image', 'wp-real-estate'),
						'desc' => __('Upload an image or enter a URL.', 'wp-real-estate'),
						'id' => $prefix . 'marker_image',
						'type' => 'file',
						'text' => array(
							'add_upload_files_text' => __('Add Image', 'wp-real-estate'),
						),
					),
				),
			);

			return $meta_boxes;
		}

	}

	new WRE_Admin_Metaboxes();

endif;