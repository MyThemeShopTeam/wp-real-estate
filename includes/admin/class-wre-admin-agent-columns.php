<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) )
	exit;

if ( !class_exists( 'WRE_Admin_Agent_Columns' ) ):
	
	/**
	 * Admin columns
	 * @version 0.1.0
	 */
	class WRE_Admin_Agent_Columns {

		/**
		 * Constructor
		 * @since 0.1.0
		 */
		public function __construct() {
			return $this->hooks();
		}

		public function hooks() {
			if ( isset( $_GET['role'] ) && ( $_GET['role'] == 'wre_agent' || $_GET['role'] == 'administrator' ) ) {
				add_filter( 'manage_users_columns', array( $this, 'modify_user_table' ) );
				add_filter( 'manage_users_custom_column', array( $this, 'modify_user_table_row' ), 10, 3 );
			}
		}

		public function modify_user_table( $column ) {
			if ( isset( $_GET['role'] ) && $_GET['role'] == 'wre_agent' ) {
				unset( $column['posts'] );
				unset( $column['role'] );
			}
			$column['wre']			= __( 'Listings', 'wp-real-estate' );
			$column['mobile']		= __( 'Mobile', 'wp-real-estate' );
			$column['office_phone']	= __( 'Office Phone', 'wp-real-estate' );
			return $column;
		}

		function modify_user_table_row( $val, $column_name, $user_id ) {

			switch ( $column_name ) {

				case 'wre':
					return '<a href="' . esc_url( admin_url( 'edit.php?post_type=listing&agent=' . $user_id ) ) . '"><strong>' . esc_html( wre_agent_listings_count( $user_id ) ) . '</strong></a>';
				break;

				case 'mobile':
					return esc_html( get_the_author_meta( 'mobile', $user_id ) );
				break;

				case 'name':
					return esc_html( get_the_author_meta( 'display_name', $user_id ) ) . '<br>' . esc_html( get_the_author_meta( 'title_position', $user_id ) );
				break;

				case 'office_phone':
					return esc_html( get_the_author_meta( 'phone', $user_id ) );
				break;

				default:

			}
			return $val;
		}

	}

	new WRE_Admin_Agent_Columns;
endif;