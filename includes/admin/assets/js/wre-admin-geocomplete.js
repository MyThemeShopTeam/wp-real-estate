/*
 * WP Real Estate plugin by MyThemeShop
 * https://wordpress.com/plugins/wp-real-estate/
 */
(function ($) {

	$("#wre-geocomplete").trigger("geocode");

	var lat = $("input[name=_wre_listing_lat]").val();
	var lng = $("input[name=_wre_listing_lng]").val();

	var location = [lat, lng];
	$("#wre-geocomplete").geocomplete({
		map: ".wre-admin-map",
		details: "#post", // form id
		detailsAttribute: "data-geo",
		types: ["geocode", "establishment"],
		location: location,
		markerOptions: {
			draggable: true
		}
	});

	$("#wre-geocomplete").bind("geocode:dragged", function (event, latLng) {
		$("input[name=_wre_listing_lat]").val(latLng.lat());
		$("input[name=_wre_listing_lng]").val(latLng.lng());
	});

	$("#wre-find").click(function () {
		$("#wre-geocomplete").trigger("geocode");
	});

	$("#wre-reset").click(function () {
		$("#wre-geocomplete").geocomplete("resetMarker");
		return false;
	});

})(jQuery);