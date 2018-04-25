<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

/**
 * Type of listings to display (buy or rent).
 */
function wre_display() {
	$purpose = wre_option( 'display_purpose' );
	$default = wre_option( 'display_default' );

	$return = 'Sell'; // set default
	if( $purpose == 'both' ) {
		$return = $default ? $default : 'Sell';
	}
	if( $purpose == 'rent' ) {
		$return = 'Rent';
	}
	if( isset( $_GET['purpose'] ) && ! empty( $_GET['purpose'] ) ) {
		$return = $_GET['purpose'] == 'buy' ? 'Sell' : 'Rent';
	}
	return apply_filters( 'wre_default_display', $return );
}

/**
 * Post classes for listings.
 */
 if( !function_exists( 'wre_listing_post_class' ) ) {

	function wre_listing_post_class( $classes, $class = '', $post_id = '' ) {

		if ( ! $post_id || 'listing' !== get_post_type( $post_id ) ) {
			return $classes;
		}

		$listing = get_post( $post_id );

		if ( $listing ) {

			$classes[] = 'listing';
			$classes[] = 'listing-' . $listing->ID;

			if ( wre_meta( 'type' ) ) {
				$classes[] = strtolower( wre_meta( 'type' ) );
			}

			if ( wre_meta( 'status' ) ) {
				$classes[] = strtolower( wre_meta( 'status' ) );
			}

			if ( wre_meta( 'purpose' ) ) {
				$classes[] = strtolower( wre_meta( 'purpose' ) );
			}

			$images = wre_meta( 'image_gallery' );
			if ( $images ) {
				foreach ( $images as $key => $url ) {
					if( ! empty( $url ) ) {
						$classes[] = strtolower( 'has-thumbnail' );
						break;
					}
				}
			}

			if ( wre_meta( 'bedrooms' ) ) {
				$classes[] = 'beds-' . wre_meta( 'bedrooms' );
			}

			if ( wre_meta( 'bathrooms' ) ) {
				$classes[] = 'baths-' . wre_meta( 'bathrooms' );
			}

		}

		if ( false !== ( $key = array_search( 'hentry', $classes ) ) ) {
			unset( $classes[ $key ] );
		}

		return $classes;
	}
}
/*
 * Show Archive Page title within page content area
 */
if( !function_exists( 'wre_force_page_title' ) ) {
	function wre_force_page_title() {
		$force = wre_option( 'archives_page_title' ) ? wre_option( 'archives_page_title' ) : 'no';
		return $force;
	}
}
/*
 * Map height
 */
function wre_map_height() {
	$height = wre_option( 'map_height' ) ? wre_option( 'map_height' ) : '300';
	return apply_filters( 'wre_map_height', $height );
}

/*
 * Are we hiding an item
 */
function wre_hide_item( $item ) {
	$hide = wre_meta( 'hide' );
	if( ! $hide ) {
		return false;
	}
	return in_array( $item, $hide );
}

/*
 * Output the chosen tick
 */
function wre_tick() {
	return '<i class="wre-icon-tick-7"></i>';
}

/*
 * Get the URL of the first image of a listing
 */
function wre_get_first_image( $post_id = 0 ) {

	if( ! $post_id )
		$post_id = get_the_ID();

	$gallery = wre_meta( 'image_gallery', $post_id );

	if( empty( $gallery ) ) {
		$sml 	= apply_filters( 'wre_default_no_image', WRE_PLUGIN_URL . 'assets/images/no-image.jpg' );
		$alt 	= '';
	} else {
		$id 	= key( $gallery );
		$sml 	= wp_get_attachment_image_url( $id, 'wre-sml' );
		$alt 	= get_post_meta( $id, '_wp_attachment_image_alt', true );
	}

	return array(
		'alt' => $alt,
		'sml' => $sml,
	);
}

/*
 * Get the listing status
 */
 if( !function_exists( 'wre_get_status' ) ) {

	function wre_get_status() {

		$listing_status     = wre_meta( 'status' );
		$option_statuses    = wre_option( 'listing_status' );

		if( ! $listing_status )
			return;

		$status = null;
		foreach ($option_statuses as $option_status) {
			$status_slug = strtolower( str_replace( ' ', '-', $option_status) );
			if( $listing_status == $status_slug ) {
				$status = isset( $option_status ) ? $option_status : null;
				if( $status ) {


					$status_bg_color = '';
					$status_text_color = '';
					$status_icon_class = '';

					$bg_color = $text_color = $icon = null;

					if($status_bg_color)
						$bg_color = $status_bg_color;

					if($status_text_color)
						$text_color = $status_text_color;

					if($status_icon_class)
						$icon = $status_icon_class;
				}
			}
		}

		if( ! $status ) {
			$status 	= $listing_status;
			$bg_color 	= '#ffffff';
			$text_color = '#444444';
			$icon 		= '';
		}

		return array(
			'status'		=> $status,
			'bg_color'		=> $bg_color,
			'text_color'	=> $text_color,
			'icon'			=> $icon,
		);
	}
}
/**
 * Do we include the decimals
 * @since  1.0.0
 * @return string
 */
function wre_include_decimals() {
	return 'no';
}

/**
 * Get the price format depending on the currency position.
 *
 * @return string
 */
 if( !function_exists( 'wre_format_price_format' ) ) {

		function wre_format_price_format() {
			$currency_pos = 'left';
			$format = '%1$s%2$s';

			return apply_filters( 'wre_format_price_format', $format, $currency_pos );
		}

	}

/**
 * Return the currency_symbol for prices.
 * @since  1.0.0
 * @return string
 */
function wre_currency_symbol() {
	return '$';
}

/**
 * Return the thousand separator for prices.
 * @since  1.0.0
 * @return string
 */
function wre_thousand_separator() {
	return ',';
}

/**
 * Return the decimal separator for prices.
 * @since  1.0.0
 * @return string
 */
function wre_decimal_separator() {
	return '.';
}

/**
 * Return the number of decimals after the decimal point.
 * @since  1.0.0
 * @return int
 */
function wre_decimals() {
	return 2;
}

/**
 * Trim trailing zeros off prices.
 *
 * @param mixed $price
 * @return string
 */
function wre_trim_zeros( $price ) {
	return preg_replace( '/' . preg_quote( wre_decimal_separator(), '/' ) . '0++$/', '', $price );
}

/**
 * Format the price with a currency symbol.
 *
 * @param float $price
 * @param array $args (default: array())
 * @return string
 */
function wre_format_price( $price, $args = array() ) {
	extract( apply_filters( 'wre_format_price_args', wp_parse_args( $args, array(
		'currency_symbol'		=> wre_currency_symbol(),
		'decimal_separator'		=> wre_decimal_separator(),
		'thousand_separator'	=> wre_thousand_separator(),
		'decimals'				=> wre_decimals(),
		'price_format'			=> wre_format_price_format(),
		'include_decimals'		=> wre_include_decimals()
	) ) ) );

	$return = null;
	if( $price != 0 ) {
		$negative	= $price < 0;
		$price		= apply_filters( 'wre_raw_price', floatval( $negative ? $price * -1 : $price ) );
		$price		= apply_filters( 'wre_formatted_price', number_format( $price, $decimals, $decimal_separator, $thousand_separator ), $price, $decimals, $decimal_separator, $thousand_separator );

		if ( $include_decimals == 'no' ) {
			$price = wre_trim_zeros( $price );
		}

		$formatted_price = ( $negative ? '-' : '' ) . sprintf( $price_format, '<span class="currency-symbol">' . $currency_symbol . '</span>', $price );
		$return = '<span class="price-amount">' . $formatted_price . '</span>';
	}

	return apply_filters( 'wre_format_price', $return, $price, $args );
}

/**
 * Format the price with a currency symbol.
 *
 * @param float $price
 * @param array $args (default: array())
 * @return string
 */
function wre_raw_price( $price ) {
	return strip_tags( wre_format_price( $price ) );
}

/*
 * Outputs the price HTML
 */
function wre_price( $price ) {
	$suffix = wre_meta( 'price_suffix' );
	return wre_format_price( $price ) . ' ' . $suffix;
}

add_filter( 'cmb2_override__wre_listing_agent_meta_save', 'wre_cpt_author_meta_save_override', 0, 4 );
/**
 * Override CPT author meta save in order to store as post author
 */
function wre_cpt_author_meta_save_override( $override, $data_args, $args, $field ) {
	// Checks to avoid infinite loops
	// @link	https://codex.wordpress.org/Function_Reference/wp_update_post#Caution_-_Infinite_loop
	if ( ! wp_is_post_revision( $data_args['id'] ) ) {
		// Remove filter to avoid loop
		remove_filter( 'cmb2_override__wre_listing_agent_meta_save', 'wre_cpt_author_meta_save_override', 0 );
		// Update post author
		// Will return non-null value to short-circuit normal meta save

		$prev_listing_author = get_post_meta( $data_args['id'], '_wre_listing_agent', true );
		$prev_author_listings = get_user_meta( $prev_listing_author, '_wre_listing_ids', true);
		if( !empty( $prev_author_listings ) && in_array( $data_args['id'], $prev_author_listings ) ) {
			if(($key = array_search($data_args['id'], $prev_author_listings)) !== false) {
				unset($prev_author_listings[$key]);
			}
			if( empty($prev_author_listings) )
				$prev_author_listings = '';

			update_user_meta( $prev_listing_author, '_wre_listing_ids', $prev_author_listings );
		}
		$override = wp_update_post( array(
			'ID'			=> $data_args['id'],
			'post_author'	=> $data_args['value'],
		));

		$current_author_listings = get_user_meta( $data_args['value'], '_wre_listing_ids', true);
		if( ! is_array( $current_author_listings ) )
			$current_author_listings = array();

		array_push($current_author_listings, $data_args['id']);
		update_user_meta( $data_args['value'], '_wre_listing_ids', $current_author_listings );
		// Add filter back
		add_filter( 'cmb2_override__wre_listing_agent_meta_save', 'wre_cpt_author_meta_save_override', 0 );
	}
	return $override;
}

function wre_default_display_mode() {
	return $default_listing_mode = wre_option( 'wre_default_display_mode' ) ? wre_option( 'wre_default_display_mode' ) : 'grid-view';
}

function wre_default_posts_number() {
	return $default_listing_number = wre_option( 'archive_listing_number' ) ? wre_option( 'archive_listing_number' ) : 9;
}

function wre_default_grid_columns() {
	return $default_columns = wre_option( 'wre_grid_columns' ) ? wre_option( 'wre_grid_columns' ) : 3;
}
