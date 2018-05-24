<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

add_action('cmb2_admin_init', 'wre_options_page');

function wre_options_page() {

	$listing_label = __('Listing', 'wp-real-estate');
	$listings_label = __('Listings', 'wp-real-estate');
	// the options key fields will be saved under
	$opt_key = 'wre_options';

	// the show_on parameter for configuring the CMB2 box, this is critical!
	$show_on = array('key' => 'wre-options-page', 'value' => array($opt_key));

	// an array to hold our boxes
	$boxes = array();

	// an array to hold some tabs
	$tabs = array();

	/*
	 * Tabs - an array of configuration arrays.
	 */
	$tabs[] = array(
		'id' => 'general',
		'title' => __('General', 'wp-real-estate'),
		'desc' => '',
		'boxes' => array(
			'wre_display_settings',
			'google_maps',
			'search',
		),
	);

	$tabs[] = array(
		'id' => 'listings',
		'title' => sprintf(__('%s', 'wp-real-estate'), $listings_label),
		'desc' => '',
		'boxes' => array(
			'listing_setup',
			'listing_attributes',
			'listing_features',
			'listing_statuses',
		),
	);

	$tabs[] = array(
		'id' => 'agents-listings',
		'title' => __('Agents', 'wp-real-estate'),
		'desc' => '',
		'boxes' => array(
			'agents_archive_data',
			'agents_listing_data',
		),
	);

	$tabs[] = array(
		'id' => 'contact',
		'title' => 'Contact Form',
		'desc' => '',
		'boxes' => array(
			'contact_form',
			'contact_form_email',
			'contact_form_messages',
		),
	);
	
	$tabs[] = array(
		'id' => 'idx-settings',
		'title' => 'IDX Settings',
		'desc' => '',
		'boxes' => array(
			'idx_imported_listings',
		),
	);

	$tabs[] = array(
		'id' => 'advanced',
		'title' => 'Advanced',
		'desc' => '',
		'boxes' => array(
			'template_html',
			'uninstall',
		),
	);
	
	// display-setttings
	$cmb = new_cmb2_box(array(
		'id' => 'wre_display_settings',
		'title' => __('Display Settings', 'wp-real-estate'),
		'show_on' => $show_on,
	));
	$cmb->add_field(array(
		'name' => __('Archive Display Mode', 'wp-real-estate'),
		'desc' => '',
		'id' => 'wre_default_display_mode',
		'type' => 'select',
		'default' => 'grid-view',
		'options' => array(
			'grid-view' => __('Grid Mode', 'wp-real-estate'),
			'list-view' => __('List Mode', 'wp-real-estate'),
		),
	));
	$cmb->add_field(array(
		'name' => __('Archive Grid Columns', 'wp-real-estate'),
		'desc' => __('The number of columns to display on the archive page, when viewing listings in grid mode.', 'wp-real-estate'),
		'id' => 'wre_grid_columns',
		'type' => 'select',
		'default' => '3',
		'options' => array(
			'2' => __('2 columns', 'wp-real-estate'),
			'3' => __('3 columns', 'wp-real-estate'),
			'4' => __('4 columns', 'wp-real-estate'),
		),
	));
	$cmb->add_field( array(
		'name' => __('Number of Listings/Agencies', 'wp-real-estate'),
		'desc' => __('The max number of listings/agencies to show in archive page.', 'wp-real-estate') . '<br>' . __('Could show less than this if not enough listings/agencies are found.', 'wp-real-estate'),
		'id'   => 'archive_listing_number',
		'type' => 'text',
		'default' => '9',
		'attributes' => array(
			'type' => 'number',
			'min'	=> 1
		),
	) );
	$cmb->object_type('options-page');
	$boxes[] = $cmb;

	// maps
	$cmb = new_cmb2_box(array(
		'id' => 'google_maps',
		'title' => __('Google Maps', 'wp-real-estate'),
		'show_on' => $show_on,
	));
	$cmb->add_field(array(
		'name' => __('API Key', 'wp-real-estate'),
		'before_row' => sprintf(__('A Google Maps API Key is required to be able to show the maps. It\'s free and you can get yours %s.', 'wp-real-estate'), '<strong><a target="_blank" href="https://developers.google.com/maps/documentation/javascript/get-api-key">here</a></strong>').'<br />'. __('You can add a configurable map to pinpoint your listings on the front end using [wre_map] shortcode.', 'wp-real-estate'),
		'id' => 'maps_api_key',
		'type' => 'text',
	));
	$cmb->add_field(array(
		'name' => __('Map Zoom', 'wp-real-estate'),
		'desc' => '',
		'id' => 'map_zoom',
		'type' => 'text',
		'default' => '14',
		'attributes' => array(
			'type' => 'number',
		),
	));
	$cmb->add_field(array(
		'name' => __('Map Height', 'wp-real-estate'),
		'desc' => '',
		'id' => 'map_height',
		'type' => 'text',
		'default' => '300',
		'attributes' => array(
			'type' => 'number',
		),
	));

	$cmb->object_type('options-page');
	$boxes[] = $cmb;

	// maps
	$cmb = new_cmb2_box(array(
		'id' => 'search',
		'title' => __('Search', 'wp-real-estate'),
		'show_on' => $show_on,
	));
	$cmb->add_field(array(
		'name' => __('Distance Measurement', 'wp-real-estate'),
		'before_row' => __('These settings relate to the [wre_search] shortcode.', 'wp-real-estate'),
		'desc' => __('Choose miles or kilometers for the radius.', 'wp-real-estate'),
		'id' => 'distance_measurement',
		'type' => 'select',
		'options' => array(
			'miles' => __('Miles', 'wp-real-estate'),
			'kilometers' => __('Kilometers', 'wp-real-estate'),
		),
	));

	$cmb->add_field(array(
		'name' => __('Radius', 'wp-real-estate'),
		'desc' => __('Show listings that are within this distance (mi or km as selected above).', 'wp-real-estate'),
		'id' => 'search_radius',
		'type' => 'text',
		'default' => '20',
		'atributes' => array(
			'type' => 'number',
			'placeholder' => '20',
		),
	));
	$cmb->add_field(array(
		'name' => __('Country', 'wp-real-estate'),
		'desc' => sprintf(__('Country name or two letter %s country code.', 'wp-real-estate'), '<a target="_blank" href="https://en.wikipedia.org/wiki/ISO_3166-1">ISO 3166-1</a>'),
		'id' => 'search_country',
		'type' => 'text',
	));

	$cmb->object_type('options-page');
	$boxes[] = $cmb;


	/* ==================== Listing Options ==================== */

	// listings setup
	$cmb = new_cmb2_box(array(
		'id' => 'listing_setup',
		'title' => __('Listing Setup', 'wp-real-estate'),
		'show_on' => $show_on,
	));
	$cmb->add_field(array(
		'name' => __('Rent / Sell', 'wp-real-estate'),
		'desc' => __('Are your listings only for rent or only for sale? Or both?', 'wp-real-estate'),
		'id' => 'display_purpose',
		'type' => 'select',
		'default' => 'both',
		'options' => array(
			'both' => __('Both', 'wp-real-estate'),
			'rent' => __('Rent Only', 'wp-real-estate'),
			'sell' => __('Sell Only', 'wp-real-estate'),
		),
	));
	$cmb->add_field(array(
		'name' => __('Default Listing Type', 'wp-real-estate'),
		'desc' => __('If the above is set to "Both", which type would you like to display as the default on the listings page?', 'wp-real-estate'),
		'id' => 'display_default',
		'type' => 'select',
		'options' => array(
			'Sell' => __('Sell', 'wp-real-estate'),
			'Rent' => __('Rent', 'wp-real-estate'),
		),
	));
	$cmb->add_field(array(
		'name' => __('Listings Page', 'wp-real-estate'),
		'desc' => __('The main page to display your listings.', 'wp-real-estate'),
		'id' => 'archives_page',
		'type' => 'select',
		'options_cb' => 'wre_get_pages',
	));
	$cmb->add_field(array(
		'name' => __('Compare Listings Page', 'wp-real-estate'),
		'desc' => __('The page to display compare listings data. This page must have an [wre_compare_listings] shortcode.', 'wp-real-estate'),
		'id' => 'compare_listings',
		'type' => 'select',
		'options_cb' => 'wre_get_pages',
	));

	$cmb->add_field(array(
		'name' => __('Force Listings Page Title', 'wp-real-estate'),
		'desc' => __('If your page title is not displaying correctly, you can force the page title here.', 'wp-real-estate') . '<br>' . __('(Some themes may be using incorrect template tags to display the archive title. This forces the title within the page)', 'wp-real-estate'),
		'id' => 'archives_page_title',
		'type' => 'select',
		'default' => 'no',
		'options' => array(
			'no' => __('No', 'wp-real-estate'),
			'yes' => __('Yes', 'wp-real-estate'),
		),
	));

	$cmb->add_field(array(
		'name' => __('Single Listing URL', 'wp-real-estate'),
		'desc' => __('The single listing URL (or slug).', 'wp-real-estate'),
		'id' => 'single_url',
		'type' => 'text',
		'default' => 'listing',
	));

	$cmb->object_type('options-page');
	$boxes[] = $cmb;

	// listing types
	$cmb = new_cmb2_box(array(
		'id' => 'listing_features',
		'title' => sprintf(__('%s Features', 'wp-real-estate'), $listing_label),
		'show_on' => $show_on,
	));
	$cmb->add_field(array(
		'name' => __('Internal Features', 'wp-real-estate'),
		'before_row' => __('Once Features have been added here, they are then available as checkboxes when adding or editing a listing.', 'wp-real-estate'),
		'after' => '<p class="cmb2-metabox-description">' . sprintf(__('Internal Features such as Open Fireplace, Gas Heating, Dishwasher etc.', 'wp-real-estate'), $listing_label) . '</p>',
		'id' => 'internal_feature',
		'type' => 'text',
		'repeatable' => true,
		'text' => array(
			'add_row_text' => __('Add Feature', 'wp-real-estate'),
		),
	));
	$cmb->add_field(array(
		'name' => __('External Features', 'wp-real-estate'),
		'after' => '<p class="cmb2-metabox-description">' . sprintf(__('External Features such as Balcony, Shed, Outdoor Entertaining etc.', 'wp-real-estate'), $listing_label) . '</p>',
		'id' => 'external_feature',
		'type' => 'text',
		'repeatable' => true,
		'text' => array(
			'add_row_text' => __('Add Feature', 'wp-real-estate'),
		),
	));
	$cmb->object_type('options-page');
	$boxes[] = $cmb;


	$cmb = new_cmb2_box(array(
		'id' => 'listing_statuses',
		'title' => sprintf(__('%s Statuses', 'wp-real-estate'), $listing_label),
		'show_on' => $show_on,
	));

	$cmb->add_field(array(
		'name' => __('Statuses', 'wp-real-estate'),
		'before_row' => __('Once Statuses have been added here, they are then available in the Status dropdown field when adding or editing a listing.', 'wp-real-estate') .' '.__('Statuses appear in a styled box over the listing\'s image.', 'wp-real-estate'),
		'after' => '<p class="cmb2-metabox-description">' . sprintf(__('Listing Statuses such as Under Offer, Sold, Available etc.', 'wp-real-estate'), $listing_label) . '</p>',
		'id' => 'listing_status',
		'type' => 'text',
		'repeatable' => true,
		'text' => array(
			'add_row_text' => __('Add Status', 'wp-real-estate'),
		),
	));

	$cmb->object_type('options-page');
	$boxes[] = $cmb;


	/* ==================== Contact Form ==================== */

	// contact form
	$cmb = new_cmb2_box(array(
		'id' => 'contact_form',
		'title' => __('Contact Form Settings', 'wp-real-estate'),
		'show_on' => $show_on,
	));
	$cmb->add_field(array(
		'name' => __('Email From', 'wp-real-estate'),
		'desc' => __('The "from" address for all enquiry emails that are sent to Agents.', 'wp-real-estate'),
		'id' => 'email_from',
		'type' => 'text_email',
		'default' => get_bloginfo('admin_email'),
		'before_row' => '<p class="cmb2-metabox-description">' . __('Contact form enquiries are sent directly to the selected Agent on that listing.', 'wp-real-estate') . '</p>',
	));
	$cmb->add_field(array(
		'name' => __('Email From Name', 'wp-real-estate'),
		'desc' => __('The "from" name for all enquiry emails that are sent to Agents.', 'wp-real-estate'),
		'id' => 'email_from_name',
		'type' => 'text',
		'default' => get_bloginfo('name'),
	));
	$cmb->add_field(array(
		'name' => __('CC', 'wp-real-estate'),
		'desc' => __('Extra email addresses that are CC\'d on every enquiry (comma separated).', 'wp-real-estate'),
		'id' => 'contact_form_cc',
		'type' => 'text',
		'attributes' => array(
			'placeholder' => 'somebody@somewhere.com',
		),
	));
	$cmb->add_field(array(
		'name' => __('BCC', 'wp-real-estate'),
		'desc' => __('Extra email addresses that are BCC\'d on every enquiry (comma separated).', 'wp-real-estate'),
		'id' => 'contact_form_bcc',
		'type' => 'text',
		'attributes' => array(
			'placeholder' => 'somebody@somewhere.com',
		),
	));

	$cmb->object_type('options-page');
	$boxes[] = $cmb;

	// contact form email
	$cmb = new_cmb2_box(array(
		'id' => 'contact_form_email',
		'title' => __('Contact Form Email', 'wp-real-estate'),
		'show_on' => $show_on,
		'desc' => '',
	));

	$cmb->add_field(array(
		'name' => __('Email Type', 'wp-real-estate'),
		'desc' => '',
		'id' => 'contact_form_email_type',
		'type' => 'select',
		'options' => array(
			'html_email' => __('HTML', 'wp-real-estate'),
			'text_email' => __('Plain Text', 'wp-real-estate'),
		),
		'default' => 'html_email',
	));
	$cmb->add_field(array(
		'name' => __('Email Subject', 'wp-real-estate'),
		'desc' => '',
		'id' => 'contact_form_subject',
		'type' => 'text',
		'default' => __('New enquiry on listing #{listing_id}', 'wp-real-estate'),
	));
	$cmb->add_field(array(
		'name' => __('Email Message', 'wp-real-estate'),
		'desc' => __('Content of the email that is sent to the agent (and other email addresses above). ' .
				'Available tags are:<br>' .
				'{agent_name}<br>' .
				'{listing_title}<br>' .
				'{listing_id}<br>' .
				'{enquiry_name}<br>' .
				'{enquiry_email}<br>' .
				'{enquiry_phone}<br>' .
				'{enquiry_message}<br>'
				, 'wp-real-estate'),
		'default' => __('Hi {agent_name},', 'wp-real-estate') . "\r\n" .
		__('There has been a new enquiry on <strong>{listing_title}</strong>', 'wp-real-estate') . "\r\n" .
		'<hr>' . "\r\n" .
		__('Name: {enquiry_name}', 'wp-real-estate') . "\r\n" .
		__('Email: {enquiry_email}', 'wp-real-estate') . "\r\n" .
		__('Phone: {enquiry_phone}', 'wp-real-estate') . "\r\n" .
		__('Message: {enquiry_message}', 'wp-real-estate') . "\r\n" .
		'<hr>',
		'id' => 'contact_form_message',
		'type' => 'textarea',
	));

	$cmb->object_type('options-page');
	$boxes[] = $cmb;

	// contact form messages
	$cmb = new_cmb2_box(array(
		'id' => 'contact_form_messages',
		'title' => __('Contact Form Messages', 'wp-real-estate'),
		'show_on' => $show_on,
		'desc' => '',
	));

	$cmb->add_field(array(
		'name' => __('Consent Field Label', 'wp-real-estate'),
		'desc' => __('Add Consent Field.', 'wp-real-estate'),
		'id' => 'contact_form_consent_label',
		'type' => 'text',
		'default' => ''
	));

	$cmb->add_field(array(
		'name' => __('Consent Description', 'wp-real-estate'),
		'desc' => __('Add Consent Description.', 'wp-real-estate'),
		'id' => 'contact_form_consent_desc',
		'type' => 'wysiwyg',
		'options' => array( 'teeny' => true, 'quicktags' => false, 'media_buttons' => false, 'textarea_rows' => 5 ),
		'default' => ''
	));

	$cmb->add_field(array(
		'name' => __('Success Message', 'wp-real-estate'),
		'desc' => __('The message that is displayed to users upon successfully sending a message.', 'wp-real-estate'),
		'id' => 'contact_form_success',
		'type' => 'text',
		'default' => __('Thank you, the agent will be in touch with you soon.', 'wp-real-estate'),
	));
	$cmb->add_field(array(
		'name' => __('Error Message', 'wp-real-estate'),
		'desc' => __('The message that is displayed if there is an error sending the message.', 'wp-real-estate'),
		'id' => 'contact_form_error',
		'type' => 'text',
		'default' => __('There was an error. Please try again.', 'wp-real-estate'),
	));
	$cmb->add_field(array(
		'name' => __('Include Error Code', 'wp-real-estate'),
		'desc' => __('Should the error code be shown with the error. Can be helpful for troubleshooting.', 'wp-real-estate'),
		'id' => 'contact_form_include_error',
		'type' => 'select',
		'options' => array(
			'yes' => __('Yes', 'wp-real-estate'),
			'no' => __('No', 'wp-real-estate'),
		),
		'default' => 'yes',
	));

	$cmb->object_type('options-page');
	$boxes[] = $cmb;


	/* ==================== Agents Listings Options ==================== */

	$cmb = new_cmb2_box(array(
		'id' => 'agents_archive_data',
		'title' => __('Agents Archive', 'wp-real-estate'),
		'show_on' => $show_on,
	));

	$cmb->add_field(array(
		'name' => __('Agents Archive Listing Style', 'wp-real-estate'),
		'desc' => '',
		'id' => 'wre_agents_mode',
		'type' => 'select',
		'default' => 'grid-view',
		'options' => array(
			'grid-view' => __('Grid Mode', 'wp-real-estate'),
			'list-view' => __('List Mode', 'wp-real-estate'),
		),
	));

	$cmb->add_field(array(
		'name' => __('Agents Archive Grid Columns', 'wp-real-estate'),
		'desc' => __('The number of columns to display, when viewing agents in grid mode.', 'wp-real-estate'),
		'id' => 'wre_archive_agents_columns',
		'type' => 'select',
		'default' => '3',
		'options' => array(
			'2' => __('2 columns', 'wp-real-estate'),
			'3' => __('3 columns', 'wp-real-estate'),
			'4' => __('4 columns', 'wp-real-estate'),
		),
	));
	
	$cmb->add_field( array(
		'name' => __('Agents Archive Number of Agents', 'wp-real-estate'),
		'desc' => __('The max number of agents to show', 'wp-real-estate') . '<br>' . __('Could show less than this if not enough agents are found.', 'wp-real-estate'),
		'id'   => 'agents_archive_max_agents',
		'type' => 'text',
		'default' => '9',
		'attributes' => array(
			'type' => 'number',
			'min'	=> 1
		),
	) );
	
	$cmb->add_field(array(
		'name' => __('Agents Archive Allow Pagination', 'wp-real-estate'),
		'desc' => '',
		'id' => 'agents_archive_allow_pagination',
		'type' => 'select',
		'default' => 'yes',
		'options' => array(
			'yes' => __('Yes', 'wp-real-estate'),
			'no' => __('No', 'wp-real-estate'),
		),
	));

	$cmb->object_type('options-page');
	$boxes[] = $cmb;

	// template html
	$cmb = new_cmb2_box(array(
		'id' => 'agents_listing_data',
		'title' => __('Agent Single', 'wp-real-estate'),
		'show_on' => $show_on,
	));

	$cmb->add_field(array(
		'name' => __('Show Agent Listings', 'wp-real-estate'),
		'desc' => __('Should the agent listings be shown below the content.', 'wp-real-estate'),
		'id' => 'show_agents_listings',
		'type' => 'select',
		'default' => 'yes',
		'options' => array(
			'yes' => __('Yes', 'wp-real-estate'),
			'no' => __('No', 'wp-real-estate'),
		),
	));

	$cmb->add_field(array(
		'name' => __('Single Agent Page Listing Style', 'wp-real-estate'),
		'desc' => '',
		'id' => 'wre_agent_mode',
		'type' => 'select',
		'default' => 'grid-view',
		'options' => array(
			'grid-view' => __('Grid Mode', 'wp-real-estate'),
			'list-view' => __('List Mode', 'wp-real-estate'),
		),
	));

	$cmb->add_field(array(
		'name' => __('Single Agent Page Grid Columns', 'wp-real-estate'),
		'desc' => __('The number of columns to display, when viewing agent listings in grid mode.', 'wp-real-estate'),
		'id' => 'wre_agent_columns',
		'type' => 'select',
		'default' => '3',
		'options' => array(
			'2' => __('2 columns', 'wp-real-estate'),
			'3' => __('3 columns', 'wp-real-estate'),
			'4' => __('4 columns', 'wp-real-estate'),
		),
	));
	
	$cmb->add_field( array(
		'name' => __('Single Agent Page Max Listings', 'wp-real-estate'),
		'desc' => __('The max number of agent listings to show', 'wp-real-estate') . '<br>' . __('Could show less than this if not enough agent listings are found.', 'wp-real-estate'),
		'id'   => 'agent_max_listings',
		'type' => 'text',
		'default' => '3',
		'attributes' => array(
			'type' => 'number',
			'min'	=> 1
		),
	) );

	$cmb->add_field(array(
		'name' => __('Single Agent Page', 'wp-real-estate'),
		'desc' => __('Single agent page, used when Theme Compatibility is enabled.', 'wp-real-estate'),
		'id' => 'wre_single_agent',
		'type' => 'select',
		'options_cb' => 'wre_get_pages',
	));

	$cmb->object_type('options-page');
	$boxes[] = $cmb;


	/* ==================== IDX Settings ==================== */

	$cmb = new_cmb2_box(array(
		'id' => 'idx_imported_listings',
		'title' => __('IDX Imported Listings', 'wp-real-estate'),
		'description' => '<p class="cmb2-metabox-description">' . __('These settings apply to any imported IDX listings. Imported listings are updated via the latest API response twice daily.', 'wp-real-estate') . '</p>',
		'show_on' => $show_on,
	));
	
	$cmb->add_field( array(
		'name' => __('Enter Your API Key:', 'wp-real-estate'),
		'desc' => __('Enter your API key to continue', 'wp-real-estate'),
		'before_row' => '<p class="cmb2-metabox-description">' . sprintf (__( 'If you do not have an %s account, please contact the IDX Broker team at 800-421-9668.', 'wp-real-estate' ), '<a href="https://idxbroker.com" target="_blank">'. __( 'IDX Broker', 'wp-real-estate' ) .'</a>') .' </p>',
		'id'   => 'wre_idx_api_key',
		'type' => 'text',
		'default' => '',
		'attributes' => array(
		),
	) );
	
	$cmb->add_field(array(
		'name' => __('Update Listings', 'wp-real-estate'),
		'before_row' => '<p class="cmb2-metabox-description">' . __('These settings apply to any imported IDX listings. Imported listings are updated via the latest API response twice daily.', 'wp-real-estate') . '</p>',
		'id' => 'wre_update_listings',
		'type' => 'radio_inline',
		'default' => 'update_noimage',
		'options' => array(
			'update_all' => __('Update All', 'wp-real-estate') . '<p class="cmb2-metabox-description">'.__('Excludes Post Title and Post Content', 'wp-real-estate').')</span>',
			'update_noimage' => __('Update Excluding Images', 'wp-real-estate') . '<p class="cmb2-metabox-description">'.__('Also excludes Post Title and Post Content', 'wp-real-estate').')</p>',
			'update_none' => __('Do Not Update', 'wp-real-estate') . '<p class="cmb2-metabox-description"> <b>'.__( 'Not recommended as displaying inaccurate MLS data may violate your IDX agreement.', 'wp-real-estate' ).'</b><br />'.__('Listing will be changed to sold status if it exists in the sold data feed.', 'wp-real-estate').'</p>',
		),
	));
	
	$cmb->add_field(array(
		'name' => __('Sold Listings', 'wp-real-estate'),
		'id' => 'wre_sold_listings',
		'type' => 'radio',
		'default' => 'keep_sold',
		'options' => array(
			'keep_sold' => __('Keep All', 'wp-real-estate') . '<p class="cmb2-metabox-description">'.__('This will keep all imported listings published with the status changed to reflect as sold.', 'wp-real-estate').'</p>',
			'draft_sold' => __('Keep as Draft', 'wp-real-estate') . '<p class="cmb2-metabox-description">'.__('This will keep all imported listings that have been sold, but they will be changed to draft status in WordPress.', 'wp-real-estate').'</p>',
			'delete_sold' => __('Delete Sold', 'wp-real-estate') . '<p class="cmb2-metabox-description"><b>'.__( 'Not recommended.', 'wp-real-estate' ).'</b><br />'.__('This will delete all sold listings and attached featured images from your WordPress database and media library.', 'wp-real-estate').'</p>',
		),
	));

	$cmb->object_type('options-page');
	$boxes[] = $cmb;

	
	/* ==================== Advanced Options ==================== */

	// template html
	$cmb = new_cmb2_box(array(
		'id' => 'template_html',
		'title' => __('Template HTML', 'wp-real-estate'),
		'show_on' => $show_on,
	));
	$cmb->add_field(array(
		'name' => __('Theme Compatibility', 'wp-real-estate'),
		'desc' => __('If enabled, add [wre_archive_listings], [wre_archive_agencies] & [wre_archive_agent] shortcode on there respective pages and remove it if disabled.', 'wp-real-estate'),
		'id' => 'wre_theme_compatibility',
		'type' => 'select',
		'default' => 'enable',
		'options' => array(
			'enable' => __('Enabled', 'wp-real-estate'),
			'disable' => __('Disabled', 'wp-real-estate'),
		),
	));
	$cmb->add_field(array(
		'name' => __('Opening HTML Tag(s)', 'wp-real-estate'),
		'desc' => __('Used for theme compatability, this option will override the opening HTML for all Listings pages.', 'wp-real-estate') . '<br>' . __('This can help you to match the HTML with your current theme.', 'wp-real-estate'),
		'id' => 'opening_html',
		'type' => 'textarea',
		'attributes' => array(
			'placeholder' => '<div class=&quot;container&quot;><div class=&quot;main-content&quot;>',
			'rows' => 2,
		),
		'before_row' => '<p class="cmb2-metabox-description"></p>',
	));
	$cmb->add_field(array(
		'name' => __('Closing HTML Tag(s)', 'wp-real-estate'),
		'desc' => __('Used for theme compatability, this option will override the closing HTML for all Listings pages.', 'wp-real-estate') . '<br>' .
		__('This can help you to match the HTML with your current theme.', 'wp-real-estate'),
		'id' => 'closing_html',
		'type' => 'textarea',
		'attributes' => array(
			'placeholder' => '</div></div>',
			'rows' => 2,
		),
	));
	$cmb->add_field(array(
		'name' => __('Hide In-content sidebar page', 'wp-real-estate'),
		'desc' => __('Used for removing in-content sidebar on single-listing page.', 'wp-real-estate'),
		'id' => 'wre_hide_in_content_sidebar',
		'type' => 'select',
		'default' => 'no',
		'options' => array(
			'yes' => __('Yes', 'wp-real-estate'),
			'no' => __('No', 'wp-real-estate'),
		),
	));
	
	$cmb->object_type('options-page');
	$boxes[] = $cmb;

	// uninstall
	$cmb = new_cmb2_box(array(
		'id' => 'uninstall',
		'title' => __('Uninstall', 'wp-real-estate'),
		'show_on' => $show_on,
	));
	$cmb->add_field(array(
		'name' => __('Delete Data', 'wp-real-estate'),
		'desc' => __('Should all plugin data be deleted upon uninstalling this plugin?', 'wp-real-estate'),
		'id' => 'delete_data',
		'type' => 'select',
		'default' => 'no',
		'options' => array(
			'yes' => __('Yes', 'wp-real-estate'),
			'no' => __('No', 'wp-real-estate'),
		),
	));

	$cmb->object_type('options-page');
	$boxes[] = $cmb;

	// box 3, in sidebar of our two-column layout
	$cmb = new_cmb2_box(array(
		'id' => 'side_metabox',
		'title' => __('Save Options', 'wp-real-estate'),
		'show_on' => $show_on,
		'context' => 'side',
	));
	$cmb->add_field(array(
		'name' => __('Publish?', 'wp-real-estate'),
		'desc' => __('Save Changes', 'wp-real-estate'),
		'id' => 'wre_save_button',
		'type' => 'wre_options_save_button',
		'show_names' => false,
	));
	$cmb->object_type('options-page');
	$boxes[] = $cmb;
	
	$cmb = new_cmb2_box(array(
		'id' => 'wre_pro_version_image',
		'title' => 'WRE Pro Version',
		'show_on' => $show_on,
		'context' => 'side',
	));
	$cmb->add_field(array(
		'id' => 'wre_save_button1',
		'name' => '',
		'type' => 'wiki_test_textareasmall',
		'show_names' => false,
	));
	$cmb->object_type('options-page');
	$boxes[] = $cmb;

	// Arguments array. See the arguments page for more detail
	$args = array(
		'key' => $opt_key,
		'title' => __('WRE Settings', 'wp-real-estate'),
		'topmenu' => 'edit.php',
		'postslug' => 'listing',
		'boxes' => $boxes,
		'tabs' => $tabs,
		'cols' => 2,
		'savetxt' => '',
	);

	new Cmb2_Metatabs_Options(apply_filters('wre_admin_options', $args, $cmb));
}