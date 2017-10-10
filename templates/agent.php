<?php
/**
 * The Template for displaying all single listings
 *
 * This template can be overridden by copying it to yourtheme/listings/single-listing.php.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

get_header( 'listings' );

	/**
	 * @hooked wre_output_content_wrapper (outputs opening divs for the content)
	 */
	do_action( 'wre_before_main_content' );

		wre_get_part( 'content-single-agent.php' );

	/**
	 * @hooked wre_output_content_wrapper_end (outputs closing divs for the content)
	 */
	do_action( 'wre_after_main_content' );

	do_action( 'wre_sidebar' );

get_footer( 'listings' );