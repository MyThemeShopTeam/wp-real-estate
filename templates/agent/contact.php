<?php
/**
 * Single agent avatar
 *
 * This template can be overridden by copying it to yourtheme/listings/agent/avatar.php.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

wre_get_agent_contact_details( wre_agent_ID() );
