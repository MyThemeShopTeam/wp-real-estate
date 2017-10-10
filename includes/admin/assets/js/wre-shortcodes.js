/*
 * WP Real Estate plugin by MyThemeShop
 * https://wordpress.com/plugins/wp-real-estate/
 */
jQuery(document).ready(function($) {

	tinymce.create('tinymce.plugins.wre_plugin', {
			
		init : function(ed, url) {
			function getField(key, data) {

				switch(data.type) {

					case 'listbox':
						return {
								type	: 'listbox',
								name	: key,
								label	: data.label,
								values	: data.values,
								tooltip	: data.tooltip,
								value	: data.value // Sets the default
							};
					break;

					case 'textbox':
						return {
								type	: 'textbox',
								name	: key,
								label	: data.label,
								tooltip	: data.tooltip,
								value	: data.value
							}
					break;
					case 'container':
						return {
							type	: 'container',
							name	: '',
							label	: '',
							html	: '<h1 style="font-weight: 600">'+data.value+'</h1>'
						}
					break;
					default:
					return false;
				}
			}

			// Register buttons - trigger above command when clicked
			ed.addButton('wre_shortcodes_dropdown', {
				type: 'listbox',
				text: 'WRE Shortcodes',
				icon: false,
				onselect: function(e) {
					var shortcode_args = [];
					var attributes = e.target.settings.attributes;
					var shortcode_name = e.target.settings.shortcode_name;

					if( attributes != undefined ) {
						jQuery.each( attributes, function(key, value) {
							var data = getField(key, value);
							if(data)
								shortcode_args.push(data);
						});

						ed.windowManager.open({
							title: e.target.settings.text,
							autoScroll: true,
							body: shortcode_args,
							onsubmit: function( e ) {
								var shortcode_data = '[';
								shortcode_data += shortcode_name;
								$.each(e.data, function(key, value){
									shortcode_data += ' '+key+'="'+value+'"';
								});
								shortcode_data += ']';
								ed.insertContent( shortcode_data );
							}
						});

					}
				}, values: [
					{
						text			: 'WRE Listings',
						shortcode_name	: 'wre_listings',
						value			: '[wre_listings]',
						'attributes'	: wre_tinyMCE_object.listings_fields
					},

					{
						text			: 'WRE Search',
						shortcode_name	: 'wre_search',
						value			: '[wre_search]',
						attributes		: wre_tinyMCE_object.search_fields
					},

					{
						text			: 'WRE Listing',
						shortcode_name	: 'wre_listing',
						value			: '[wre_listing]',
						attributes		: wre_tinyMCE_object.listing_fields
					},

					{
						text			: 'WRE Agent',
						shortcode_name	: 'wre_agent',
						value			: '[wre_agent]',
						attributes		: wre_tinyMCE_object.agent_fields
					},

					{
						text			: 'WRE Nearby Listings',
						shortcode_name	: 'wre_nearby_listings',
						value			: '[wre_nearby_listings]',
						attributes		: wre_tinyMCE_object.nearby_listing_fields
					},
					{
						text			: 'WRE Agents',
						shortcode_name	: 'wre_agents',
						value			: '[wre_agents]',
						attributes		: wre_tinyMCE_object.wre_agents_fields
					},
					{
						text			: 'WRE Map',
						shortcode_name	: 'wre_map',
						value			: '[wre_map]',
						attributes		: wre_tinyMCE_object.wre_map_fields
					}

				], onPostRender: function() {
					// Select the second item by default
				}
			});
		}
	});

	// Register our TinyMCE plugin
	// first parameter is the button ID1
	// second parameter must match the first parameter of the tinymce.create() function above
	tinymce.PluginManager.add('wre_shortcodes_dropdown', tinymce.plugins.wre_plugin);
});