<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Install
 *
 * Runs on plugin install by setting up the post types, custom taxonomies,
 * flushing rewrite rules to initiate the new 'downloads' slug and also
 * creates the plugin and populates the settings fields for those plugin
 * pages. After successful install, the user is redirected to the WRE Welcome
 * screen.
 *
 * @since 1.0
 */

function wre_install( $network_wide = false ) {
	global $wpdb;

	if ( is_multisite() && $network_wide ) {

		foreach ( $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs LIMIT 100" ) as $blog_id ) {
			switch_to_blog( $blog_id );
			wre_run_install();
			restore_current_blog();
		}

	} else {
		wre_run_install();
	}

}
register_activation_hook( WRE_PLUGIN_FILE, 'wre_install' );

function wre_install_listings_page() {

	$options = get_option( 'wre_options' );

	if ( isset( $options['archives_page'] ) && ( $page_object = get_post( $options['archives_page'] ) ) ) {
		if ( 'page' === $page_object->post_type && ! in_array( $page_object->post_status, array( 'pending', 'trash', 'future', 'auto-draft' ) ) ) {
			// Valid page is already in place
			return $page_object->ID;
		}
	}

	$page = get_page_by_title( 'Listings' );

	if ( $page && has_shortcode( $page->post_content, 'wre_archive_listings' ) ) {

		$page_id = $page->ID;

		$page_status = get_post_status( $page_id );
		if( $page_status != 'publish' ) {
			$page_id = wp_update_post(array(
				'ID'    =>  $page_id,
				'post_status'   =>  'publish'
			));
		}
	} else {
		$page_content = '[wre_search] [wre_archive_listings]';
		$page_data = array(
			'post_status'		=> 'publish',
			'post_type'			=> 'page',
			'post_title'		=> 'Listings',
			'post_content'		=> $page_content,
			'comment_status'	=> 'closed',
		);
		$page_id = wp_insert_post( $page_data );
	}

	if ( $page_id ) {
		$options['archives_page'] = $page_id;
		update_option( 'wre_options', $options );
		return $page_id;
	}

}

function wre_install_agents_page() {
	
	$page = get_page_by_title( 'Agents' );

	if ( $page && has_shortcode( $page->post_content, 'wre_agents' ) ) {

		$page_id = $page->ID;

		$page_status = get_post_status( $page_id );
		if( $page_status != 'publish' ) {
			$page_id = wp_update_post(array(
				'ID'    =>  $page_id,
				'post_status'   =>  'publish'
			));
		}
	} else {
		$page_content = '[wre_agents]';
		$page_data = array(
			'post_status'		=> 'publish',
			'post_type'			=> 'page',
			'post_title'		=> 'Agents',
			'post_content'		=> $page_content,
			'comment_status'	=> 'closed',
		);
		$page_id = wp_insert_post( $page_data );
	}
}

function wre_install_agent_page() {

	$options = get_option( 'wre_options' );

	if ( isset( $options['wre_single_agent'] ) && ( $page_object = get_post( $options['wre_single_agent'] ) ) ) {
		if ( 'page' === $page_object->post_type && ! in_array( $page_object->post_status, array( 'pending', 'trash', 'future', 'auto-draft' ) ) ) {
			// Valid page is already in place
			return $page_object->ID;
		}
	}
	
	$page = get_page_by_title( 'Agent' );

	if ( $page && has_shortcode( $page->post_content, 'wre_archive_agent' ) ) {

		$page_id = $page->ID;

		$page_status = get_post_status( $page_id );
		if( $page_status != 'publish' ) {
			$page_id = wp_update_post(array(
				'ID'    =>  $page_id,
				'post_status'   =>  'publish'
			));
		}
	} else {
		$page_content = '[wre_archive_agent]';
		$page_data = array(
			'post_status'		=> 'publish',
			'post_type'			=> 'page',
			'post_title'		=> 'Agent',
			'post_content'		=> $page_content,
			'comment_status'	=> 'closed',
		);
		$page_id = wp_insert_post( $page_data );
	}

	if ( $page_id ) {
		$options['wre_single_agent'] = $page_id;
		update_option( 'wre_options', $options );
		return $page_id;
	}
}

function wre_install_compare_listings_page() {

	$options = get_option( 'wre_options' );

	if ( isset( $options['compare_listings'] ) && ( $page_object = get_post( $options['compare_listings'] ) ) ) {
		if ( 'page' === $page_object->post_type && ! in_array( $page_object->post_status, array( 'pending', 'trash', 'future', 'auto-draft' ) ) ) {
			// Valid page is already in place
			return $page_object->ID;
		}
	}

	$page = get_page_by_title( 'Compare Listings' );

	if ( $page && has_shortcode( $page->post_content, 'wre_compare_listings' ) ) {

		$page_id = $page->ID;
		$page_status = get_post_status( $page_id );
		if( $page_status != 'publish' ) {
			$page_id = wp_update_post(array(
				'ID'    =>  $page_id,
				'post_status'   =>  'publish'
			));
		}

	} else {
		$page_content = '[wre_compare_listings]';
		$page_data = array(
			'post_status'		=> 'publish',
			'post_type'			=> 'page',
			'post_title'		=> 'Compare Listings',
			'post_content'		=> $page_content,
			'comment_status'	=> 'closed',
		);
		$page_id = wp_insert_post( $page_data );
	}
	
	if ( $page_id ) {
		$options['compare_listings'] = $page_id;
		update_option( 'wre_options', $options );
		return $page_id;
	}

	
}

function wre_install_sample_listing() {

	$listings = get_posts( array('post_type' => 'listing', 'posts_per_page' => 1, 'fields' => 'ids') );
	if( ! empty( $listings ) ) return;

	$listing_title = 'My Sample Listing';
	$listing_content = '<p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt.</p><p>Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur.</p>';
	$listing_data = array(
		'post_status'		=> 'publish',
		'post_type'			=> 'listing',
		'post_title'		=> $listing_title,
		'post_content'		=> '',
		'comment_status'	=> 'closed',
	);

	$listing_id = wp_insert_post( $listing_data );
	$prefix = '_wre_listing_';
	$save_meta = array(
		$prefix . 'status' => 'under-offer',
		$prefix . 'tagline' => 'Close to everything!',
		$prefix . 'price' => '420000',
		$prefix . 'price_suffix' => 'or near offer',
		$prefix . 'purpose' => 'Sell',
		$prefix . 'bedrooms' => '4',
		$prefix . 'bathrooms' => '2',
		$prefix . 'car_spaces' => '1',
		$prefix . 'building_size' => '24',
		$prefix . 'building_unit' => 'squares',
		$prefix . 'land_size' => '3',
		$prefix . 'land_unit' => 'acres',
		$prefix . 'displayed_address' => 'Libertyville, IL 60048, USA',
		$prefix . 'route' => '',
		$prefix . 'city' => 'Libertyville',
		$prefix . 'state' => 'Illinois',
		$prefix . 'zip' => '60048',
		$prefix . 'country' => 'United States',
		$prefix . 'lat' => '42.2868698',
		$prefix . 'lng' => '-87.9432837',
		$prefix . 'agent' => get_current_user_id(),
		'content' => $listing_content,
	);

	//Save values from created array into db
	foreach( $save_meta as $meta_key => $meta_value ) {
		update_post_meta( $listing_id, $meta_key, $meta_value );
	}

	$listing_types = array( 'House', 'Unit', 'Land' );
	foreach($listing_types as $listing_type) {
		if( ! term_exists( $listing_type, 'listing-type' ) ) {
			$term = wp_insert_term( $listing_type, 'listing-type' );
		}
	}
	wp_set_object_terms( $listing_id, 'house', 'listing-type' );
}

function wre_install_data() {
	
	$options = array();
	$options['delete_data'] = 'no';
	$options['archives_page_title'] = 'no';
	$options['single_url'] = 'listing';
	$options['display_purpose'] = 'both';
	$options['internal_feature'] = array(
		'Dishwasher',
		'Open Fireplace',
	);
	$options['external_feature'] = array(
		'Balcony',
		'Tennis Court',
	);
	$options['listing_status'] = array( 'Under Offer', 'Sold', 'Active' );
		
		// Save Contact form Default Data
	$contact_message = __( 'Hi {agent_name},', 'wp-real-estate' ) . "\r\n" .
						__( 'There has been a new enquiry on <strong>{listing_title}</strong>', 'wp-real-estate' ) . "\r\n" .
						'<hr>' . "\r\n" .
						__( 'Name: {enquiry_name}', 'wp-real-estate' ) . "\r\n" .
						__( 'Email: {enquiry_email}', 'wp-real-estate' ) . "\r\n" .
						__( 'Phone: {enquiry_phone}', 'wp-real-estate' ) . "\r\n" .
						__( 'Message: {enquiry_message}', 'wp-real-estate' ) . "\r\n" .
						'<hr>';
	$options['email_from'] = get_bloginfo( 'admin_email' );
	$options['email_from_name'] = get_bloginfo( 'name' );
	$options['contact_form_email_type'] = 'html_email';
	$options['contact_form_subject'] = __( 'New enquiry on listing #{listing_id}', 'wp-real-estate' );
	$options['contact_form_message'] = $contact_message;
	$options['contact_form_error'] = __( 'There was an error. Please try again.', 'wp-real-estate' );
	$options['contact_form_success'] = __( 'Thank you, the agent will be in touch with you soon.', 'wp-real-estate' );
	$options['contact_form_include_error'] = 'yes';
	
	//Save MAP Options
	$options['map_zoom'] = '14';
	$options['map_height'] = '300';
	$options['distance_measurement'] = 'miles';
	$options['search_radius'] = '20';

	$theme_compatible = apply_filters('wre_theme_compatibility', true);
	if( $theme_compatible ) {
		$theme_compatible = 'enable';
	} else {
		$theme_compatible = 'disable';
	}
	$options['wre_theme_compatibility'] = $theme_compatible;

	$options['wre_agents_mode'] = 'list-view';
	$options['wre_archive_agents_columns'] = 3;
	$options['agents_archive_max_agents'] = 10;
	$options['agents_archive_allow_pagination'] = 'yes';

	update_option( 'wre_options', $options );

}

/**
 * Run the WRE Instsall process
 *
 * @since  1.0
 * @return void
 */
function wre_run_install() {
	
	// Setup the Listings Custom Post Type
	$types = new WRE_Post_Types;
	$types->register_post_type();

	// install data
	$wre_options = get_option('wre_options');
	if( empty( $wre_options ) ) {
		wre_install_data();
	}
	wre_install_listings_page();
	wre_install_sample_listing();
	wre_install_compare_listings_page();
	wre_install_agent_page();
	wre_install_agents_page();

	// Add Upgraded From Option
	$current_version = get_option( 'wre_version' );
	if ( $current_version ) {
		update_option( 'wre_version_upgraded_from', $current_version );
	}

	update_option( 'wre_version', WRE_VERSION );

	// Create WRE roles
	$roles = new WRE_Roles;
	$roles->add_roles();
	$roles->add_caps();

	// when upgrading
	// if ( ! $current_version ) {}

	// Bail if activating from network, or bulk
	if ( is_network_admin() || isset( $_GET['activate-multi'] ) ) {
		return;
	}

	// Add the transient to redirect
	set_transient( '_wre_activation_redirect', true, 30 );

	// Clear the permalinks
	flush_rewrite_rules( true );

}

/**
 * When a new Blog is created in multisite, see if WRE is network activated, and run the installer
 *
 * @since  1.0.0
 * @param  int    $blog_id The Blog ID created
 * @param  int    $user_id The User ID set as the admin
 * @param  string $domain  The URL
 * @param  string $path    Site Path
 * @param  int    $site_id The Site ID
 * @param  array  $meta    Blog Meta
 * @return void
 */
function wre_new_blog_created( $blog_id, $user_id, $domain, $path, $site_id, $meta ) {

	if ( is_plugin_active_for_network( plugin_basename( WRE_PLUGIN_FILE ) ) ) {
		switch_to_blog( $blog_id );
		wre_install();
		restore_current_blog();
	}

}
add_action( 'wpmu_new_blog', 'wre_new_blog_created', 10, 6 );

/**
 * Post-installation
 *
 * Runs just after plugin installation and exposes the
 * wre_after_install hook.
 *
 * @since 1.0.0
 * @return void
 */
function wre_after_install() {

	if ( ! is_admin() ) {
		return;
	}

	$activated = get_transient( '_wre_activation_redirect' );

	if ( false !== $activated ) {

		// add the default options
		//wre_add_default_listing();
		delete_transient( '_wre_activation_redirect' );

		if( ! isset( $_GET['activate-multi'] ) ) {
			set_transient( '_wre_redirected', true, 60 );
			wp_redirect( 'edit.php?post_type=listing&page=wre_options' );
			exit;
		}

	}

}
add_action( 'admin_init', 'wre_after_install' );

function wre_install_success_notice() {

	$redirected = get_transient( '_wre_redirected' );

	if ( false !== $redirected && isset( $_GET['page'] ) && $_GET['page'] == 'wre_options' ) {
		// Delete the transient
		//delete_transient( '_wre_redirected' );
		$listing_created = get_transient( '_wre_listing_created' );
		$class = 'notice notice-info is-dismissible';
		$message = '';
		$message .= '<strong>' . __( 'Success!', 'wp-real-estate' ) . '</strong>';
		if( $listing_created !== false ) {
			delete_transient( '_wre_listing_created' );
			$message .= __( ' A sample listing has been created: ', 'wp-real-estate' );
			$message .= '<a class="button button-small" target="_blank" href="' . esc_url( get_permalink( wre_option( 'archives_page' ) ) ) . '">' . __( 'View First Listing', 'wp-real-estate' ) . '</a><br><br>';
		} else {
			$message .= '<br />';
		}
		$message .= __( 'Step 1. Please go through each tab below, configure the options and <strong>hit the save button</strong>.', 'wp-real-estate' ) . '<br>';
		$message .= __( 'Step 2. Add your first Listing by navigating to <strong>Listings > New Listing</strong>', 'wp-real-estate' ) . '<br>';

		printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message ); 
	}
}
add_action( 'admin_notices', 'wre_install_success_notice' );