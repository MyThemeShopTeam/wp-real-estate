<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) )
	exit;

/**
 * Enqueues the required admin scripts.
 *
 */
function wre_load_admin_scripts( $hook ) {
	
	$css_dir = WRE_PLUGIN_URL . 'includes/admin/assets/css/';
	$js_dir  = WRE_PLUGIN_URL . 'includes/admin/assets/js/';

	if ( $hook == 'profile.php' || $hook == 'user-edit.php' || is_wre_admin() == true ) {
		wp_enqueue_style( 'wre-admin', $css_dir . 'wre-admin.css', WRE_VERSION );
		if( is_rtl() )
			wp_enqueue_style( 'wre-rtl-admin', $css_dir . 'wre-admin-rtl.css', WRE_VERSION );
		/*
		 * Google map scripts
		 */
		if( wre_map_key() ) {
			$api_url = 'https://maps.googleapis.com/maps/api/js?v=3.exp&libraries=places';
			$api_url = $api_url . '&key=' . wre_map_key();
			wp_enqueue_script( 'wre-google-maps', $api_url, array(), true );
			wp_enqueue_script( 'wre-geocomplete', $js_dir . 'jquery.geocomplete.min.js', array(), WRE_VERSION, true );
			wp_enqueue_script( 'wre-geocomplete-map', $js_dir . 'wre-admin-geocomplete.js', array(), WRE_VERSION, true );
		}
	}
	wp_enqueue_script( 'wre-admin', $js_dir . 'wre-admin.js', array(), WRE_VERSION, true );
}
add_action( 'admin_enqueue_scripts', 'wre_load_admin_scripts', 100 );
add_action( 'customize_controls_print_styles', 'wre_load_admin_scripts' );