<?php
/**
 * Setup menus in WP admin.
 *
 * @author   WRE
 * @category Admin
 * @package  WRE/Admin
 * @version  1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WRE_Admin_Menu' ) ) :

	/**
	 * WRE_Admin_Menus Class.
	 */
	class WRE_Admin_Menu {

		/**
		 * Hook in tabs.
		 */
		public function __construct() {
			add_action( 'admin_menu', array( $this, 'listings_menu' ), 9 );
			add_action( 'admin_head', array( $this, 'menu_highlight' ) );
		}

		/**
		 * Add menu item.
		 */
		public function listings_menu() {
			add_submenu_page( 'edit.php?post_type=listing', __( 'Agents', 'wp-real-estate' ), __( 'Agents', 'wp-real-estate' ), 'publish_listings', 'users.php?role=wre_agent' );
		}

		/**
		 * Keep menu open.
		 *
		 * Highlights the wanted admin (sub-) menu items for the CPT.
		 */
		function menu_highlight() {
			global $parent_file, $submenu_file;
			if( isset( $_GET['role'] ) && $_GET['role'] == 'wre_agent' ) {
				$parent_file 	= 'edit.php?post_type=listing';
				$submenu_file 	= 'users.php?role=wre_agent';
			}
		}

	}

endif;

return new WRE_Admin_Menu();