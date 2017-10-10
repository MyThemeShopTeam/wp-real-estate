<?php
/**
 * Single listing social share
 *
 * This template can be overridden by copying it to yourtheme/listings/single-listing/social-share.php.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$listing_id = wre_get_ID();
$permalink = get_the_permalink($listing_id);
$title = get_the_title( $listing_id );
$image = wre_get_first_image( $listing_id );
?>

<div class="wre-share-networks clearfix">
	<span class="share-label"><?php _e( 'Share this', 'wp-real-estate' ); ?></span>
	<a href="//www.facebook.com/share.php?m2w&s=100&p[url]=<?php echo urlencode($permalink); ?>&p[title]=<?php echo urlencode($title); ?>&u=<?php echo urlencode( $permalink ); ?>&t=<?php echo urlencode( $title ); ?>" class="wre-fb-share" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;">
		<i class="wre-icon-facebook"></i>
		<?php _e( 'Facebook', 'wp-real-estate' ); ?>
	</a>
	<a class="wre-twitter-share" href="https://twitter.com/intent/tweet?original_referer=<?php echo urlencode($permalink); ?>&text=<?php echo $title; ?>&url=<?php echo urlencode($permalink); ?>" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;">
		<i class="wre-icon-twitter"></i>
		<?php _e( 'Twitter', 'wp-real-estate' ); ?>
	</a>
	
	<a href="//plus.google.com/share?url=<?php echo urlencode($permalink); ?>" class="wre-google-share" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;">
		<i class="wre-icon-gplus"></i>
		<?php _e( 'Google', 'wp-real-estate' ); ?>
	</a>
	
	<a href="//pinterest.com/pin/create/button/?url=<?php echo urlencode($permalink); ?>&media=<?php echo urlencode( $image['sml'] ); ?>" class="wre-pinterest-share" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;">
		<i class="wre-icon-pinterest"></i>
		<?php _e( 'Pin it', 'wp-real-estate' ); ?>
	</a>

</div>