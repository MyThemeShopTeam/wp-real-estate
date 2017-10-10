<?php
/**
 * Single agent name
 *
 * This template can be overridden by copying it to yourtheme/listings/agent/name.php.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$id = wre_agent_ID();
wre_get_agent_social_share_data( $id );