<?php
/**
 * Plugin Name: WP Real Estate
 * Plugin URI: https://mythemeshop.com/plugins/wp-real-estate/
 * Description: A Real Estate Listings plugin for WordPress. Create a smart real estate website quickly and easily.
 * Version: 1.1.8
 * Author: MyThemesShop
 * Author URI: http://mythemeshop.com/
 * Text Domain: wp-real-estate
 * Domain Path: /languages
 *
 * @since     1.0.0
 * @copyright Copyright (c) 2013, MyThemesShop
 * @author    MyThemesShop
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

if ( ! class_exists( 'WRE' ) ) :

	/*
	 * Helper function for quick debugging
	 */
	if (!function_exists('pp')) {
		function pp( $array ) {
			echo '<pre style="white-space:pre-wrap;">';
			print_r( $array );
			echo '</pre>';
		}
	}

	/**
	 * Main WRE Class.
	 *
	 * @since 1.0.0
	 */
	final class WRE {

		/**
		 * Plugin Version
		 * @var string
		 */
		private $version = '1.1.8';

		/**
		 * @var WRE The one true WRE
		 * @since 1.0.0
		 */
		protected static $_instance = null;

		/**
		 * Query instance.
		 * @since 1.0.0
		 */
		public $query = null;

		/**
		 * Main WRE Instance.
		 * @since 1.0.0
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			self::$_instance->define_constants();
			self::$_instance->includes();
			self::$_instance->init_hooks();

			do_action( 'wre_loaded' );
			return self::$_instance;
		}

		public function __clone() {
			_doing_it_wrong( __FUNCTION__, __( 'Cheating huh?', 'wp-real-estate' ), $this->version );
		}

		public function __wakeup() {
			_doing_it_wrong( __FUNCTION__, __( 'Cheating huh?', 'wp-real-estate' ), $this->version );
		}

		/**
		 * Constructor. Intentionally left empty and public.
		 *
		 * @see instance()
		 * @since  1.0.0
		 */
		public function __construct() {}

		/**
		 * Hook into actions and filters.
		 * @since  1.0.0
		 */
		private function init_hooks() {
			add_action( 'init', array( $this, 'init' ), 0 );
			add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 2 );
		}

		/**
		 * Define Constants.
		 * @since  1.0.0
		 */
		private function define_constants() {
			$upload_dir = wp_upload_dir();
			$this->define( 'WRE_PLUGIN_FILE', __FILE__ );
			$this->define( 'WRE_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
			$this->define( 'WRE_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
			$this->define( 'WRE_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
			$this->define( 'WRE_VERSION', $this->version );
		}

		/**
		 * Define constant if not already set.
		 * @since  1.0.0
		 */
		private function define( $name, $value ) {
			if ( ! defined( $name ) ) {
				define( $name, $value );
			}
		}

		/**
		 * What type of request is this?
		 * @since  1.0.0
		 */
		private function is_request( $type ) {
			switch ( $type ) {
				case 'admin' :
					return is_admin();
				case 'frontend' :
					return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
			}
		}

		/**
		 * Include required core files used in admin and on the frontend.
		 * @since  1.0.0
		 */
		public function includes() {

			include_once( 'includes/libraries/cmb2/init.php' );
			include_once( 'includes/libraries/cmb2-grid/Cmb2GridPlugin.php' );
			include_once( 'includes/libraries/cmb2-metatabs/cmb2_metatabs_options.php' );
			include_once( 'includes/libraries/cmb2-taxonomy-master/init.php' );

			include_once( 'includes/class-wre-install.php' );
			include_once( 'includes/functions-general.php' );
			include_once( 'includes/class-wre-roles.php' );
			include_once( 'includes/class-wre-post-types.php' );
			include_once( 'includes/class-wre-post-status.php' );
			include_once( 'includes/class-wre-shortcodes.php' );
			include_once( 'includes/class-wre-query.php' );
			include_once( 'includes/class-wre-search.php' );
			include_once( 'includes/class-wre-map.php' );
			include_once( 'includes/class-wre-contact-form.php' );

			include_once( 'includes/class-wre-agent.php' );

			if ( $this->is_request( 'admin' ) ) {
				include_once( 'includes/admin/class-wre-admin.php' );
			}

			if ( $this->is_request( 'frontend' ) ) {
				include_once( 'includes/frontend/class-wre-frontend.php' );
			}

			include_once( 'includes/functions-listing.php' );
			include_once( 'includes/functions-enquiry.php' );
			include_once( 'includes/functions-agent.php' );
			include_once( 'includes/wre-widgets.php' );
			include_once( 'includes/class-wre-compare-listings.php' );

			if( wre_is_theme_compatible() && $this->is_request( 'frontend' ) ) {
				include_once( 'includes/class-wre-archive-listings.php' );
				include_once( 'includes/class-wre-agent-rewrite.php' );
			}
			include_once( 'includes/admin/class-wre-idx-import.php' );
		}

		/**
		 * Init WRE when WordPress Initialises.
		 * @since 1.0.0
		 */
		public function init() {
			// Before init action.
			do_action( 'before_wre_init' );
			// Set up localisation.
			$this->load_plugin_textdomain();

			// Load class instances.
			$this->query = new WRE_Query();

			// Init action.
			do_action( 'wre_init' );
		}

		/**
		 * Load Localisation files.
		 * @since 1.0.0
		 */
		public function load_plugin_textdomain() {

			$locale = apply_filters( 'plugin_locale', get_locale(), 'wp-real-estate' );

			load_textdomain( 'wp-real-estate', WP_LANG_DIR . '/wp-real-estate-' . $locale . '.mo' );
			load_plugin_textdomain( 'wp-real-estate', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );
		}

		/**
		 * Show row meta on the plugin screen.
		 * @since 1.0.0
		 */
		public function plugin_row_meta( $links, $file ) {

			if ( $file == WRE_PLUGIN_BASENAME ) {

				$row_meta = array(
					'docs' => '<a href="#" title="' . esc_attr( __( 'View Documentation', 'wp-real-estate' ) ) . '">' . __( 'Help', 'wp-real-estate' ) . '</a>',
					);

				return array_merge( $links, $row_meta );
			}

			return (array) $links;
		}

	}

endif;

/**
 * Main instance of WRE.
 *
 * @since  1.0.0
 * @return WRE
 */
function WRE() {
	return WRE::instance();
}

WRE();
