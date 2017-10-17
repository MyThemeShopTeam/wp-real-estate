<?php
/**
 * The Template for displaying listing content in the single-listing.php template
 *
 * This template can be overridden by copying it to yourtheme/listings/content-single-listing.php.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

do_action( 'wre_before_single_listing' );

	if ( post_password_required() ) {
		echo get_the_password_form();
		return;
	}

	$hide_sidebar = wre_option('wre_hide_in_content_sidebar');
	$main_class = '';
	if($hide_sidebar == 'yes') {
		$main_class = 'full-width';
	}
?>

	<div id="listing-<?php the_ID(); ?>" class="wre-single listing">

		<div class="main-wrap <?php echo esc_attr( $main_class ); ?>" itemscope itemtype="http://schema.org/House">

			<?php
			$images = wre_meta( 'image_gallery' );
			if($images) :
			?>
				<div class="image-gallery">
					<?php
					/**
					 * @hooked wre_template_single_gallery
					 */
					do_action( 'wre_single_listing_gallery' );
					?>
				</div>
			<?php endif; ?>
			<div class="summary">
				<?php
				/**
				 * @hooked wre_template_single_title
				 * @hooked wre_template_single_price
				 * @hooked wre_template_single_at_a_glance
				 * @hooked wre_template_single_address
				 * @hooked wre_template_single_sizes
				 * @hooked wre_template_single_open_for_inspection
				 */
				do_action( 'wre_single_listing_summary' );
				?>
			</div>

			<div class="content">
				<?php
				/**
				 * @hooked wre_template_single_tagline
				 * @hooked wre_template_single_description
				 * @hooked wre_template_single_internal_features
				 * @hooked wre_template_single_external_features
				 */
				do_action( 'wre_single_listing_content' );
				?>
			</div>

		</div>
		<?php if( $hide_sidebar != 'yes' ) { ?>
			<div class="wre-sidebar">
				<?php
				/**
				 * @hooked wre_template_single_map
				 * @hooked wre_template_single_agent_details
				 * @hooked wre_template_single_contact_form
				 */
				do_action( 'wre_single_listing_sidebar' );
				?>
			</div>
		<?php } ?>

		<?php do_action( 'wre_single_listing_bottom' ); ?>
	</div>

<?php do_action( 'wre_after_single_listing' );