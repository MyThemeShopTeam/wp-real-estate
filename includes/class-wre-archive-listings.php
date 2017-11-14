<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

class WRE_Archive_Listings extends WRE_Search {

	public function __construct() {
		add_filter('wp', array($this, 'has_shortcode'));
		add_shortcode('wre_archive_listings', array($this, 'wre_archive_listings'));
	}

	/**
	 * Check if we have the shortcode displayed
	 */
	public function has_shortcode() {
		global $post;
		if (is_a($post, 'WP_Post') && has_shortcode($post->post_content, 'wre_archive_listings')) {
			add_filter('is_wre', array($this, 'is_wre'));
		}
	}

	/**
	 * Add this as a listings_wp page
	 *
	 * @param bool $return
	 * @return bool
	 */
	public function is_wre($return) {
		return true;
	}

	public function wre_archive_listings() {
		
		if( !  wre_is_theme_compatible() ) return;
		
		ob_start();
		$archive_listings = $this->build_query();

		/**
		 * @hooked wre_output_content_wrapper (outputs opening divs for the content)
		 *
		 */
		do_action('wre_before_main_content');

		/**
		 * @hooked wre_listing_archive_description (displays any content, including shortcodes, within the main content editor of your chosen listing archive page)
		 *
		 */
		do_action('wre_archive_page_content');
		if ($archive_listings->have_posts()) :

			/**
			 * @hooked wre_ordering (the ordering dropdown)
			 * @hooked wre_pagination (the pagination)
			 *
			 */
			do_action('wre_before_listings_loop');
			$default_listing_mode = wre_default_display_mode();
			echo '<div id="wre-archive-wrapper"><ul class="wre-items ' . esc_attr( $default_listing_mode ) . '">';
				while ($archive_listings->have_posts()) : $archive_listings->the_post();

					wre_get_part('content-listing.php');

				endwhile;

			echo '</ul>';
			echo '<div class="wre-orderby-loader"><img src="'. WRE_PLUGIN_URL .'assets/images/loading.svg" /></div>';
			echo '</div>';

			/**
			 * @hooked wre_pagination (the pagination)
			 *
			 */
			do_action('wre_after_listings_loop');

		else :
			?>

			<p class="alert wre-no-results"><?php _e('Sorry, no listings were found.', 'wp-real-estate'); ?></p>

		<?php
		endif;
		return ob_get_clean();
	}

	/**
	 * The shortcode
	 *
	 * @param array $atts
	 * @return string
	 */
	public function build_query() {
		$paged = ( get_query_var('paged') );
		if ( ! $paged && isset( $_GET['paged'] ) && $_GET['paged'] != '' ) {
			$paged = $_GET['paged'];
		} else if( $paged == 0 ) {
			$paged = 1;
		}
		$posts_per_page = wre_default_posts_number();
		$query_args = array(
			'post_type' => 'listing',
			'posts_per_page' => $posts_per_page,
			'post_status' => 'publish',
			'paged' => $paged,
		);

		$purpose_query[] = array(
			'key' => '_wre_listing_purpose',
			'value' => wre_display(),
			'compare' => 'LIKE'
		);
		$meta_query = array();
		$beds_query[] = self::beds_meta_query();
		$price_query[] = self::price_meta_query();
		$type_query[] = self::type_meta_query();
		$radius_query[] = self::radius_query('');
		$keyword_query[] = self::keyword_query('');
		$ordering = self::get_ordering_args();
		$query_args['orderby'] = $ordering['orderby'];
		$query_args['order'] = $ordering['order'];
		if (isset($ordering['meta_key'])) {
			$query_args['meta_key'] = $ordering['meta_key'];
		}

		// this should be always set
		// purpose AND Bedrooms AND price AND type
		$query_1 = array_merge($purpose_query, $beds_query, $price_query);

		// within radius AND purpose AND Bedrooms AND price AND type
		$query_2 = array_merge($radius_query, $keyword_query);

		// if no keyword
		if (isset($_GET['location']) && empty($_GET['location'])) {
			$query_1['relation'] = 'AND';
			$meta_query[] = $query_1;
		}

		// if keyword
		if (isset($_GET['location']) && !empty($_GET['location'])) {
			$query_2['relation'] = 'OR';
			$meta_query[] = $query_1;
			$meta_query[] = $query_2;
			$meta_query['relation'] = 'AND';
		}
		if (isset($_GET['type']) && !empty($_GET['type'])) {
			$query_args['tax_query'] = $type_query;
		}

		$query_args['meta_query'] = $meta_query;

		return $archive_listings = new WP_Query($query_args);
	}

		}

return new WRE_Archive_Listings();