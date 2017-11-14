/*
 * WP Real Estate plugin by MyThemeShop
 * https://wordpress.com/plugins/wp-real-estate/
 */
(function ($) {
	/**
	 * Archive page
	 */
	wp_real_estate_view_switcher();
	wp_real_estate_ordering();
	wp_real_estate_buy_sell();

	/**
	 * Single listing
	 */
	if ($('body').hasClass('wre')) {
		if ($('body').find('#wre-map').length > 0) {
			wp_real_estate_google_map();
		}
	}

	/**
	 * ================================= FUNCTIONS =======================================
	 */

	/**
	 * Ordering
	 */
	function wp_real_estate_ordering() {
		$('.wre-ordering').on('change', 'select.listings-orderby', function () {
			var orderby = $(this).val();
			var search_form_data = $(this).closest('form').serialize();
			wre_orderby_ajax_filter( 'wre_orderby_value', orderby, search_form_data );
		});
	}

	function wre_orderby_ajax_filter( action, orderby, formdata ) {

		$('#wre-archive-wrapper').find('.wre-orderby-loader').addClass('in');
		$.ajax({
			type: 'POST',
			url: wre.ajax_url,
			data: {
				'action': action,
				'order_by': orderby,
				'search_data': formdata
			},
			success: function (response) {
				$('#wre-archive-wrapper').find('.wre-items').html(response);
				$('#wre-archive-wrapper').find('.wre-orderby-loader').removeClass('in')
				var newurl = window.location.pathname;
				if( window.location.search == '' ) {
					newurl = newurl+'?wre-orderby='+orderby;
				} else {
					var search_string = window.location.search;
					if (search_string.indexOf("wre-orderby") <= 0) {
						newurl = window.location.href+'&wre-orderby='+orderby;
					} else {
						var search_parameters = search_string.split('&');
						jQuery.each(search_parameters, function(key, value){
							if (value.indexOf("wre-orderby") >= 0) {
								var orderby_value = value.split('=');
								newurl = newurl+orderby_value[0]+'='+orderby;
							} else {
								newurl = newurl+value;
							}
							if( search_parameters.length < (key+1) ) {
								newurl = newurl + '&';
							}
						});
					}
				}
				$('body').find('.wre-pagination').attr('data-orderby', orderby);
				window.history.pushState({path:newurl},'',newurl);
			}
		});

	}

	$('.wre-pagination a').on('click', function(e){
		e.preventDefault();
		var url = $(this).attr('href');
		var orderby = $(this).parents('.wre-pagination').attr('data-orderby');
		url = url+'&wre-orderby='+orderby;
		window.location.href = url;
		return false;
	});

	/**
	 * Buy/Sell option
	 */
	function wp_real_estate_buy_sell() {
		$('.wre-search-form').on('change', 'select.purpose', function () {
			if ($(this).parents('.widget').length == 0) {
				$(this).closest('form').submit();
			}
		});
	}

	/**
	 * View switcher
	 */
	function wp_real_estate_view_switcher() {

		$('.wre-view-switcher div').click(function () {
			var view = $(this).attr('id');
			switch_view(view);
		});

		function switch_view(to) {

			var from = (to == 'list') ? 'grid' : 'list';

			var wre_items = $('.wre-items li');
			$.each(wre_items, function (index, listing) {
				if ($(this).parents('.widget').length == 0) {
					$(this).parents('.wre-items').removeClass(from + '-view');
					$(this).parents('.wre-items').addClass(to + '-view');
				}

			});
		}

	}
	/**
	 * Google map
	 */
	function wp_real_estate_google_map() {
		if (wre.lat) {
			var lat = wre.lat;
			var lng = wre.lng;
			var options = {
				center: new google.maps.LatLng(lat, lng),
				zoom: parseInt(wre.map_zoom),
			}
			var mapClass = $('.wre-map');
			
			$.each(mapClass, function (key, value) {
				wre_map = new google.maps.Map(mapClass[key], options);
				var position = new google.maps.LatLng(lat, lng);
				var set_marker = new google.maps.Marker({
					icon: ' ',
					label: {
						fontFamily: 'wrewp',
						text: "\140",
						fontSize: '60px',
						color: '#44a3d3'
					},
					map: wre_map,
					position: position
				});
			});

		}

	}

	$('.wre-contact-form .cmb-form').on('submit', function (e) {

		e.preventDefault();
		var $form = $(this);
		$form.addClass('in');
		var speed = 700;
		$('html, body').animate({scrollTop: $form.parent().offset().top}, speed);
		$.ajax({
			type: 'POST',
			url: wre.ajax_url,
			data: {
				'action': 'wre_contact_form',
				'data': $(this).serialize(),
			},
			success: function (response) {

				$form.removeClass('in');
				$form.parent().find('.message-wrapper').html(response.message);
				$('html, body').animate({scrollTop: $form.parent().offset().top - 150}, speed);
				$form.find('input#_wre_enquiry_name').val('');
				$form.find('input#_wre_enquiry_email').val('');
				$form.find('input#_wre_enquiry_phone').val('');
				$form.find('textarea#_wre_enquiry_message').val('');

			}
		}).error(function () {
			var html = '<p class="alert error warning alert-warning alert-error">There was an error. Please try again.</p>';
			$form.removeClass('in');
			$form.parent().find('.message-wrapper').html(html);
			$('html, body').animate({scrollTop: $form.parent().offset().top - 150}, speed);
		});
		return false;

	});

	if ($('.search-text-wrap .get-current-location').length > 0) {

		$("input.search-input").geocomplete();

		$('.get-current-location').on('click', function (e) {
			e.preventDefault();
			var $this = $(this);
			if (navigator.geolocation) {

				var geocoder = new google.maps.Geocoder();
				navigator.geolocation.getCurrentPosition(function (position) {

					var lat = position.coords.latitude;
					var lng = position.coords.longitude;
					var latlng = new google.maps.LatLng(lat, lng);
					geocoder.geocode({'latLng': latlng}, function (results, status) {
						if (status == google.maps.GeocoderStatus.OK) {
							if (results[1]) {
								$this.parent().find('.search-input').focus().val(results[0].formatted_address);
							} else {
								alert("No results found");
							}
						} else {
							alert("Geocoder failed due to: " + status);
						}
					});

				});

			}

			return false;
		});

	}

	if ($('.nearby-listings-wrapper').length > 0) {
		var lat, lng;
		if (navigator.geolocation) {
			navigator.geolocation.getCurrentPosition(function (position) {

				lat = position.coords.latitude;
				lng = position.coords.longitude;
				$('.nearby-listings-wrapper').each(function () {

					var $this = $(this);
					var data = {};
					data['current_lat'] = position.coords.latitude;
					data['current_lng'] = position.coords.longitude;
					data['measurement'] = $this.attr('data-distance');
					data['radius'] = $this.attr('data-radius');
					data['number'] = $this.attr('data-number');
					data['compact'] = $this.attr('data-compact');

					$.ajax({
						type: 'POST',
						url: wre.ajax_url,
						data: {
							'action': 'wre_nearby_listings',
							'data': data
						},
						success: function (response) {

							var view = $this.attr('data-listing-view');
							var columns = $this.attr('data-columns');
							$this.html(response.data);
							$this.find('ul.wre-items').addClass(view);
							if (view == 'grid-view')
								$this.find('ul.wre-items li.compact').removeClass('compact');

							$this.find('ul.wre-items li').removeClass(function (index, className) {
								return (className.match(/(^|\s)col-\S+/g) || []).join(' ');
							}).addClass('col-' + columns);

							$this.removeAttr('data-distance data-radius data-number data-compact data-listing-view data-columns');

						}
					}).error(function () {
						$this.html('No listings near your location');
					});

				});
			});
		}
	}
	
	$('body').on('click', '.compare-wrapper .add-to-compare', function(e){
		e.preventDefault();

		var $this = $(this);
		$this.find('.wre-icon-compare').addClass('wre-spin');
		var listing_id = $this.attr('data-id');
		$('body').find('.wre-compare-listings .wre-compare-error.in').removeClass('in');
		$.ajax({
			type: 'POST',
			url: wre.ajax_url,
			data: {
				'action': 'wre_compare_listings',
				'listing_id': listing_id
			},
			success: function (response) {
				if( response.flag ) {
					$this.addClass('hide').parent('.compare-wrapper').find('.compare-output').addClass('in');
					$('body').find('.wre-compare-listings').slideDown();
					$('body').find('ul.wre-compare-lists').append(response.message);
				} else {
					$('body').find('.wre-compare-listings').find('.wre-compare-error').addClass('in').html(response.message);
				}
				$this.find('.wre-icon-compare').removeClass('wre-spin');
				$("body, html").animate({
					scrollTop: $('.wre-compare-listings').offset().top
				}, 600);
			}
		});
		return false;
	});
	
	$(document).on( 'click', '.wre-compare-lists a.remove-listing', function(e) {

		e.preventDefault();
		var $this = $(this);
		$this.find('.wre-icon-close').addClass('wre-spin');
		var listing_id = $this.attr('data-id');
		$.ajax({
			type: 'POST',
			url: wre.ajax_url,
			data: {
				'action': 'wre_remove_compare_listings',
				'listing_id': listing_id
			},
			success: function (response) {
				$this.parent('li').remove();
				$('body').find('a.add-to-compare[data-id="'+ listing_id +'"]').removeClass('hide').parent('.compare-wrapper').find('.compare-output.in').removeClass('in');
				$('body').find('.wre-compare-listings').find('.wre-compare-error').removeClass('in').html('');
				if( $('body').find('ul.wre-compare-lists li').length <= 0 ) {
					$('body').find('.wre-compare-listings').removeClass('in').slideUp();
				}
			}
		}).error(function () {
		}); 

		return false;

	});

})(jQuery);