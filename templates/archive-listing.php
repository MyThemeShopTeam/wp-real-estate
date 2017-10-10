<?php
/**
 * The Template for displaying the listings archive
 *
 * This template can be overridden by copying it to yourtheme/listings/archive-listing.php.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

get_header( 'listings' ); 

	/**
	 * @hooked wre_output_content_wrapper (outputs opening divs for the content)
	 *
	 */
	do_action( 'wre_before_main_content' );

		/**
		 * @hooked wre_listing_archive_description (displays any content, including shortcodes, within the main content editor of your chosen listing archive page)
		 *
		 */
		do_action( 'wre_archive_page_content' );

		if ( have_posts() ) :

			/**
			 * @hooked wre_ordering (the ordering dropdown)
			 * @hooked wre_pagination (the pagination)
			 *
			 */
			do_action( 'wre_before_listings_loop' );

			echo '<ul class="wre-items">';
				while ( have_posts() ) : the_post();

					wre_get_part( 'content-listing.php' );

				endwhile;
			echo '</ul>';

			/**
			 * @hooked wre_pagination (the pagination)
			 *
			 */
			do_action( 'wre_after_listings_loop' );

		else : ?>

			<p class="alert wre-no-results"><?php _e( 'Sorry, no listings were found.', 'wp-real-estate' ); ?></p>

		<?php endif;

	/**
	 * @hooked wre_output_content_wrapper_end (outputs closing divs for the content)
	 *
	 */
	do_action( 'wre_after_main_content' );
	do_action( 'wre_sidebar' );
get_footer( 'listings' );