<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

/**
 * Work out which theme the user has active
 */
function wre_get_theme() {

	if (function_exists('et_divi_fonts_url')) {
		$theme = 'divi';
	} else if (function_exists('genesis_constants')) {
		$theme = 'genesis';
	} else {
		$theme = get_option('template');
	}
	return $theme;
}

/* =================================== Global =================================== */

/**
 * Output the start of the page wrapper.
 */
if (!function_exists('wre_output_content_wrapper')) {

	function wre_output_content_wrapper() {
		wre_get_part('global/wrapper-start.php');
	}

}

/**
 * Output the end of the page wrapper.
 */
if (!function_exists('wre_output_content_wrapper_end')) {

	function wre_output_content_wrapper_end() {
		wre_get_part('global/wrapper-end.php');
	}

}

/**
 * Output the end of the page wrapper.
 */
if (!function_exists('wre_get_sidebar')) {

	function wre_get_sidebar() {
		wre_get_part('global/sidebar.php');
	}

}

/* =================================== Single Listing =================================== */

/**
 * Output the title.
 */
if (!function_exists('wre_template_single_title')) {

	function wre_template_single_title() {
		if( wre_is_theme_compatible() ) return;
		wre_get_part('single-listing/title.php');
	}

}

/**
 * Output the address.
 */
if (!function_exists('wre_template_single_address')) {

	function wre_template_single_address() {
		if (wre_hide_item('address'))
			return;
		wre_get_part('single-listing/address.php');
	}

}

/**
 * Output the price.
 */
if (!function_exists('wre_template_single_price')) {

	function wre_template_single_price() {
		if (wre_hide_item('price'))
			return;
		wre_get_part('single-listing/price.php');
	}

}

/**
 * Output the at a glance.
 */
if (!function_exists('wre_template_single_at_a_glance')) {

	function wre_template_single_at_a_glance() {
		wre_get_part('single-listing/at-a-glance.php');
	}

}

/**
 * Output the sizes.
 */
if (!function_exists('wre_template_single_sizes')) {

	function wre_template_single_sizes() {
		wre_get_part('single-listing/sizes.php');
	}

}

/**
 * Output MLS Number.
 */
if (!function_exists('wre_template_single_mls_number')) {

	function wre_template_single_mls_number() {
		wre_get_part('single-listing/mls.php');
	}

}

/**
 * Output the gallery.
 */
if (!function_exists('wre_template_single_gallery')) {

	function wre_template_single_gallery() {
		$images = wre_meta('image_gallery');
		if (!$images)
			return;
		wre_get_part('single-listing/gallery.php');
	}

}
/**
 * Output the map.
 */
if (!function_exists('wre_template_single_map')) {

	function wre_template_single_map() {
		$key = wre_map_key();

		if (wre_hide_item('map') || !$key)
			return;

		wre_get_part('single-listing/map.php');
	}

}

/**
 * Output the tagline.
 */
if (!function_exists('wre_template_single_tagline')) {

	function wre_template_single_tagline() {
		wre_get_part('single-listing/tagline.php');
	}

}

/**
 * Output the description.
 */
if (!function_exists('wre_template_single_description')) {

	function wre_template_single_description() {
		wre_get_part('single-listing/description.php');
	}

}

/**
 * Output the open_for_inspection.
 */
if (!function_exists('wre_template_single_open_for_inspection')) {

	function wre_template_single_open_for_inspection() {
		wre_get_part('single-listing/open-for-inspection.php');
	}

}

/**
 * Output the internal_features.
 */
if (!function_exists('wre_template_single_internal_features')) {

	function wre_template_single_internal_features() {
		wre_get_part('single-listing/internal-features.php');
	}

}

/**
 * Output the external_features.
 */
if (!function_exists('wre_template_single_external_features')) {

	function wre_template_single_external_features() {
		wre_get_part('single-listing/external-features.php');
	}

}

/**
 * Output the social share.
 */
if (!function_exists('wre_template_single_social_share')) {

	function wre_template_single_social_share() {

		if (wre_hide_item('social-share'))
			return;

		wre_get_part('single-listing/social-share.php');

	}

}

/**
 * Output the agent details.
 */
if (!function_exists('wre_template_single_agent_details')) {

	function wre_template_single_agent_details() {
		$agent = wre_meta('agent');
		if (wre_hide_item('agent') || empty($agent) || $agent == '')
			return;
		wre_get_part('single-listing/agent-details.php');
	}

}

/**
 * Output the contact form.
 */
if (!function_exists('wre_template_single_contact_form')) {

	function wre_template_single_contact_form() {
		$agent = wre_meta('agent');
		if (wre_hide_item('contact_form') || empty($agent))
			return;
		wre_get_part('single-listing/contact-form.php');
	}

}

/* =================================== Archive page =================================== */

add_filter('get_the_archive_title', 'wre_listing_display_theme_title');

function wre_listing_display_theme_title($title) {
	if (is_wre_archive()) {
		$title = wre_listing_archive_get_title();
	}
	return $title;
}

if (!function_exists('wre_listing_archive_title')) {

	function wre_listing_archive_title() {

		$force = wre_force_page_title();

		if ($force != 'yes')
			return;
		?>

		<h1 class="page-title"><?php esc_html_e(wre_listing_archive_get_title()); ?></h1>

		<?php
	}

}

function wre_listing_archive_get_title() {

	// get the title we need (search page or not)
	if (is_search()) {

		$query = isset($_GET['s']) && !empty($_GET['s']) ? ' - ' . $_GET['s'] : '';
		$page_title = sprintf(__('Search Results %s', 'wp-real-estate'), esc_html($query));

		if (get_query_var('paged'))
			$page_title .= sprintf(__('&nbsp;&ndash; Page %s', 'wp-real-estate'), get_query_var('paged'));
	} elseif (is_wre_archive()) {

		$page_id = wre_option('archives_page');
		$page_title = get_the_title($page_id);
	} else {
		$page_title = get_the_title();
	}

	$page_title = apply_filters('wre_archive_page_title', $page_title);

	return $page_title;
}

/**
 * Archive page title
 *
 */
if (!function_exists('wre_page_title')) {

	function wre_page_title() {
		$page_title = wre_listing_archive_get_title();
		return $page_title;
	}

}

/**
 * Show the description on listings archive page
 */
if (!function_exists('wre_listing_archive_content')) {

	function wre_listing_archive_content() {
		if (is_post_type_archive('listing')) {
			$archive_page = get_post(wre_option('archives_page'));
			if ($archive_page) {
				$description = apply_filters('wre_format_archive_content', do_shortcode(shortcode_unautop(wpautop($archive_page->post_content))), $archive_page->post_content);
				if ($description) {
					echo '<div class="page-description">' . $description . '</div>';
				}
			}
		}
	}

}

/* =================================== Loop =================================== */

/**
 * Output listings to compare.
 */
if (!function_exists('wre_comparison')) {

	function wre_comparison() {
		wre_get_part('loop/comparison.php');
	}

}

/**
 * Output listings to compare.
 */
if (!function_exists('wre_before_listings_loop_item_wrapper')) {

	function wre_before_listings_loop_item_wrapper() {
		echo '<div class="inner-container">';
	}

}

/**
 * Output listings to compare.
 */
if (!function_exists('wre_after_listings_loop_item_wrapper')) {

	function wre_after_listings_loop_item_wrapper() {
		echo '</div>';
	}

}

/**
 * Output sorting options.
 */
if (!function_exists('wre_ordering')) {

	function wre_ordering() {
		wre_get_part('loop/orderby.php');
	}

}

/**
 * View switcher.
 */
if (!function_exists('wre_view_switcher')) {

	function wre_view_switcher() {
		wre_get_part('loop/view-switcher.php');
	}

}

/**
 * Output pagination.
 */
if (!function_exists('wre_pagination')) {

	function wre_pagination() {
		wre_get_part('loop/pagination.php');
	}

}

/**
 * Output the title.
 */
if (!function_exists('wre_template_loop_title')) {

	function wre_template_loop_title() {
		wre_get_part('loop/title.php');
	}

}

/**
 * Output the address.
 */
if (!function_exists('wre_template_loop_address')) {

	function wre_template_loop_address() {
		if (wre_hide_item('address'))
			return;
		wre_get_part('loop/address.php');
	}

}

/**
 * Output the price.
 */
if (!function_exists('wre_template_loop_price')) {

	function wre_template_loop_price() {
		if (wre_hide_item('price'))
			return;
		wre_get_part('loop/price.php');
	}

}

/**
 * Output the at a glance.
 */
if (!function_exists('wre_template_loop_at_a_glance')) {

	function wre_template_loop_at_a_glance() {
		wre_get_part('loop/at-a-glance.php');
	}

}

/**
 * Output the sizes.
 */
if (!function_exists('wre_template_loop_sizes')) {

	function wre_template_loop_sizes() {
		wre_get_part('loop/sizes.php');
	}

}

/**
 * Output the tagline.
 */
if (!function_exists('wre_template_loop_tagline')) {

	function wre_template_loop_tagline() {
		wre_get_part('loop/tagline.php');
	}

}

/**
 * Output the description.
 */
if (!function_exists('wre_template_loop_description')) {

	function wre_template_loop_description() {
		wre_get_part('loop/description.php');
	}

}

/**
 * Output the comparison button.
 */
if (!function_exists('wre_template_loop_compare')) {

	function wre_template_loop_compare() {
		$single_agent_page = wre_option( 'archives_page' );
	if( is_wre_archive() || (wre_is_theme_compatible() && is_page( $single_agent_page ) ) || defined( 'DOING_AJAX' ) )
			wre_get_part('loop/compare.php');

		return;
	}

}

/**
 * Output the image.
 */
if (!function_exists('wre_template_loop_image')) {

	function wre_template_loop_image() {
		wre_get_part('loop/image.php');
	}

}

/* =================================== Single Agent =================================== */

/**
 * Output agent avatar
 */
if (!function_exists('wre_template_agent_avatar')) {

	function wre_template_agent_avatar($agent_id) {
		if (wre_is_theme_compatible())
			wre_agent_avatar_data( $agent_id );
		else
			wre_get_part('agent/avatar.php');
	}

}
/**
 * Output agent name
 */
if (!function_exists('wre_template_agent_name')) {

	function wre_template_agent_name( $agent_id ) {
		if (wre_is_theme_compatible())
			wre_agent_name( $agent_id );
		else
			wre_get_part('agent/name.php');
	}

}
/**
 * Output agent position
 */
if (!function_exists('wre_template_agent_title_position')) {

	function wre_template_agent_title_position( $agent_id ) {
		if (wre_is_theme_compatible())
			wre_agent_title_position( $agent_id );
		else
			wre_get_part('agent/title-position.php');
	}

}
/**
 * Output agent mobile
 */
if (!function_exists('wre_template_agent_contact')) {

	function wre_template_agent_contact( $agent_id ) {
		if (wre_is_theme_compatible())
			wre_get_agent_contact_details( $agent_id );
		else
			wre_get_part('agent/contact.php');
	}

}

/**
 * Output agent social
 */
if (!function_exists('wre_template_agent_social')) {

	function wre_template_agent_social( $agent_id ) {
		if (wre_is_theme_compatible())
			wre_get_agent_social_share_data( $agent_id );
		else
			wre_get_part('agent/social.php');
	}

}
/**
 * Output agent specialties
 */
if (!function_exists('wre_template_agent_specialties')) {

	function wre_template_agent_specialties( $agent_id ) {
		if (wre_is_theme_compatible())
			wre_get_agent_specialities_data( $agent_id );
		else
			wre_get_part('agent/specialties.php');
	}

}
/**
 * Output agent awards
 */
if (!function_exists('wre_template_agent_awards')) {

	function wre_template_agent_awards( $agent_id ) {
		if (wre_is_theme_compatible())
			wre_get_agent_awards_data( $agent_id );
		else
			wre_get_part('agent/awards.php');
	}

}

/**
 * Output agent description
 */
if (!function_exists('wre_template_agent_description')) {

	function wre_template_agent_description( $agent_id ) {
		if (wre_is_theme_compatible())
			wre_get_agent_description( $agent_id );
		else
			wre_get_part('agent/description.php');
	}

}

/**
 * Output agent listings
 */
if (!function_exists('wre_template_agent_listings')) {

	function wre_template_agent_listings( $agent_id ) {
		if (wre_is_theme_compatible())
			wre_get_agent_listings( $agent_id );
		else
			wre_get_part('agent/listings.php');
	}

}

/**
 * Output agent data
 */
if (!function_exists('wre_agent_avatar_data')) {

	function wre_agent_avatar_data( $agent_id ) {
		?>
		<div class="avatar-wrap">
			<?php echo get_avatar( $agent_id, 200 ); ?>
		</div>
		<?php
	}

}

if (!function_exists('wre_get_agent_listings')) {

	function wre_get_agent_listings( $agent_id ) {
		?>
		<h2 class="listings widget-title"><?php _e( 'Agents Listings', 'wp-real-estate' ); ?></h2>
		<?php
		echo do_shortcode( '[wre_listings number="5" agent="' . $agent_id . '" compact="true"]' );
	}

}

if (!function_exists('wre_agent_name')) {

	function wre_agent_name( $agent_id ) {
		?>
		<h3 class="name" itemprop="name"><?php echo esc_html( get_the_author_meta( 'display_name', $agent_id ) ); ?></h3>
		<?php
	}

}

if (!function_exists('wre_agent_title_position')) {

	function wre_agent_title_position( $agent_id ) {
		?>
		<p class="position"><?php echo esc_html( get_the_author_meta( 'title_position', $agent_id ) ); ?></p>
		<?php
	}

}

if (!function_exists('wre_get_agent_description')) {

	function wre_get_agent_description( $agent_id ) {
		if( ! get_the_author_meta( 'description', $agent_id ) )
			return;
		?>
		<div class="description" itemprop="description"><?php echo wpautop( wp_kses_post( get_the_author_meta( 'description', $agent_id ) ) ); ?></div>
		<?php
	}

}

if (!function_exists('wre_get_agent_specialities_data')) {

	function wre_get_agent_specialities_data( $agent_id ) {
		if( ! get_the_author_meta( 'specialties', $agent_id ) )
			return;
		?>
		<div class="specialties">
			<h4><?php _e( 'Specialties', 'wp-real-estate' ); ?></h4>
			<?php echo wpautop( wp_kses_post( get_the_author_meta( 'specialties', $agent_id ) ) ); ?>
		</div>
		<?php
	}

}

if (!function_exists('wre_get_agent_awards_data')) {

	function wre_get_agent_awards_data( $agent_id ) {
		if( ! get_the_author_meta( 'awards', $agent_id ) )
			return;
		?>
		<div class="awards">
			<h4><?php _e( 'Awards', 'wp-real-estate' ); ?></h4>
			<?php echo wpautop( wp_kses_post( get_the_author_meta( 'awards', $agent_id ) ) ); ?>
		</div>
		<?php
	}

}

if (!function_exists('wre_get_agent_social_share_data')) {

	function wre_get_agent_social_share_data($agent_id) {

		if (!$agent_id)
			return;

		$facebook = get_the_author_meta('facebook', $agent_id);
		$google = get_the_author_meta('google', $agent_id);
		$twitter = get_the_author_meta('twitter', $agent_id);
		$linkedin = get_the_author_meta('linkedin', $agent_id);
		$youtube = get_the_author_meta('youtube', $agent_id);

		if ($facebook || $google || $twitter || $linkedin || $youtube) {
			?>

			<ul class="wre-social">

				<?php if ($facebook) { ?>
					<li class="facebook">
						<a href="<?php echo esc_url($facebook); ?>" target="_blank">
							<i class="wre-icon-facebook"></i>
						</a>
					</li>
				<?php } ?>

				<?php if ($google) { ?>
					<li class="google">
						<a href="<?php echo esc_url($google); ?>" target="_blank">
							<i class="wre-icon-gplus"></i>
						</a>
					</li>
				<?php } ?>

				<?php if ($twitter) { ?>
					<li class="twitter">
						<a href="<?php echo esc_url($twitter); ?>" target="_blank">
							<i class="wre-icon-twitter"></i>
						</a>
					</li>
				<?php } ?>

				<?php if ($linkedin) { ?>
					<li class="linkedin">
						<a href="<?php echo esc_url($linkedin); ?>" target="_blank">
							<i class="wre-icon-linkedin"></i>
						</a>
					</li>
				<?php } ?>

				<?php if ($youtube) { ?>
					<li class="youtube">
						<a href="<?php echo esc_url($youtube); ?>" target="_blank">
							<i class="wre-icon-youtube"></i>
						</a>
					</li>
				<?php } ?>

			</ul>

			<?php
		}
	}

}

if (!function_exists('wre_get_agent_contact_details')) {

	function wre_get_agent_contact_details($agent_id) {

		$phone = get_the_author_meta('phone', $agent_id);
		$mobile = get_the_author_meta('mobile', $agent_id);
		$email = get_the_author_meta('email', $agent_id);
		$website = get_the_author_meta('url', $agent_id);
		?>

		<ul class="contact">

			<?php if ($website) { ?>
				<li class="website">
					<a href="<?php echo esc_url($website); ?>" target="_blank"><i class="wre-icon-website"></i><?php echo esc_html($website); ?></a>
				</li>
			<?php } ?>

			<?php if ($email) { ?>
				<li class="email">
					<a href="mailto:<?php echo esc_attr($email); ?>"><i class="wre-icon-email"></i><?php echo esc_html($email); ?></a>
				</li>
			<?php } ?>

			<?php if ($phone) { ?>
				<li class="phone"><i class="wre-icon-old-phone"></i><?php echo esc_html($phone); ?></li>
			<?php } ?>

			<?php if ($mobile) { ?>
				<li class="mobile"><i class="wre-icon-phone"></i><?php echo esc_html($mobile); ?></li>
			<?php } ?>

		</ul>
		<?php
	}

}


/**
 * Output agent after bio
 */
if (!function_exists('wre_template_agent_bottom')) {

	function wre_template_agent_bottom( $agent_id ) {
		if (wre_is_theme_compatible())
			wre_get_agent_footer_data( $agent_id );
		else
			wre_get_part('agent/agent-footer.php');
	}

}

if (!function_exists('wre_get_agent_footer_data')) {

	function wre_get_agent_footer_data( $agent_id ) {
		$listing_view = wre_option('wre_agent_mode') ? wre_option('wre_agent_mode') : 'grid-view';
		$listing_columns = wre_option('wre_agent_columns') ? wre_option('wre_agent_columns') : '3';
		$max_listings = wre_option('agent_max_listings') ? wre_option('agent_max_listings') : 3;

		$args = array(
			'post_type' => 'listing',
			'post_status' => 'publish',
			'posts_per_page' => $max_listings,
			'meta_key' => '_wre_listing_agent',
			'meta_value' => wre_agent_ID(),
			'meta_compare' => '='
		);
		$agent_listings = new WP_Query( $args );
		if ( $agent_listings->have_posts() ) {
		?>
			<h3 class="related"><?php echo __('Agents Listings', 'wp-real-estate'); ?></h3>
			<ul class="listings-wp-items wre-items <?php echo esc_attr( $listing_view ); ?>">
				<?php
				while ($agent_listings->have_posts()) {
					$agent_listings->the_post();
					?>
					<li <?php post_class('col-'.$listing_columns); ?> itemscope itemtype="http://schema.org/House">
						<?php do_action('wre_before_listings_loop_item_wrapper'); ?>
							<?php do_action('wre_before_listings_loop_item_summary'); ?>

							<div class="summary">
								<?php
								do_action('wre_before_listings_loop_item');
								do_action('wre_listings_loop_item');
								do_action('wre_after_listings_loop_item');
								?>
							</div>

							<?php do_action('wre_after_listings_loop_item_summary'); ?>
						<?php do_action('wre_after_listings_loop_item_wrapper'); ?>
					</li>
			<?php
				}
				wp_reset_postdata();
			?>
			</ul>
		<?php }
	}

}

/**
 * Show the selected agent data on agent-single and [wre_agent] page
 */
if (!function_exists('wre_get_single_agent_data')) {
	function wre_get_single_agent_data( $agent_id ) {
		do_action( 'wre_before_single_agent', $agent_id );

		$show_agents_listings = wre_option('show_agents_listings') ? wre_option('show_agents_listings') : 'yes';
	?>

		<div class="wre-single agent">

			<div class="main-wrap full-width" itemscope itemtype="http://schema.org/ProfilePage">

				<div class="summary">
					<div class="wre-social-icons-wrapper">
						<?php do_action( 'wre_single_agent_intro', $agent_id ); ?>
					</div>
					<div class="agent-details-wrapper wre-agent-details">
						<?php do_action( 'wre_single_agent_summary', $agent_id ); ?>
					</div>
				</div>

				<div class="content">
					<?php do_action( 'wre_single_agent_content', $agent_id ); ?>
				</div>

				<?php if( $show_agents_listings == 'yes' ) { ?>
					<div class="bottom" itemscope itemtype="http://schema.org/House">
						<?php do_action( 'wre_single_agent_bottom', $agent_id ); ?>
					</div>
				<?php } ?>
			</div>
		</div>

	<?php do_action( 'wre_after_single_agent', $agent_id );
	}
}