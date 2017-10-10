<?php
/**
 * Single agent avatar
 *
 * This template can be overridden by copying it to yourtheme/listings/agent/avatar.php.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

wre_agent_avatar_data( wre_agent_ID() );
