<?php
/**
 * Loop description
 *
 * This template can be overridden by copying it to yourtheme/listings/loop/description.php.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$description = wre_meta( 'main_description' );
if( empty( $description ) )
	return;

$trimmed = wp_trim_words( $description, 20, '...' );
?>

<div class="description"><?php echo wp_kses_post( $trimmed ); ?></div>