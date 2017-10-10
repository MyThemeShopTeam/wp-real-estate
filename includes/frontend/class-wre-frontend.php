<?php
/**
 * WRE Frontend
 *
 * @version  1.0.0
 */
// Exit if accessed directly
if (!defined('ABSPATH'))
	exit;

/**
 * WRE_Frontend class.
 */
class WRE_Frontend {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action('init', array($this, 'includes'));
		add_action('body_class', array($this, 'body_class'));
	}

	/**
	 * Include any files we need within frontend.
	 */
	public function includes() {
		if( ! wre_is_theme_compatible() )
		include_once( 'class-wre-template-loader.php' );

		include_once( 'class-wre-enqueues.php' );
		include_once( 'template-hooks.php' );
		include_once( 'template-tags.php' );
	}

	/**
	 * Add body classes for our pages.
	 *
	 * @param  array $classes
	 * @return array
	 */
	public function body_class($classes) {
		$classes = (array) $classes;

		if (is_wre()) {
			$classes[] = 'wre';
		}

		return array_unique($classes);
	}

}

return new WRE_Frontend();