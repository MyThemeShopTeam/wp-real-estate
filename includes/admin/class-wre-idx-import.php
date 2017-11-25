<?php
/**
 * This file contains the methods for interacting with the IDX API
 * to import listing data
 */
if (!defined('ABSPATH'))
	exit;

class WRE_Idx_Listing {

	public $_idx;

	public function __construct() {}

	/**
	 * Function to get the array key (listingID+mlsID) 
	 * @param  [type] $array  [description]
	 * @param  [type] $key    [description]
	 * @param  [type] $needle [description]
	 * @return [type]         [description]
	 */
	public static function get_key($array, $key, $needle) {
		if (!$array)
			return false;
		foreach ($array as $index => $value) {
			if ($value[$key] == $needle)
				return $index;
		}
		return false;
	}

	/**
	 * Creates a post of listing type using post data from options page
	 * @param  array $listings listingID of the property
	 * @return [type] $featured [description]
	 */
	public static function wre_idx_create_post($listings) {
			
		$properties_data = wre_get_idx_properties('featured?disclaimers=true');

		if( isset($properties_data['properties']) && !empty( $properties_data['properties'] ) ) {
			
			$properties = $properties_data['properties'];
			// Load WP options
			$idx_featured_listing_wp_options = get_option('wre_idx_featured_listing_wp_options');

			update_option('wre_import_progress', true);

			if (is_array($listings) && is_array($properties)) {
				$listing_author = wre_option('wre_idx_listings_author');
				$listing_title = wre_option('wre_idx_listing_title');
				$listing_sold_status = wre_option('wre_sold_listings');
				// Loop through featured properties
				foreach ($properties as $prop) {

					// Get the listing ID
					$key = self::get_key($properties, 'listingID', $prop['listingID']);

					// Add options
					if (!in_array($prop['listingID'], $listings)) {
						$idx_featured_listing_wp_options[$prop['listingID']]['listingID'] = $prop['listingID'];
						$idx_featured_listing_wp_options[$prop['listingID']]['status'] = '';
					}

					// Unset options if they don't exist
					if (isset($idx_featured_listing_wp_options[$prop['listingID']]['post_id']) && !get_post($idx_featured_listing_wp_options[$prop['listingID']]['post_id'])) {
						unset($idx_featured_listing_wp_options[$prop['listingID']]['post_id']);
						unset($idx_featured_listing_wp_options[$prop['listingID']]['status']);
					}

					// Add post and update post meta
					if (in_array($prop['listingID'], $listings) && !isset($idx_featured_listing_wp_options[$prop['listingID']]['post_id'])) {

						if (empty($listing_title)) {
							$title_format = $properties[$key]['address'];
						} else {
							$title_format = $listing_title;
							$title_format = str_replace('address', $properties[$key]['address'], $title_format);
							$title_format = str_replace('city', $properties[$key]['cityName'], $title_format);
							$title_format = str_replace('state', $properties[$key]['state'], $title_format);
							$title_format = str_replace('zipcode', $properties[$key]['zipcode'], $title_format);
							$title_format = str_replace('listingid', $properties[$key]['listingID'], $title_format);
						}

						// Post creation options
						$opts = array(
							'post_title' => $title_format,
							'post_status' => 'publish',
							'post_type' => 'listing',
							'post_author' => $listing_author ? $listing_author : 1
						);

						// Add the post
						$add_post = wp_insert_post($opts, true);

						// Show error if wp_insert_post fails
						// add post meta and update options if success
						if (is_wp_error($add_post)) {
							$error_string = $add_post->get_error_message();
							add_settings_error('wre_idx_listing_settings_group', 'insert_post_failed', 'WordPress failed to insert the post. Error ' . $error_string, 'error');
						} elseif ($add_post) {
							$idx_featured_listing_wp_options[$prop['listingID']]['post_id'] = $add_post;
							$idx_featured_listing_wp_options[$prop['listingID']]['status'] = 'publish';
							update_post_meta($add_post, '_listing_details_url', $properties[$key]['fullDetailsURL']);
							update_post_meta($add_post, '_wre_listing_agent', $listing_author);

							self::wre_idx_insert_post_meta($add_post, $properties[$key]);
						}
					} elseif (in_array($prop['listingID'], $listings) && $idx_featured_listing_wp_options[$prop['listingID']]['status'] != 'publish') {
						self::wre_idx_change_post_status($idx_featured_listing_wp_options[$prop['listingID']]['post_id'], 'publish');
						$idx_featured_listing_wp_options[$prop['listingID']]['status'] = 'publish';
					} elseif (!in_array($prop['listingID'], $listings) && isset($idx_featured_listing_wp_options[$prop['listingID']]['status']) && $idx_featured_listing_wp_options[$prop['listingID']]['status'] == 'publish') {

						// Change to draft or delete listing if the post exists but is not in the listing array based on settings
						if ($listing_sold_status && $listing_sold_status == 'draft_sold') {
							// Change to draft
							self::wre_idx_change_post_status($idx_featured_listing_wp_options[$prop['listingID']]['post_id'], 'draft');
							$idx_featured_listing_wp_options[$prop['listingID']]['status'] = 'draft';
						} elseif ($listing_sold_status && $listing_sold_status == 'delete_sold') {

							$idx_featured_listing_wp_options[$prop['listingID']]['status'] = 'deleted';
							// Delete gallery images
							$post_gallery_images = get_post_meta($idx_featured_listing_wp_options[$prop['listingID']]['post_id'], '_wre_listing_image_gallery', true);
							if( !empty($post_gallery_images) ) {
								foreach( $post_gallery_images as $attachment_id => $post_gallery_image ) {
									wp_delete_attachment($attachment_id, true);
								}
							}

							//Delete post
							wp_delete_post($idx_featured_listing_wp_options[$prop['listingID']]['post_id']);
						}
					}
					update_option('wre_idx_featured_listing_wp_options', $idx_featured_listing_wp_options);
				}
			}

			// Lastly update our options
			delete_option('wre_import_progress');
			return $idx_featured_listing_wp_options;
		}
	}

	/**
	 * Update existing post
	 * @return true if success
	 */
	public static function wre_update_post() {
		$properties_data = wre_get_idx_properties('featured?disclaimers=true');
		// Load WP options
		$idx_featured_listing_wp_options = get_option('wre_idx_featured_listing_wp_options');

		if( isset($properties_data['properties']) && !empty( $properties_data['properties'] ) ) {
			$properties = $properties_data['properties'];
			$auto_update_listings = wre_option('wre_update_listings');
			$sold_listing = wre_option('wre_sold_listings');
			foreach ($properties as $prop) {

				$key = self::get_key($properties, 'listingID', $prop['listingID']);
				if (isset($idx_featured_listing_wp_options[$prop['listingID']]['post_id'])) {
					if (!($auto_update_listings) || isset($auto_update_listings) && $auto_update_listings != 'update_none')
						self::wre_idx_insert_post_meta($idx_featured_listing_wp_options[$prop['listingID']]['post_id'], $properties[$key], true, ($auto_update_listings == 'update_noimage') ? false : true, false);
					$idx_featured_listing_wp_options[$prop['listingID']]['updated'] = date("m/d/Y h:i:sa");
				}
			}
		}

		// Load and loop through Sold properties
		$sold_properties_data = wre_get_idx_properties('soldpending');
		if( isset($sold_properties_data['properties']) && !empty( $sold_properties_data['properties'] ) ) {
			$sold_properties = $sold_properties_data['properties'];
			foreach ($sold_properties as $sold_prop) {

				$key = self::get_key($sold_properties, 'listingID', $sold_prop['listingID']);

				if (isset($idx_featured_listing_wp_options[$sold_prop['listingID']]['post_id'])) {

					// Update property data
					self::wre_idx_insert_post_meta($idx_featured_listing_wp_options[$sold_prop['listingID']]['post_id'], $sold_properties[$key], true, ($auto_update_listings == 'update_noimage') ? false : true, true);

					if ($sold_listing && $sold_listing == 'draft_sold') {
						// Change to draft
						self::wre_idx_change_post_status($idx_featured_listing_wp_options[$sold_prop['listingID']]['post_id'], 'draft');
					} elseif ($sold_listing && $sold_listing == 'delete_sold') {
						// Delete featured image
						$post_gallery_images = get_post_meta($idx_featured_listing_wp_options[$prop['listingID']]['post_id'], '_wre_listing_image_gallery', true);
						if( !empty($post_gallery_images) ) {
							foreach( $post_gallery_images as $attachment_id => $post_gallery_image ) {
								wp_delete_attachment($attachment_id, true);
							}
						}

						//Delete post
						wp_delete_post($idx_featured_listing_wp_options[$sold_prop['listingID']]['post_id']);
					}
				}
			}
		}

		update_option('wre_idx_featured_listing_wp_options', $idx_featured_listing_wp_options);
	}

	/**
	 * Change post status
	 * @param  [type] $post_id [description]
	 * @param  [type] $status  [description]
	 * @return [type]          [description]
	 */
	public static function wre_idx_change_post_status($post_id, $status) {
		$current_post = get_post($post_id, 'ARRAY_A');
		$current_post['post_status'] = $status;
		wp_update_post($current_post);
	}

	/**
	 * Inserts post meta based on property data
	 * API fields are mapped to post meta fields
	 * prefixed with _listing_ and lowercased
	 * @param  [type] $id  [description]
	 * @param  [type] $key [description]
	 * @return [type]      [description]
	 */
	public static function wre_idx_insert_post_meta($id, $idx_featured_listing_data, $update = false, $update_image = true, $sold = false) {
		if ($sold == true) {
			$propstatus = ucfirst($idx_featured_listing_data['archiveStatus']);
		} else {
			if ($idx_featured_listing_data['propStatus'] == 'A') {
				$propstatus = 'active';
			} elseif ($idx_featured_listing_data['propStatus'] == 'S') {
				$propstatus = 'sold';
			} else {
				$propstatus = ucfirst($idx_featured_listing_data['propStatus']);
			}
		}
		$listing_purpose = 'Sell';
		// Add or reset taxonomies for property-types, locations, and status
		if( isset( $idx_featured_listing_data['idxPropType'] ) && $idx_featured_listing_data['idxPropType'] != '' ) {
			wp_set_object_terms($id, $idx_featured_listing_data['idxPropType'], 'listing-type', true);
			if( $idx_featured_listing_data['idxPropType'] == 'Rental' )
				$listing_purpose = 'Rent';
		}
		update_post_meta($id, '_wre_listing_purpose', $listing_purpose);
		// Add post meta for existing WRE fields
		if (isset($idx_featured_listing_data['listingPrice'])) {
			$listing_price = $tempValue = preg_replace('/[\$,]/', '', $idx_featured_listing_data['listingPrice']);
			$listing_price = floatval($listing_price);
			update_post_meta($id, '_wre_listing_price', $listing_price);
		}
		if ($idx_featured_listing_data['sqFt'] == 'y') {
			$hide_address = array('address');
			update_post_meta($id, '_wre_listing_hide', $hide_address);
		}
		if (isset($idx_featured_listing_data['sqFt'])) {
			$building_size = (int)str_replace(',', '', $idx_featured_listing_data['sqFt']);
			update_post_meta($id, '_wre_listing_building_size', $building_size);
			update_post_meta($id, '_wre_listing_building_unit', 'sqft');
		}
		if (isset($idx_featured_listing_data['acres'])) {
			$land_size = str_replace(',', '', $idx_featured_listing_data['acres']);
			update_post_meta($id, '_wre_listing_land_size', $land_size);
			update_post_meta($id, '_wre_listing_land_unit', 'acres');
		}
		if (isset($idx_featured_listing_data['address'])) {
			update_post_meta($id, '_wre_listing_displayed_address', sanitize_textarea_field($idx_featured_listing_data['address']));
		}
		if (isset($idx_featured_listing_data['cityName'])) {
			update_post_meta($id, '_wre_listing_city', sanitize_text_field($idx_featured_listing_data['cityName']));
		}
		if (isset($idx_featured_listing_data['country'])) {
			update_post_meta($id, '_wre_listing_country', sanitize_text_field($idx_featured_listing_data['country']));
		}
		if (isset($idx_featured_listing_data['state'])) {
			update_post_meta($id, '_wre_listing_state', sanitize_text_field($idx_featured_listing_data['state']));
		}
		if (isset($idx_featured_listing_data['zipcode'])) {
			update_post_meta($id, '_wre_listing_zip', intval($idx_featured_listing_data['zipcode']));
		}
		if (isset($idx_featured_listing_data['listingID'])) {
			update_post_meta($id, '_wre_listing_mls', sanitize_text_field($idx_featured_listing_data['listingID']));
		}
		if (isset($idx_featured_listing_data['bedrooms'])) {
			update_post_meta($id, '_wre_listing_bedrooms', sanitize_text_field($idx_featured_listing_data['bedrooms']));
		}
		if (isset($idx_featured_listing_data['totalBaths'])) {
			update_post_meta($id, '_wre_listing_bathrooms', sanitize_text_field($idx_featured_listing_data['totalBaths']));
		}
		if (isset($idx_featured_listing_data['latitude'])) {
			update_post_meta($id, '_wre_listing_lat', sanitize_text_field($idx_featured_listing_data['latitude']));
		}
		if (isset($idx_featured_listing_data['longitude'])) {
			update_post_meta($id, '_wre_listing_lng', sanitize_text_field($idx_featured_listing_data['longitude']));
		}
		if (isset($idx_featured_listing_data['remarksConcat'])) {
			update_post_meta($id, 'content', sanitize_textarea_field($idx_featured_listing_data['remarksConcat']));
		}
		if (isset($idx_featured_listing_data['idxStatus'])) {
			update_post_meta($id, '_wre_listing_status', sanitize_text_field($idx_featured_listing_data['idxStatus']));
		}

		// Add disclaimers and courtesies
		if (isset($idx_featured_listing_data['disclaimer'])) {
			foreach ($idx_featured_listing_data['disclaimer'] as $disclaimer) {
				if (in_array('details', $disclaimer)) {
					$disclaimer_logo = ($disclaimer['logoURL']) ? '<br /><img src="' . $disclaimer['logoURL'] . '" style="opacity: 1 !important; position: static !important;" />' : '';
					$disclaimer_combined = $disclaimer['text'] . $disclaimer_logo;
					update_post_meta($id, '_wre_listing_disclaimer', $disclaimer_combined);
				}
				if (in_array('widget', $disclaimer)) {
					$disclaimer_logo = ($disclaimer['logoURL']) ? '<br /><img src="' . $disclaimer['logoURL'] . '" style="opacity: 1 !important; position: static !important;" />' : '';
					$disclaimer_combined = $disclaimer['text'] . $disclaimer_logo;
					update_post_meta($id, '_wre_listing_disclaimer_widget', $disclaimer_combined);
				}
			}
		}
		if (isset($idx_featured_listing_data['courtesy'])) {
			foreach ($idx_featured_listing_data['courtesy'] as $courtesy) {
				if (in_array('details', $courtesy)) {
					update_post_meta($id, '_wre_listing_courtesy', sanitize_textarea_field($courtesy['text']));
				}
				if (in_array('widget', $courtesy)) {
					update_post_meta($id, '_wre_listing_courtesy_widget', sanitize_textarea_field($courtesy['text']));
				}
			}
		}
		/**
		 * Pull featured image if it's not an update or update image is set to true
		 */
		if ( $update_image == true && isset($idx_featured_listing_data['image']) ) {
			$images = $idx_featured_listing_data['image'];

			$value = array();
			foreach ($images as $key => $image) {
				if (isset($image['url'])) {
					$img_url = $image['url'];
					$attach_id = self::wre_check_image_exists($img_url);
					if( ! $attach_id ) {
						$attach_id = wre_download_image_file($img_url, $id);
						if( $attach_id ) {
							$newpath = get_attached_file($attach_id);
							$wp_filetype = wp_check_filetype($new_path);

							// Generate the metadata for the attachment, and update the database record.
							$attach_data = wp_generate_attachment_metadata($attach_id, $new_path);
							wp_update_attachment_metadata($attach_id, $attach_data);
						}
					}
					if( $attach_id ) {
						$value[$attach_id] = wp_get_attachment_image_src($attach_id, 'medium')[0];
					}
				}
			}
			
			update_post_meta($id, '_wre_listing_image_gallery', $value);
		}

		return true;
	}
	
	public function wre_check_image_exists($img_url) {
		preg_match('/[^\?]+\.(jpg|JPG|jpe|JPE|jpeg|JPEG|gif|GIF|png|PNG)/', $img_url, $matches);
		$filename = basename($matches[0]);
		$attachment_args = array(
			'posts_per_page' => 1,
			'post_type'      => 'attachment',
			'name'           => $filename
		);
		$attachment_check = new Wp_Query( $attachment_args );
		if ( $attachment_check->have_posts() ) {
			return $attachment_check->post->ID;
		}
		return false;
	}

}

/**
 * Admin settings page
 * Outputs clients/featured properties to import
 * Enqueues scripts for display
 * Deletes post and post thumbnail via ajax
 */
add_action('admin_menu', 'wre_idx_listing_register_menu_page', 11);

function wre_idx_listing_register_menu_page() {
	add_submenu_page('edit.php?post_type=listing', __('Import IDX Listings', 'wp-real-estate'), __('Import IDX Listings', 'wp-real-estate'), 'manage_options', 'wre-idx-listing', 'wre_idx_listing_setting_page');
	add_action('admin_init', 'wre_idx_listing_register_settings');
}

function wre_idx_listing_register_settings() {
	register_setting('wre_idx_listing_settings_group', 'wre_idx_featured_listing_options', 'wre_idx_create_post_cron');
}

/**
 * Do wp_cron job for importing listings
 */
function wre_idx_create_post_cron($listings) {
	wp_schedule_single_event( time(), 'wre_new_post_cron_hook', array($listings) );
}

add_action('wre_new_post_cron_hook', array('WRE_Idx_Listing', 'wre_idx_create_post'));

add_action('admin_enqueue_scripts', 'wre_idx_listing_scripts');

function wre_idx_listing_scripts() {
	$screen = get_current_screen();
	if ($screen->id != 'listing_page_wre-idx-listing')
		return;
	wp_enqueue_script('wre_idx_listing_delete_script', WRE_PLUGIN_URL . 'includes/admin/assets/js/wre-admin-idx-import.js', array('jquery'), true);
	wp_localize_script('wre_idx_listing_delete_script', 'DeleteListingAjax', array('ajaxurl' => admin_url('admin-ajax.php')));
}

add_action('wp_ajax_wre_idx_listing_delete', 'wre_idx_listing_delete');

function wre_idx_listing_delete() {
	$permission = check_ajax_referer('wre_idx_listing_delete_nonce', 'nonce', false);
	if ($permission == false) {
		echo 'error';
	} else {
		// Delete featured image
		$listing_images = get_post_meta( $_REQUEST['id'], '_wre_listing_image_gallery', true );
		if( !empty($listing_images) ) {
			foreach($listing_images as $attachment_id => $listing_image) {
				wp_delete_attachment($attachment_id, true);
			}
		}
		//Delete post
		wp_delete_post($_REQUEST['id']);
		echo 'success';
	}
	die();
}

add_action('wp_ajax_wre_idx_listing_delete_all', 'wre_idx_listing_delete_all');

function wre_idx_listing_delete_all() {

	$permission = check_ajax_referer('wre_idx_listing_delete_all_nonce', 'nonce', false);
	if ($permission == false) {
		echo 'error';
	} else {
		// Get listings
		$idx_featured_listing_wp_options = get_option('wre_idx_featured_listing_wp_options');

		foreach ($idx_featured_listing_wp_options as $prop) {
			if (isset($prop['post_id']) && get_post_status($prop['post_id']) != '') {
				// Delete featured image
				$listing_images = get_post_meta($prop['post_id'], '_wre_listing_image_gallery', true);
				if( !empty($listing_images) ) {
					foreach($listing_images as $attachment_id => $listing_image) {
						wp_delete_attachment($attachment_id, true);
					}
				}

				//Delete post
				wp_delete_post($prop['post_id']);
			}
		}
		echo 'success';
	}
	die();
}

function wre_idx_listing_setting_page() {
	if (get_option('wre_import_progress') == true) {
		add_settings_error('wre_idx_listing_settings_group', 'idx_listing_import_progress', 'Your listings are being imported in the background. This notice will dismiss when all selected listings have been imported.', 'updated');
	}
	$idx_featured_listing_wp_options = get_option('wre_idx_featured_listing_wp_options');
	$properties_data = wre_get_idx_properties();
	?>
	<h1><?php _e( 'Import IDX Listings', 'wp-real-estate' ); ?></h1>
	<?php
	
	if( isset($properties_data['error']) && $properties_data['error'] != '' ) {
		add_settings_error('wre_idx_listing_settings_group', 'idx_invalid_key', $properties_data['error'], 'error');
		settings_errors('wre_idx_listing_settings_group');
		return;
	}

	?>
	<p><?php _e( 'Select the listings to import.', 'wp-real-estate' ); ?></p>
	<form id="wre-idx-listing-import" method="post" action="options.php">
		<label for="wre-selectall">
			<input type="checkbox" id="wre-selectall" />
			<?php
			printf(
				'%s<br/><em>%s<strong class="error">%s</strong></em>',
				__( 'Select/Deselect All', 'wp-real-estate' ),
				__( 'If importing all listings, it may take some time.', 'wp-real-estate' ),
				__( 'Please be patient.', 'wp-real-estate' )
			);
			?>
		</label>

		<?php
		if ($idx_featured_listing_wp_options) {
			foreach ($idx_featured_listing_wp_options as $prop) {
				if (isset($prop['post_id']) && get_post_status($prop['post_id']) != '') {
					$nonce_all = wp_create_nonce('wre_idx_listing_delete_all_nonce');
					echo '<a class="wre-delete-all" href="admin-ajax.php?action=wre_idx_delete_all&nonce=' . $nonce_all . '" data-nonce="' . $nonce_all . '">'.__('Delete All Imported Listings', 'wp-real-estate').'</a>';
					echo '<img class="wre-delete-all-loader" src="'. WRE_PLUGIN_URL .'assets/images/loading.svg" />';
					break;
				}
			}
		}

		submit_button( __('Import Listings', 'wp-real-estate') );

		settings_errors('wre_idx_listing_settings_group');
		?>

		<ol id="selectable" class="wre-grid">
			<?php
			settings_fields('wre_idx_listing_settings_group');
			do_settings_sections('wre_idx_listing_settings_group');
			$properties = $properties_data['properties'];
			// No featured properties found
			if (!$properties || !empty($properties->errors)) {
				echo __('No featured properties found.', 'wp-real-estate');
				return;
			}
			echo '<div class="grid-inner-wrapper">';
				// Loop through properties
				foreach ($properties as $prop) {

					if (!isset($idx_featured_listing_wp_options[$prop['listingID']]['post_id']) || !get_post($idx_featured_listing_wp_options[$prop['listingID']]['post_id'])) {
						$idx_featured_listing_wp_options[$prop['listingID']] = array(
							'listingID' => $prop['listingID']
						);
					}

					if (isset($idx_featured_listing_wp_options[$prop['listingID']]['post_id']) && get_post($idx_featured_listing_wp_options[$prop['listingID']]['post_id'])) {
						$pid = $idx_featured_listing_wp_options[$prop['listingID']]['post_id'];
						$nonce = wp_create_nonce('wre_idx_listing_delete_nonce');
						$delete_listing = sprintf('<a href="%s" data-id="%s" data-nonce="%s" class="wre-delete-post">%s</a>', admin_url('admin-ajax.php?action=wre_idx_listing_delete&id=' . $pid . '&nonce=' . $nonce), $pid, $nonce, __('Delete', 'wp-real-estate')
						);
					}
					$listing_id = $prop['listingID'];
					$listing_status = isset($idx_featured_listing_wp_options[$prop['listingID']]['status']) ? ($idx_featured_listing_wp_options[$prop['listingID']]['status'] == 'publish' ? "imported" : '') : '';
					$listing_image = isset($prop['image']['0']['url']) ? $prop['image']['0']['url'] : WRE_PLUGIN_URL.'assets/images/no-image.jpg';
					$listing_exists = $listing_status ? ($listing_status == 'imported' ? "checked" : '') : '';
					$listing_text = $listing_status ? ($listing_status == 'imported' ? "<span class='imported'><i class='dashicons dashicons-yes'></i>Imported</span>" : '') : '';
					$listing_price = $prop['listingPrice'];
					$listing_address = $prop['address'];
					$delete_listing = $listing_status ? ($listing_status == 'imported' ? $delete_listing : '') : '';
					?>

					<div class="wre-grid-item post">
						<label for="<?php echo esc_attr($listing_id); ?>" class="wre-idx-listing">
							<li class="<?php echo esc_attr($listing_status); ?>">
								<img class="listing" src="<?php echo esc_url($listing_image); ?>" />
								<input type="checkbox" id="<?php echo esc_attr($listing_id); ?>" class="wre-checkbox" name="wre_idx_featured_listing_options[]" value="<?php echo esc_attr($listing_id); ?>" <?php echo $listing_exists; ?> />
								<?php echo $listing_text; ?>
								<p>
									<span class="price"><?php echo esc_html($listing_price); ?></span> <br />
									<span class="address"><?php echo esc_html($listing_address); ?></span> <br />
									<span class="mls"><?php _e('MLS#:', 'wp-real-estate'); ?> </span> <?php echo esc_html($listing_id); ?>
								</p>
								<?php echo $delete_listing; ?>
							</li>
						</label>
					</div>

					<?php
				}
			echo '</div>';
		echo '</ol>';
		submit_button( __('Import Listings', 'wp-real-estate') );
		?>
	</form>
	<?php
}

/**
 * Check if update is scheduled - if not, schedule it to run twice daily.
 * Schedule auto import if option checked
 * Only add if IDX plugin is installed
 * @since 2.0
 */
add_action('admin_init', 'wre_idx_update_schedule');

if (wre_option('wre_auto_import_idx_listings') && wre_option('wre_auto_import_idx_listings') == 'on') {
	add_action('admin_init', 'wre_idx_auto_import_schedule');
}

function wre_idx_update_schedule() {
	if (!wp_next_scheduled('wre_idx_update')) {
		wp_schedule_event(time(), 'twicedaily', 'wre_idx_update');
	}
}

/**
 * On the scheduled update event, run wre_update_post
 */
add_action('wre_idx_update', array('WRE_Idx_Listing', 'wre_update_post'));

/**
 * Schedule auto import task
 */
function wre_idx_auto_import_schedule() {
	if (!wp_next_scheduled('wre_idx_auto_import')) {
		wp_schedule_event(time(), 'twicedaily', 'wre_idx_auto_import');
	}
}

add_action('wre_idx_auto_import', 'wre_idx_auto_import_task');
/**
 * Get listingIDs and pass to create post cron job
 * @return void
 */
function wre_idx_auto_import_task() {
	$properties = wre_get_idx_properties();
	if( $properties ) {
		foreach ($properties as $prop) {
			$listingIDs[] = $prop['listingID'];
		}
		wre_idx_create_post_cron($listingIDs);
	}
}

function wre_get_idx_properties( $type='featured' ) {

	$idx_broker_key = wre_option('wre_idx_api_key');

	if( $idx_broker_key == '' ) {
		return array("code" => "", "error" => "Please add your IDX API key in <a href=". admin_url('edit.php?post_type=listing&page=wre_options&tab=opt-tab-idx-settings') ."> WRE Settings </a> to continue.");
	}

	if (!function_exists('curl_init')) {
		return array("code" => "PHP", "error" => "The cURL extension for PHP is not enabled on your server.<br />Please contact your developer and/or hosting provider.");
	}

	// access URL and request method
	$url = 'https://api.idxbroker.com/clients/'.$type;
	$method = 'GET';
	// headers (required and optional)
	$headers = array(
		'Content-Type: application/x-www-form-urlencoded', // required
		'accesskey: ' . $idx_broker_key,
		'outputtype: json' // optional - overrides the preferences in our API control page
	);
	// set up cURL
	$handle = curl_init();
	curl_setopt($handle, CURLOPT_URL, $url);
	curl_setopt($handle, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
	// exec the cURL request and returned information. Store the returned HTTP code in $code for later reference
	$response = curl_exec($handle);

	$response_code = curl_getinfo($handle, CURLINFO_HTTP_CODE);

	$properties = $err_message = '';
	if (is_numeric($response_code)) {
		switch ($response_code) {
			case 401:
				$err_message = 'Access key is invalid or has been revoked, please ensure there are no spaces in your key.<br />If the problem persists, please reset your API key in the <a href="https://support.idxbroker.com/customer/en/portal/articles/1911631-api-key-control">IDX Broker Dashboard</a>, or contact <a href="mailto:help@idxbroker.com?subject=IMPress for IDX Broker - Error 401">help@idxbroker.com</a>';
				break;
			case 403:
				$ip = gethostbyname(trim(`hostname`));
				$err_message = 'IP address: '.$ip.' was blocked due to violation of TOS. Contact <a href="mailto:help@idxbroker.com?subject=IMPress for IDX Broker - Error 403">help@idxbroker.com</a> with your IP to determine the reason for the block.';
				break;
			case 403.4:
				$err_message = 'API call generated from WordPress is not using SSL (HTTPS) to communicate.<br />Please contact your developer and/or hosting provider.';
				break;
			case 405:
			case 409:
				$err_message = 'Invalid request sent to IDX Broker API, please re-install the IMPress for IDX Broker plugin.';
				break;
			case 406:
				$err_message = 'Access key is missing. To obtain an access key, please visit your <a href="https://support.idxbroker.com/customer/en/portal/articles/1911631-api-key-control">IDX Broker Dashboard</a>.';
				break;
			case 412:$err_message = 'Your account has exceeded the hourly access limit for your API key.<br />You may either wait and try again later, reset your API key in the <a href="https://support.idxbroker.com/customer/en/portal/articles/1911631-api-key-control">IDX Broker Dashboard</a>, or contact <a href="mailto:help@idxbroker.com?subject=IMPress for IDX Broker - Error 412">help@idxbroker.com</a>';
				break;
			case 500:
				$err_message = 'General system error when attempting to communicate with the IDX Broker API, please try again in a few moments or contact <a href="mailto:help@idxbroker.com?subject=IMPress for IDX Broker - Error 500">help@idxbroker.com</a> if the problem persists.';
				break;
			case 503:
				$err_message = 'IDX Broker API is currently undergoing maintenance. Please try again in a few moments or contact <a href="mailto:help@idxbroker.com?subject=IMPress for IDX Broker - Error 503">help@idxbroker.com</a> if the problem persists.';
				break;
			case 200:
				$properties = json_decode($response,true);
				break;
		}
	}
	return array( "code" => $response_code,"error" => $err_message, 'properties' => $properties );
}