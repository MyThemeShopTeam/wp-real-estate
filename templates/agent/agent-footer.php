<?php
/**
 * Single agent listings
 *
 * This template can be overridden by copying it to yourtheme/listings/agent/agent-footer.php.
 */
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

wre_get_agent_footer_data( wre_agent_ID() );
