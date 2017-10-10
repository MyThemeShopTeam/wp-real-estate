<?php
/**
 * Single listing address
 *
 * This template can be overridden by copying it to yourtheme/listings/single-listing/address.php.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$agent_id = wre_meta( 'agent' );

if( empty( $agent_id ) )
	return;

$phone		= get_the_author_meta( 'phone', $agent_id );
$mobile		= get_the_author_meta( 'mobile', $agent_id );
$email		= get_the_author_meta( 'email', $agent_id );
$website	= get_the_author_meta( 'url', $agent_id );
$agent_url = get_author_posts_url($agent_id);
?>

<div class="agent">

	<h2 class="widget-title"><?php esc_html_e( 'Agent Details', 'wp-real-estate' ); ?></h2>

	<div class="avatar-wrap">
		<a href="<?php echo esc_url( $agent_url ); ?>">
			<?php echo get_avatar( $agent_id, 100 ); ?>
		</a>
	</div>

	<h4 class="name">
		<a href="<?php echo esc_url( $agent_url ); ?>">
			<?php esc_html( the_author_meta( 'display_name', $agent_id ) ); ?>
		</a>
	</h4>

	<ul class="contact">

		<?php if( $website ) { ?>
				<li class="website"><i class="wre-icon-website"></i><?php echo esc_html( $website ); ?></li>
		<?php } ?>

		<?php if( $email ) { ?>
				<li class="email"><i class="wre-icon-email"></i><?php echo esc_html( $email ); ?></li>
		<?php } ?>

		<?php if( $phone ) { ?>
				<li class="phone"><i class="wre-icon-old-phone"></i><?php echo esc_html( $phone ); ?></li>
		<?php } ?>

		<?php if( $mobile ) { ?>
				<li class="mobile"><i class="wre-icon-phone"></i><?php echo esc_html( $mobile ); ?></li>
		<?php } ?>

	</ul>

</div>