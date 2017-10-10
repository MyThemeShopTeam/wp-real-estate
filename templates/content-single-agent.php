<?php
/**
 * The Template for displaying agent content in the agent.php template
 *
 * This template can be overridden by copying it to yourtheme/listings/content-single-agent.php.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

wre_get_single_agent_data( wre_agent_ID() );