<?php
/**
 * Agents Listings
 *
 * This template can be overridden by copying it to yourtheme/listings/agent/listings.php.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
wre_get_agent_listings( wre_agent_ID() );