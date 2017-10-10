<?php
/**
 * Content wrappers
 *
 * This template can be overridden by copying it to yourtheme/listings/global/wrapper-start.php.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if( wre_option( 'opening_html' ) ) {

	echo wp_kses_post( wre_option( 'opening_html' ) );

} else {

	switch( wre_get_theme() ) {
		case 'genesis' :
			echo '<div id="primary"><div id="content" role="main" class="wre-content">';
		break;
		case 'divi' :
			echo '<div id="main-content"><div class="container wre-content"><div id="content-area" class="clearfix"><div id="left-area">';
		break;
		case 'twentyeleven' :
			echo '<div id="primary"><div id="content" role="main" class="twentyeleven wre-content">';
		break;
		case 'twentytwelve' :
			echo '<div id="primary" class="site-content"><div id="content" role="main" class="twentytwelve wre-content">';
		break;
		case 'twentythirteen' :
			echo '<div id="primary" class="site-content"><div id="content" role="main" class="entry-content twentythirteen wre-content">';
		break;
		case 'twentyfourteen' :
			echo '<div id="primary" class="content-area"><div id="content" role="main" class="site-content twentyfourteen wre-content"><div class="tfwc">';
		break;
		case 'twentyfifteen' :
			echo '<div id="primary" role="main" class="content-area twentyfifteen"><div id="main" class="site-main wre-content t15wc">';
		break;
		case 'twentysixteen' :
			echo '<div id="primary" class="content-area twentysixteen"><main id="main" class="site-main wre-content" role="main">';
		break;
		case 'twentyseventeen' :
			echo '<div class="wrap"><div id="primary" class="content-area twentyseventeen"><main id="main" class="site-main wre-content"role="main">';
		break;
		default :
			echo apply_filters( 'wre_wrapper_start', '<div id="container" class="container"><div id="content" class="content wre-content" role="main">' );
		break;
	}

}