<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WRE_Metaboxes' ) ) :
		/**
		 * CMB2 Theme Options
		 * @version 0.1.0
		 */
		class WRE_Metaboxes {
			
				/**
				 * Post type
				 * @var string
				 */
				public $type = 'listing';

				/**
				 * Metabox prefix
				 * @var string
				 */
				public $prefix = '_wre_listing_';

				public $listing_label = '';

				/**
				 * Holds an instance of the object
				 *
				 * @var Myprefix_Admin
				 **/
				public static $instance = null;

				public function __construct() {
						$this->listing_label = __( 'Listing', 'wp-real-estate' );
				}

				/**
				 * Returns the running object
				 *
				 * @return Myprefix_Admin
				 **/
				public static function get_instance() {
						if( is_null( self::$instance ) ) {
								self::$instance = new self();

								self::$instance->description();
								self::$instance->details();
								self::$instance->features();
								self::$instance->images();

								self::$instance->status();
								self::$instance->address();
								self::$instance->settings();
								self::$instance->open();
						}
						return self::$instance;
				}

				/*==================== Listing Description ====================*/

				public function description() {

						$box = new_cmb2_box( array(
								'id'            => $this->prefix . 'description',
								'title'         => '<span class="dashicons dashicons-welcome-write-blog"></span> ' . sprintf( __( "%s Description", 'wp-real-estate' ), $this->listing_label ),
								'object_types'  => array( $this->type ), 
								'priority'     => 'high'
						) );

						$fields = array();

						$fields[10] = array(
							'name'          => __( 'Tagline', 'wp-real-estate' ),
							'desc'    		=> '',
							'id'            => $this->prefix . 'tagline',
							'type'          => 'text',
						);

						$fields[20] = array(
							'name'          => __( 'Main Description', 'wp-real-estate' ),
							'desc'    		=> '',
							'id'            => 'content',
							'type'    => 'wysiwyg',
								'options' => array(
									'wpautop' => true, // use wpautop?
									'media_buttons' => false, // show insert/upload button(s)
									'textarea_rows' => get_option('default_post_edit_rows',3), // rows="..."
									'teeny' => true, // output the minimal editor config used in Press This
									'tinymce' => true, // load TinyMCE, can be used to pass settings directly to TinyMCE using an array()
									'quicktags' => false // load Quicktags, can be used to pass settings directly to Quicktags using an array()
								),
						);

						// filter the fields
						$fields = apply_filters( 'wre_metabox_description', $fields );

						// sort numerically
						ksort( $fields );

						// loop through ordered fields and add them to the metabox
						if( $fields ) {
								foreach ($fields as $key => $value) {
										$fields[$key] = $box->add_field( $value );
								}
						}

				}

				
				/*==================== Listing Details ====================*/

				public function details() {

						$box = new_cmb2_box( array(
								'id'			=> $this->prefix . 'details',
								'title'			=> '<span class="dashicons dashicons-admin-home"></span> ' . sprintf( __( "%s Details", 'wp-real-estate' ), $this->listing_label ),
								'object_types'	=> array( $this->type ), 
								'priority'     => 'high'
						) );

						$fields = array();

						$fields[10] = array(
							'name'			=> __( 'MLS #:', 'wp-real-estate' ),
							'id'			=> $this->prefix . 'mls',
							'type'			=> 'text',
						);
						$fields[11] = array(
							'name'			=> __( 'Price', 'wp-real-estate' ),
							'desc'			=> __( 'Numbers only.', 'wp-real-estate' ),
							'id'			=> $this->prefix . 'price',
							'type'			=> 'text',
							'attributes'	=> array(
								'type'	=> 'number',
								'min'	=> '0',
								'step'	=> '0.01',
							),
						);
						$fields[12] = array(
							'name'			=> __( 'Suffix', 'wp-real-estate' ),
							'desc'			=> __( 'Optional text to add after the price. Such as p/w, per month or POA.', 'wp-real-estate' ),
							'id'			=> $this->prefix . 'price_suffix',
							'type'			=> 'text',
						);

						$fields[20] = array(
							'name'				=> __( 'Type', 'wp-real-estate' ),
							'desc'				=> '',
							'id'				=> $this->prefix . 'type',
							'taxonomy'			=> 'listing-type', //Enter Taxonomy Slug
							'type'				=> 'taxonomy_select',
							'show_option_none'	=> false,
						);
						$fields[21] = array(
							'name'				=> __( 'Status', 'wp-real-estate' ),
							'desc'				=> '',
							'id'				=> $this->prefix . 'status',
							'type'				=> 'select',
							'show_option_none'	=> true,
							'options_cb'		=> 'wre_listing_statuses',
						);
						$fields[22] = array(
							'name'			=> __( 'Purpose', 'wp-real-estate' ),
							'desc'			=> '',
							'id'			=> $this->prefix . 'purpose',
							'type'			=> 'select',
							'options'		=> apply_filters( 'wre_purposes',  array(
									'Sell'	=> __( 'Sell', 'wp-real-estate' ),
									'Rent'	=> __( 'Rent', 'wp-real-estate' ),
							) )
						);

						$fields[30] = array(
							'name'			=> __( 'Bedrooms', 'wp-real-estate' ),
							'desc'			=> __( 'Numbers only.', 'wp-real-estate' ),
							'id'			=> $this->prefix . 'bedrooms',
							'type'			=> 'text',
							'attributes'	=> array(
								'type'	=> 'number',
								'min'	=> '0',
								'step'	=> '0.5',
							),
						);
						$fields[31] = array(
							'name'			=> __( 'Bathrooms', 'wp-real-estate' ),
							'id'			=> $this->prefix . 'bathrooms',
							'type'			=> 'text',
							'attributes'	=> array(
								'type'	=> 'number',
								'min'	=> '0',
								'step'	=> '0.5',
							),
						);
						$fields[32] = array(
							'name'			=> __( 'Car Spaces', 'wp-real-estate' ),
							'id'			=> $this->prefix . 'car_spaces',
							'type'			=> 'text',
							'attributes'	=> array(
								'type'	=> 'number',
								'min'	=> '0',
								'step'	=> '0.5',
							),
						);

						// filter the fields
						$fields = apply_filters( 'wre_metabox_details', $fields );

						// sort numerically
						ksort( $fields );

						// loop through ordered fields and add them to the metabox
						if( $fields ) {
							foreach ($fields as $key => $value) {
								$fields[$key] = $box->add_field( $value );
							}
						}

						// setup the columns
						if( !is_admin() ) { return; }

						$cmb2Grid = new \Cmb2Grid\Grid\Cmb2Grid( $box );

						// define number of rows
						$rows = apply_filters( 'wre_metabox_details_rows', 4 ); 

						// loop through number of rows
						for ($i=1; $i < $rows; $i++) {

							// add each row
							$row[$i] = $cmb2Grid->addRow();

							// reset the array for each row
							$array = array();

							// this allows up to 4 columns in each row
							if( isset( $fields[$i * 10] ) ) {
								$array[] = $fields[$i * 10];
							}
							if( isset( $fields[$i * 10 + 1] ) ) {
								$array[] = $fields[$i * 10 + 1];
							}
							if( isset( $fields[$i * 10 + 2] ) ) {
								$array[] = $fields[$i * 10 + 2];
							}
							if( isset( $fields[$i * 10 + 3] ) ) {
								$array[] = $fields[$i * 10 + 3];
							}

							// add the fields as columns
							$row[$i]->addColumns( 
								apply_filters( "wre_metabox_details_row_{$i}_columns", $array )
							);

						}

				}

				/*==================== Listing Features ====================*/

				public function features() {

						$box = new_cmb2_box( array(
							'id'			=> $this->prefix . 'features',
							'title'			=> '<span class="dashicons dashicons-yes"></span> ' . sprintf( __( "%s Features", 'wp-real-estate' ), $this->listing_label ),
							'object_types'	=> array( $this->type ),
							'priority'     => 'high'
						) );

						$fields = array();

						$fields[10] = array(
							'name'			=> __( 'Building Size', 'wp-real-estate' ),
							'id'			=> $this->prefix . 'building_size',
							'type'			=> 'text',
							'attributes'	=> array(
								'type'	=> 'number',
								'min'	=> '0',
								'step'	=> '0.01',
							),
						);
						$fields[11] = array(
							'name'			=> __( 'Building Unit', 'wp-real-estate' ),
							'id'			=> $this->prefix . 'building_unit',
							'type'			=> 'select',
							'options'		=> apply_filters( 'wre_building_unit', array(
								'squares'	=> __( 'Squares', 'wp-real-estate' ),
								'sqmtr'	=> __( 'Sq Mtr', 'wp-real-estate' ),
								'sqft'		=> __( 'Sq Ft', 'wp-real-estate' ),
							) ),
						);

						$fields[20] = array(
							'name'			=> __( 'Land Size', 'wp-real-estate' ),
							'id'			=> $this->prefix . 'land_size',
							'type'			=> 'text',
							'attributes'	=> array(
								'type'	=> 'number',
								'min'	=> '0',
								'step'	=> '0.01',
							),
						);
						$fields[21] = array(
							'name'			=> __( 'Land Unit', 'wp-real-estate' ),
							'id'			=> $this->prefix . 'land_unit',
							'type'			=> 'select',
							'options'		=> apply_filters( 'wre_land_unit', array(
								'squares'	=> __( 'Squares', 'wp-real-estate' ),
								'sqmtr'		=> __( 'Sq Mtr', 'wp-real-estate' ),
								'sqft'		=> __( 'Sq Ft', 'wp-real-estate' ),
								'acres'		=> __( 'Acres', 'wp-real-estate' ),
								'hectares'	=> __( 'Hectares', 'wp-real-estate' ),
							) ),
						);

						$fields[30] = array(
							'name'			=> __( 'Internal Features', 'wp-real-estate' ),
							'id'			=> $this->prefix . 'internal_features',
							'type'			=> 'multicheck_inline',
							'options_cb'	=> 'wre_listing_internal_features',
						);
						$fields[31] = array(
							'name'			=> __( 'External Features', 'wp-real-estate' ),
							'id'			=> $this->prefix . 'external_features',
							'type'			=> 'multicheck_inline',
							'options_cb'	=> 'wre_listing_external_features',
						);


						// filter the fields
						$fields = apply_filters( 'wre_metabox_features', $fields );

						// sort numerically
						ksort( $fields );

						// loop through ordered fields and add them to the metabox
						if( $fields ) {
							foreach ($fields as $key => $value) {
								$fields[$key] = $box->add_field( $value );
							}
						}

						// setup the columns
						if( !is_admin() ) { return; }
						$cmb2Grid = new \Cmb2Grid\Grid\Cmb2Grid( $box );

						// define number of rows
						$rows = apply_filters( 'wre_metabox_features_rows', 4 ); 

						// loop through number of rows
						for ($i=1; $i < $rows; $i++) {

							// add each row
							$row[$i] = $cmb2Grid->addRow();

							// reset the array for each row
							$array = array();

							// this allows up to 4 columns in each row
							if( isset( $fields[$i * 10] ) ) {
								$array[] = $fields[$i * 10];
							}
							if( isset( $fields[$i * 10 + 1] ) ) {
								$array[] = $fields[$i * 10 + 1];
							}
							if( isset( $fields[$i * 10 + 2] ) ) {
								$array[] = $fields[$i * 10 + 2];
							}
							if( isset( $fields[$i * 10 + 3] ) ) {
								$array[] = $fields[$i * 10 + 3];
							}

							// add the fields as columns
							$row[$i]->addColumns( 
								apply_filters( "wre_metabox_features_row_{$i}_columns", $array )
							);

						}

				}

				/*==================== Gallery ====================*/
				public function images() {

						$box = new_cmb2_box( array(
								'id'			=> $this->prefix . 'images',
								'title'			=> '<span class="dashicons dashicons-images-alt2"></span> ' . __( "Images", 'wp-real-estate' ),
								'object_types'	=> array( $this->type ),
								'priority'     => 'high'
						) );

						$fields = array();

						$fields[10] = array(
							'name'			=> __( 'Image Gallery', 'wp-real-estate' ),
							'desc'			=> __( 'The first image will be used as the main feature image. Drag and drop to re-order.', 'wp-real-estate' ),
							'id'			=> $this->prefix . 'image_gallery',
							'type'			=> 'file_list',
							'preview_size'	=> array( 150, 100 ), // Default: array( 50, 50 )
								'text'	=> array(
									'add_upload_files_text'	=> __( 'Add Images', 'wp-real-estate' ), 
								),
						);

						// filter the fields
						$fields = apply_filters( 'wre_metabox_images', $fields );

						// sort numerically
						ksort( $fields );

						// loop through ordered fields and add them to the metabox
						if( $fields ) {
							foreach ($fields as $key => $value) {
								$fields[$key] = $box->add_field( $value );
							}
						}
				}

				/*==================== Listing Status ====================*/

				public function status() {

						$box = new_cmb2_box( array(
								'id'			=> $this->prefix . 'status',
								'title'			=> '<span class="dashicons dashicons-admin-generic"></span> ' . sprintf( __( "%s Status", 'wp-real-estate' ), $this->listing_label ),
								'object_types'	=> array( $this->type ), 
								'context'		=> 'side',
						) );

						$fields = array();

						$fields[10] = array(
							'name'			=> '',
							'desc'			=> '',
							'id'			=> '',
							'type'			=> 'title',
							'after_row'		=> 'wre_admin_status_area',
						);

						// filter the fields
						$fields = apply_filters( 'wre_metabox_status', $fields );

						// sort numerically
						ksort( $fields );

						// loop through ordered fields and add them to the metabox
						if( $fields ) {
								foreach ($fields as $key => $value) {
										$fields[$key] = $box->add_field( $value );
								}
						}

			}


			/*==================== Listing Address ====================*/

			public function address() {

					$box = new_cmb2_box( array(
							'id'			=> $this->prefix . 'address',
							'title'			=> '<span class="dashicons dashicons-location-alt"></span> ' . sprintf( __( "%s Address", 'wp-real-estate' ), $this->listing_label ),
							'object_types'	=> array( $this->type ), 
							'context'		=> 'side',
					) );

					$fields = array();

					$fields[10] = array(
						'name'			=> __( 'Find Address', 'wp-real-estate' ),
						'desc'			=> '',
						'id'			=> "wre-geocomplete",
						'type'			=> 'text',
						'after_row'		=> 'wre_admin_map',
						'attributes'	=> array( 
							'placeholder'	=> __( 'Type your address...', 'wp-real-estate' ),
						),
					);
					$fields[15] = array(
						'name'			=> __( 'Displayed Address', 'wp-real-estate' ),
						'desc'			=> '',
						'id'			=> $this->prefix . 'displayed_address',
						'type'			=> 'text',
						'attributes'	=> array( 
							'data-geo'	=> 'formatted_address',
						),
					);
					$fields[20] = array(
						'name'			=> __( 'Street Number', 'wp-real-estate' ),
						'desc'			=> '',
						'id'			=> $this->prefix . 'street_number',
						'type'			=> 'text',
						'attributes'	=> array( 
							'data-geo'	=> 'street_number',
						),
					);
					$fields[25] = array(
						'name'			=> __( 'Street Name', 'wp-real-estate' ),
						'desc'			=> '',
						'id'			=> $this->prefix . 'route',
						'type'			=> 'text',
						'attributes'	=> array( 
							'data-geo'	=> 'route',
						),
					);
					$fields[30] = array(
						'name'			=> __( 'City / Town / Locality', 'wp-real-estate' ),
						'desc'			=> '',
						'id'			=> $this->prefix . 'city',
						'type'			=> 'text',
						'attributes'	=> array( 
							'data-geo'	=> 'locality',
						),
					);
					$fields[35] = array(
						'name'			=> __( 'Zip / Postal Code', 'wp-real-estate' ),
						'desc'			=> '',
						'id'			=> $this->prefix . 'zip',
						'type'			=> 'text',
						'attributes'	=> array( 
							'data-geo'	=> 'postal_code',
						),
					);
					$fields[40] = array(
						'name'			=> __( 'State', 'wp-real-estate' ),
						'desc'			=> '',
						'id'			=> $this->prefix . 'state',
						'type'			=> 'text',
						'attributes'	=> array( 
							'data-geo'	=> 'administrative_area_level_1',
						),
					);
					$fields[45] = array(
						'name'			=> __( 'Country', 'wp-real-estate' ),
						'desc'			=> '',
						'id'			=> $this->prefix . 'country',
						'type'			=> 'text',
						'attributes'	=> array( 
							'data-geo'	=> 'country',
						),
					);
					$fields[50] = array(
						'name'			=> __( 'Latitude', 'wp-real-estate' ),
						'desc'			=> '',
						'id'			=> $this->prefix . 'lat',
						'type'			=> 'text',
						'attributes'	=> array( 
							'data-geo'	=> 'lat',
						),
					);
					$fields[55] = array(
						'name'			=> __( 'Longitude', 'wp-real-estate' ),
						'desc'			=> '',
						'id'			=> $this->prefix . 'lng',
						'type'			=> 'text',
						'attributes'	=> array(
							'data-geo'	=> 'lng',
						),
					);

					// filter the fields
					$fields = apply_filters( 'wre_metabox_address', $fields );

					// sort numerically
					ksort( $fields );

					// loop through ordered fields and add them to the metabox
					if( $fields ) {
							foreach ($fields as $key => $value) {
									$fields[$key] = $box->add_field( $value );
							}
					}

			}
			
			/*==================== Listing Settings ====================*/
			
			public function settings() {

					$box = new_cmb2_box( array(
							'id'			=> $this->prefix . 'settings',
							'title'			=> '<span class="dashicons dashicons-admin-settings"></span> ' . sprintf( __( "%s Settings", 'wp-real-estate' ), $this->listing_label ),
							'object_types'	=> array( $this->type ), 
							'context'		=> 'side',
					) );

					$fields = array();

					$fields[10] = array(
						'name'				=> __( 'Agent', 'wp-real-estate' ),
						'desc'				=> '',
						'id'				=> $this->prefix . 'agent',
						'type'				=> 'select',
						'select_all_button'	=> false,
						'options_cb'		=> 'wre_admin_get_agents',
					);

					$fields[15] = array(
						'name'				=> __( 'Hide Items', 'wp-real-estate' ),
						'desc'				=> __( 'Allows you to hide items on the front end, even if they are filled in.', 'wp-real-estate' ),
						'id'				=> $this->prefix . 'hide',
						'type'				=> 'multicheck',
						'select_all_button'	=> false,
						'options'			=> array(
							'price'			=> __( 'Price', 'wp-real-estate' ),
							'address'		=> __( 'Address', 'wp-real-estate' ),
							'map'			=> __( 'Map', 'wp-real-estate' ),
							'contact_form'	=> __( 'Contact Form', 'wp-real-estate' ),
							'agent'			=> __( 'Agent', 'wp-real-estate' ),
							'social-share'	=> __( 'Social Share', 'wp-real-estate' )
						),
					);

					// filter the fields
					$fields = apply_filters( 'wre_metabox_settings', $fields );

					// sort numerically
					ksort( $fields );

					// loop through ordered fields and add them to the metabox
					if( $fields ) {
							foreach ($fields as $key => $value) {
									$fields[$key] = $box->add_field( $value );
							}
					}

			}

			/*==================== Listing Open for Inspection ====================*/
			public function open() {

					$box = new_cmb2_box( array(
							'id'			=> $this->prefix . 'open',
							'title'			=> '<span class="dashicons dashicons-admin-network"></span> ' . __( "Open For Inspection", 'wp-real-estate' ),
							'object_types'	=> array( $this->type ), 
							'context'		=> 'side',
					) );


					$group_field_id = $box->add_field( array(
							'id'			=> $this->prefix . 'open',
							'type'			=> 'group',
							'description'	=> '',
							'options'		=> array(
								'group_title'	=> __( 'Open Time {#}', 'wp-real-estate' ), 
								'add_button'	=> __( 'Add Open Time', 'wp-real-estate' ),
								'remove_button'	=> __( 'Remove Time', 'wp-real-estate' ),
								'sortable'		=> true, // beta
							),
					) );

				$fields = array();

				$fields[10] = array(
						'name'			=> __( 'Day', 'wp-real-estate' ),
						'id'			=> 'day',
						'type'			=> 'text_date',
						'date_format'	=> 'y-m-d',
				);
				$fields[15] = array(
						'name'			=> __( 'Start Time', 'wp-real-estate' ),
						'id'			=> 'start',
						'type'			=> 'text_time',
						'time_format'	=> get_option( 'time_format' ),
				);
				$fields[20] = array(
						'name'			=> __( 'End Time', 'wp-real-estate' ),
						'id'			=> 'end',
						'type'			=> 'text_time',
						'time_format'	=> get_option( 'time_format' ),
				);


				// filter the fields
				$fields = apply_filters( 'wre_metabox_features', $fields );

				// sort numerically
				ksort( $fields );

				// loop through ordered fields and add them to the metabox
				if( $fields ) {
						foreach ($fields as $key => $value) {
								$fields[$key] = $box->add_group_field( $group_field_id, $value );
						}
				}

			}

		}

endif;