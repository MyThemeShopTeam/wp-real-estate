<?php
/**
 * Single agent awards
 *
 * This template can be overridden by copying it to yourtheme/listings/agent/awards.php.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

wre_get_agent_awards_data( wre_agent_ID() );
