<?php
/**
 * Uninstall WRE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

// Load WRE file.
include_once( 'wp-real-estate.php' );

$remove = wre_option( 'delete_data' );
/**
 * WRE_Uninstall Class
 *
 * This class removes post data, options data and user roles and capabilities
 *
 *
 * @since 1.0.0
 */
if ( ! class_exists( 'WRE_Uninstall' ) ) :

	class WRE_Uninstall {

		public function __clone() {
			_doing_it_wrong( __FUNCTION__, __( 'Cheating huh?', 'wp-real-estate' ), '1.0.0' );
		}

		public function __wakeup() {
			_doing_it_wrong( __FUNCTION__, __( 'Cheating huh?', 'wp-real-estate' ), '1.0.0' );
		}

		/**
		 * The Constructor.
		 *
		 * @since  1.0.0
		 */
		public function __construct() {
			$this->remove_post_types();
			$this->remove_pages();
			$this->delete_options();
			$this->delete_agent_data();
			$this->delete_capabilities();
		}

		/**
		 * Removes Posts data.
		 *
		 * @since  1.0.0
		 */
		public function remove_post_types() {
			global $wpdb;
			$taxonomies = array( 'listing-type' );

			$wre_post_types = array( 'listing', 'listing-enquiry' );
			foreach ( $wre_post_types as $post_type ) {
				$taxonomies = array_merge( $taxonomies, get_object_taxonomies( $post_type ) );
				$wre_posts = get_posts( array( 'post_type' => $post_type, 'post_status' => 'any', 'numberposts' => -1, 'fields' => 'ids' ) );
				if ( $wre_posts ) {
					foreach ( $wre_posts as $wre_post ) {
						$this->remove_post_attachment( $wre_post );
						wp_delete_post( $wre_post, true);
					}
				}
			}

			/** Delete All the Terms & Taxonomies */
			foreach ( array_unique( array_filter( $taxonomies ) ) as $taxonomy ) {

				$terms = $wpdb->get_results( $wpdb->prepare( "SELECT t.*, tt.* FROM $wpdb->terms AS t INNER JOIN $wpdb->term_taxonomy AS tt ON t.term_id = tt.term_id WHERE tt.taxonomy IN ('%s') ORDER BY t.name ASC", $taxonomy ) );

				// Delete Terms
				if ( $terms ) {
					foreach ( $terms as $term ) {
						$wpdb->delete( $wpdb->term_taxonomy, array( 'term_taxonomy_id' => $term->term_taxonomy_id ) );
						$wpdb->delete( $wpdb->terms, array( 'term_id' => $term->term_id ) );
					}
				}

				// Delete Taxonomies
				$wpdb->delete( $wpdb->term_taxonomy, array( 'taxonomy' => $taxonomy ), array( '%s' ) );
			}

		}
		
		/**
		 * Removes Plugin Specific pages.
		 *
		 * @since  1.0.0
		 */
		public function remove_pages() {
			
			$listings_page = wre_option( 'archives_page' );
			$compare_listings_page = wre_option( 'compare_listings' );
			$single_listings_page = wre_option( 'wre_single_agent' );
			$pages = array( $listings_page, $compare_listings_page, $agency_page, $single_listings_page );
			foreach ( $pages as $page ) {
				wp_delete_post( $page, true);
			}

		}

		/**
		* Removes Post Attachments.
		*
		* @param  int	$post_id
		*
		*/
		public function remove_post_attachment($post_id) {

			$images = wre_meta( 'image_gallery', $post_id );
			if( !empty($images) ) {
				foreach( $images as $attachment_id => $image ) {
					$this->remove_attachment( $attachment_id, $post_id );
				}
			}

		}

		/**
		 * Function to check if attachment is used anywhere else before deleting it.
		 *
		 * @since  1.0.0
		 */
		public function remove_attachment( $attachment_id, $post_id = '' ) {

			// First we'll check if it is used as a thumbnail by another post
			if ( empty ( get_posts( array( 'post_type' => 'any', 'post_status' => 'any', 'fields' => 'ids', 'no_found_rows' => true, 'posts_per_page' => -1, 'meta_key' => '_thumbnail_id', 'meta_value' => $attachment_id, 'post__not_in' => array( $post_id ) ) ) ) ) {

				// Now we have to check if it's used somewhere in content.
				$attachment_urls = array( wp_get_attachment_url( $attachment_id ) );
				foreach ( get_intermediate_image_sizes() as $size ) {
					$intermediate = image_get_intermediate_size( $attachment_id, $size );
					if ( $intermediate ) {
						$attachment_urls[] = $intermediate['url'];
					}
				}

				// Now we can search for these URLs in content
				$used = array();
				foreach ( $attachment_urls as $attachment_url ) {
					$used = array_merge( $used, get_posts( array( 'post_type' => 'any', 'post_status' => 'any', 'fields' => 'ids', 'no_found_rows' => true, 'posts_per_page' => -1, 's' => $attachment_url, 'post__not_in' => array( $post_id ) ) ) );
				}
				if ( empty( $used ) ) {
					// The image is not used anywhere in the content
					// So finally we can delete it
					wp_delete_attachment( $attachment_id, true );
				}
			}
		}

		/**
		 * Function to get attachment id from attachment url.
		 *
		 * @since  1.0.0
		 */
		public function get_attachment_id_from_url( $attachment_url ) {
			global $wpdb;
			$attachment = $wpdb->get_col($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE guid='%s';", $attachment_url ));
			return $attachment[0]; 
		}

		/**
		 * Function to delete plugin options.
		 *
		 * @since  1.0.0
		 */
		public function delete_options() {

			if (isset($_COOKIE['wre_compare_listings'])) {
				unset($_COOKIE['wre_compare_listings']);
				setcookie('wre_compare_listings', null, -1, '/');
			}

			delete_option( 'wre_options' );
			delete_option( 'wre_version' );
			delete_option( 'wre_version_upgraded_from' );
			delete_option( 'wre_idx_featured_listing_wp_options' );
			delete_option( 'wre_import_progress' );
			// Delete cron job
			wp_clear_scheduled_hook('wre_idx_update');

		}

		/**
		 * Function to delete agent metafields.
		 *
		 * @since  1.0.0
		 */
		public function delete_agent_data() {

			$agents = get_users( 'role=wre_agent' );
			if ( !empty( $agents ) ) {

				$agent_meta_fields = array(
					'title_position',
					'phone',
					'mobile',
					'facebook',
					'twitter',
					'google',
					'linkedin',
					'youtube',
					'specialties',
					'awards',
					'wre_meta',
					'wre_upload_meta'
				);

				foreach ( $agents as $agent ) {

					$agent_id = $agent->ID;

					$agent_profile_image = get_the_author_meta( 'wre_upload_meta', $agent_id );
					if($agent_profile_image) {
						$attachment_id = $this->get_attachment_id_from_url( $agent_profile_image );
						$this->remove_attachment( $attachment_id );
					}

					foreach ( $agent_meta_fields as $agent_meta_field ) {
						delete_user_meta( $agent_id, $agent_meta_field );
					}

				}

			}
		}

		/**
		 * Function to delete capabilities and role.
		 *
		 * @since  1.0.0
		 */
		public function delete_capabilities() {
			/** Delete Capabilities */
			$roles = new WRE_Roles;
			$roles->remove_caps();

			/** Delete the Roles */
			$wre_roles = array( 'wre_agent' );
			foreach ( $wre_roles as $role ) {
				remove_role( $role );
			}
		}

	}

endif;

if( $remove == 'yes' ) {
	return new WRE_Uninstall();
}