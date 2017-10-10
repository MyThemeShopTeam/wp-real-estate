<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

/**
 * Returns the listing statuses as set in the options.
 *
 * @return array		
 */
function wre_listing_statuses() {
	$option = get_option('wre_options');
	$statuses = isset($option['listing_status']) ? $option['listing_status'] : '';
	$array = array();
	if ($statuses) {
		foreach ($statuses as $status) {
			$status_slug = strtolower(str_replace(' ', '-', $status));
			$array[$status_slug] = $status;
		}
	}
	return $array;
}

/**
 * Returns the listing internal features as set in the options.
 *
 * @return array
 */
function wre_listing_internal_features() {
	$option = get_option('wre_options');
	$datas = isset($option['internal_feature']) ? $option['internal_feature'] : '';
	$array = array();
	if ($datas) {
		foreach ($datas as $data) {
			$array[$data] = $data;
		}
	}
	return $array;
}

/**
 * Returns the listing external features as set in the options.
 *
 * @return array		
 */
function wre_listing_external_features() {
	$option = get_option('wre_options');
	$datas = isset($option['external_feature']) ? $option['external_feature'] : '';
	$array = array();
	if ($datas) {
		foreach ($datas as $data) {
			$array[$data] = $data;
		}
	}
	return $array;
}

/**
 * Returns array of all agents.
 * For use in dropdowns.
 */
function wre_admin_get_agents($field) {
	global $current_user;
	if (in_array('wre_agent', $current_user->roles)) {
		$array[$current_user->ID] = $current_user->display_name;
	} else {
		$args = apply_filters('wre_agents_as_dropdown', array(
			'role' => '',
			'role__in' => array('wre_agent', 'administrator'),
			'role__not_in' => array(),
			'meta_key' => '',
			'meta_value' => '',
			'meta_compare' => '',
			'meta_query' => array(),
			'date_query' => array(),
			'include' => array(),
			'exclude' => array(),
			'orderby' => 'display_name',
			'order' => 'ASC',
			'offset' => '',
			'search' => '',
			'number' => '',
			'count_total' => false,
			'fields' => array('display_name', 'ID'),
			'who' => '',
				));

		$agents = get_users($args);
		$array = array('' => __('No Agent', 'wp-real-estate'));
		if ($agents) {
			foreach ($agents as $agent) {
				$array[$agent->ID] = $agent->display_name;
			}
		}
	}

	return $array;
}

/**
 * Returns array of all pages.
 * For use in dropdowns.
 */
function wre_get_pages() {

	$args = array(
		'sort_order' => 'asc',
		'sort_column' => 'post_title',
		'hierarchical' => 1,
		'exclude' => '',
		'include' => '',
		'meta_key' => '',
		'meta_value' => '',
		'authors' => '',
		'child_of' => 0,
		'parent' => -1,
		'exclude_tree' => '',
		'number' => '',
		'offset' => 0,
		'post_type' => 'page',
		'post_status' => 'publish'
	);

	$pages = get_pages($args);
	$array = array();
	if ($pages) {
		foreach ($pages as $page) {
			$array[$page->ID] = $page->post_title;
		}
	}

	return $array;
}

/**
 * Output the map on the admin edit listing
 * @param  object $field_args Current field args
 * @param  object $field      Current field object
 */
function wre_admin_map($field_args, $field) {
	?>

	<div class="cmb-th"></div>
	<div class="cmb-td">
		<button id="wre-find" type="button" class="button button-small"><?php _e('Find', 'wp-real-estate'); ?></button>
		<button id="wre-reset" type="button" class="button button-small"><?php _e('Reset', 'wp-real-estate'); ?></button>
	</div>

	<div class="cmb-th"></div>
	<div class="cmb-td">
		<div class="wre-admin-map" style="height:220px"></div>
		<p class="cmb2-metabox-description map-desc"><?php _e('Modify the marker\'s position by dragging it.', 'wp-real-estate'); ?></p>
	</div>

	<?php
}

/**
 * Output the archive button
 * @param  object $field_args Current field args
 * @param  object $field      Current field object
 */
function wre_admin_status_area($field_args, $field) {

	$post_id = $field->object_id;
	$enquiries = wre_meta('enquiries', $field->object_id);
	$count = !empty($enquiries) ? count($enquiries) : 0;
	$latest = is_array($enquiries) ? end($enquiries) : null;

	// listing enquiries section
	echo '<div class=""listing-enquiries>';
	echo '<span class="dashicons dashicons-admin-comments"></span> <a target="_blank" href="' . esc_url(admin_url('edit.php?post_type=listing-enquiry&listings=' . $post_id)) . '"><span>' . sprintf(_n('%s Enquiry', '%s Enquiries', $count, 'wp-real-estate'), $count) . '</a></span>';

	if ($latest) {
		echo '<p class="cmb2-metabox-description most-recent">' . __('Most Recent:', 'wp-real-estate') . ' ' . sprintf(_x('%s ago', '%s = human-readable time difference', 'wp-real-estate'), human_time_diff(get_the_date('U', $latest), current_time('timestamp'))) . '</p>';
	}
	echo '</div>';

	if ('archive' !== get_post_status($post_id)) {
		// archive button
		$button = ' <button id="archive-listing" type="button" class="button button-small">' . __('Archive This Listing', 'wp-real-estate') . '</button>';

		echo $button;
	} else {
		echo '<div class="archived-text warning">' . __('This listing is archived.', 'wp-real-estate') . '<br>' . __('It is no longer visible on the front end.', 'wp-real-estate') . '<br>' . __('Hit the Publish button to un-archive it.', 'wp-real-estate') . '</div>';
	}
	?>

	<script type="text/javascript" >

		jQuery(document).ready(function ($) {

			$("#archive-listing").click(function () {
				var btn = $(this);
				var data = {
					'action': 'wre_ajax_archive_listing',
					'post_id': <?php echo (int) $post_id; ?>,
					'nonce': '<?php echo wp_create_nonce('wre-archive-' . $post_id); ?>',
				};

				// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
				$.post(ajaxurl, data, function (response) {

					var obj = $.parseJSON(response);

					$(btn).hide();
					$(btn).after('<div class="archived-text ' + obj.result + '">' + obj.string + '</div>');

					// change the select input to be archived (in case listing is updated after our actions)
					$('#post-status-display').text('<?php esc_html_e('Archived', 'wp-real-estate') ?>');

				});

			});

		});
	</script>

	<?php
}

// Ajax Handler for archiving a listings
add_action('wp_ajax_wre_ajax_archive_listing', 'wre_ajax_archive_listing');

function wre_ajax_archive_listing() {
	// Get the Post ID
	$post_id = (int) $_REQUEST['post_id'];
	$response = false;

	// Proceed, again we are checking for permissions
	if (wp_verify_nonce($_REQUEST['nonce'], 'wre-archive-' . $post_id)) {

		$updated = wp_update_post(array(
			'ID' => $post_id,
			'post_status' => 'archive'
		));

		if (is_wp_error($updated)) {
			$response = false;
		} else {
			$response = true;
		}
	}

	if ($response == true) {
		$return = array(
			'string' => __('This listing is now archived. It is no longer visible on the front end.', 'wp-real-estate'),
			'result' => 'warning'
		);
	} else {
		$return = array(
			'string' => __('There was an error archiving this listing', 'wp-real-estate'),
			'result' => 'error'
		);
	}

	// Whatever the outcome, send the Response back
	echo json_encode($return);

	// Always exit when doing Ajax
	exit();
}