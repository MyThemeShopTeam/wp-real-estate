<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * WRE_Roles Class
 *
 * This class handles the role creation and assignment of capabilities for those roles.
 *
 *
 * @since 1.0.0
 */
class WRE_Roles {
 
	/**
	 * Add new shop roles with default WP caps
	 * Called during installation
	 *
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function add_roles() {
		add_role( 'wre_agent', __( 'WRE Agent', 'wp-real-estate' ), array(
			'read'                      => true,
			'edit_posts'                => true,
			'delete_posts'              => true,
			'unfiltered_html'           => true,
			'upload_files'              => true,
			'delete_others_posts'       => false,
			'delete_private_posts'      => true,
			'delete_published_posts'    => true,
			'edit_others_posts'         => false,
			'edit_private_posts'        => true,
			'edit_published_posts'      => true,
			'publish_posts'             => true,
			'read_private_posts'        => true,
			'list_users'                => false,
			'add_users'                 => false,
			'create_users'              => false,
			'edit_users'                => false
			)
		);
	}

	/**
	 * Add new WRE specific capabilities
	 * Called during installation
	 *
	 * @access public
	 * @since  1.0.0
	 * @global WP_Roles $wp_roles
	 * @return void
	 */
	public function add_caps() {
		global $wp_roles;

		if ( class_exists('WP_Roles') ) {
			if ( ! isset( $wp_roles ) ) {
				$wp_roles = new WP_Roles();
			}
		}

		if ( is_object( $wp_roles ) ) {

			// Add the main post type capabilities
			$capabilities = $this->get_wre_agent_caps();
			foreach ( $capabilities as $cap_group ) {
				foreach ( $cap_group as $cap ) {
					$wp_roles->add_cap( 'wre_agent', $cap );
					$wp_roles->add_cap( 'administrator', $cap );
				}
			}
			$wp_roles->remove_cap( 'wre_agent', 'edit_others_listings' );
			$wp_roles->remove_cap( 'wre_agent', 'delete_others_listings' );
		}
	}

	/**
	 * Gets the core post type capabilities
	 *
	 * @access public
	 * @since  1.0.0
	 * @return array $capabilities Core post type capabilities
	 */
	public function get_wre_agent_caps() {
		$capabilities = array();

		$capabilities[ 'listing'] = array(
			// Users
			"list_users",

			// Listings
			"edit_listing",
			"read_listing",
			"delete_listing",
			"edit_listings",
			"edit_others_listings",
			"publish_listings",
			"read_private_listings",
			"delete_listings",
			"delete_private_listings",
			"delete_published_listings",
			"delete_others_listings",
			"edit_private_listings",
			"edit_published_listings"
		);

		return $capabilities;
	}

	/**
	 * Remove core post type capabilities (called on uninstall)
	 *
	 * @access public
	 * @since 1.5.2
	 * @return void
	 */
	public function remove_caps() {

		global $wp_roles;

		if ( class_exists( 'WP_Roles' ) ) {
			if ( ! isset( $wp_roles ) ) {
				$wp_roles = new WP_Roles();
			}
		}

		if ( is_object( $wp_roles ) ) {

			// Add the main post type capabilities
			$agent_caps   = $this->get_wre_agent_caps();
			$agent_role   = get_role( 'wre_agent' );
			$admin_role   = get_role( 'administrator' );

			//pp( $agent_role );
			foreach ( $agent_caps as $post_type ) {
				foreach ( $post_type as $index => $cap ) {
					if( $agent_role ) {
						$agent_role->remove_cap( $cap );
					}
				}
			}

		}

	}
}