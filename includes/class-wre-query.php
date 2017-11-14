<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class WRE_Query {

	/**
	 * Get things going
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		if ( ! is_admin() ) {
			add_action( 'pre_get_posts', array( $this, 'pre_get_posts' ) );
			add_action( 'wp', array( $this, 'remove_listing_query' ) );
			add_action( 'wp', array( $this, 'remove_ordering_args' ) );
		}
	}


	/**
	 * Hook into pre_get_posts to do the main listing query.
	 *
	 * @param mixed $query Query object.
	 */
	public function pre_get_posts( $query ) {

		// Don't modify any other queries.
		if ( ! $query->is_post_type_archive( 'listing' ) ) {
			return;
		}

		// We only want to affect the main query.
		if ( ! $query->is_main_query() ) {
			return;
		}

		$this->listings_query( $query );

		// And remove the pre_get_posts hook.
		$this->remove_listing_query();
	}

	/**
	 * Query the listings, applying sorting/ordering etc. This applies to the main wordpress loop.
	 *
	 * @param mixed $q
	 */
	public function listings_query( $q ) {

		$q->set( 'post_status', 'publish' );
		$q->set( 'posts_per_page', wre_default_posts_number() );
		// Ordering query vars
		$ordering  = $this->get_ordering_args();
		$q->set( 'orderby', $ordering['orderby'] );
		$q->set( 'order', $ordering['order'] );
		if ( isset( $ordering['meta_key'] ) ) {
			$q->set( 'meta_key', $ordering['meta_key'] );
		}

		// get default as either rent or sell. We don't want both at the same time
		// the search query can modify this later if need be
		$meta_query[] = array(
			'key'		=> '_wre_listing_purpose',
			'value'		=> wre_display(),
			'compare'	=> 'LIKE',
		);
		$q->set( 'meta_query', $meta_query );

	}


	/**
	 * Returns an array of arguments for ordering listings based on the selected values.
	 *
	 * @access public
	 * @return array
	 */
	public function get_ordering_args( $orderby = '', $order = '' ) {
		global $wpdb;

		// Get ordering from query string unless defined
		if ( ! $orderby ) {
			$orderby_value = isset( $_GET['wre-orderby'] ) ? esc_html( $_GET['wre-orderby'] ) : 'date';

			// Get order + orderby args from string
			$orderby_value = explode( '-', $orderby_value );
			$orderby       = esc_attr( $orderby_value[0] );
			$order         = ! empty( $orderby_value[1] ) ? $orderby_value[1] : $order;
		}

		$orderby = strtolower( $orderby );
		$order   = strtoupper( $order );
		$args    = array();

		// default - menu_order
		$args['orderby']  = 'date ID';
		$args['order']    = $order == 'OLD' ? 'ASC' : 'DESC';
		$args['meta_key'] = '';

		switch ( $orderby ) {

			case 'date' :
				$args['orderby']  = 'date ID';
				$args['order']    = $order == 'OLD' ? 'ASC' : 'DESC';
			break;
			case 'price' :
				$args['orderby']  = "meta_value_num ID";
				$args['order']    = $order == 'HIGH' ? 'DESC' : 'ASC';
				$args['meta_key'] = '_wre_listing_price';
			break;

		}

		return apply_filters( 'wre_get_ordering_args', $args );
	}

	/**
	 * Remove the query.
	 */
	public function remove_listing_query() {
		remove_action( 'pre_get_posts', array( $this, 'pre_get_posts' ) );
	}

	/**
	 * Remove ordering queries.
	 */
	public function remove_ordering_args() {}

}

return new WRE_Query();
