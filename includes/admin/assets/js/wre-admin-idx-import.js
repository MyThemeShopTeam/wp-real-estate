/*
 * WP Real Estate plugin by MyThemeShop
 * https://wordpress.com/plugins/wp-real-estate/
 */

jQuery(document).ready(function($) {
	// make sure labels are drawn in the correct state
	$('li').each(function() {
		if ($(this).find(':checkbox').attr('checked'))
			$(this).addClass('selected');
	});

	$(document).on( 'click', '.wre-delete-post', function(e) {
		e.preventDefault();
		var $this = $(this);
		var id = $this.data('id');
		var nonce =$this.data('nonce');
		var post = $this.parents('.post:first');
		$this.css( 'cursor', 'not-allowed' );
		$.ajax({
			type: 'post',
			url: DeleteListingAjax.ajaxurl,
			data: {
				action: 'wre_idx_listing_delete',
				nonce: nonce,
				id: id
			},
			success: function( result ) {
				if( result == 'success' ) {
					$this.parents('li').removeClass('imported selected');
					$this.parents('li').find('.imported').remove();
					$this.parents('li').find('.wre-checkbox').attr( 'checked', false );
					$this.remove();
				}
			}
		});
		return false;
	});

	$(document).on( 'click', '.wre-delete-all', function(e) {
		e.preventDefault();
		var go_ahead = confirm("This will delete all imported listings and their attached images. Are you sure you want to continue?");
		var nonce = $(this).data('nonce');
		var post = $('#selectable').find('.selected');
		
		if ( go_ahead === true ) {
			$(this).parent().find('.wre-delete-all-loader').addClass('in');
			$.ajax({
				type: 'post',
				url: DeleteListingAjax.ajaxurl,
				data: {
					action: 'wre_idx_listing_delete_all',
					nonce: nonce,
				},
				success: function( result ) {
					if( result == 'success' ) {
						$.each(post, function(key,value){
							$(this).removeClass('selected imported').find('.wre-checkbox').attr( 'checked', false );;
							$(this).find('span.imported').remove();
							$(this).find('a.wre-delete-post').remove();
						});
						$('.wre-delete-all-loader').remove();
						$('.wre-delete-all').remove();
					}
				}
			});
		}
		return false;
	});

	// toggle label css when checkbox is clicked
	$(':checkbox').click(function(e) {
		var checked = $(this).attr('checked');
		$(this).closest('li').toggleClass('selected', checked);
	});

	// Select all
	$("#wre-selectall").change(function(){
		$(".wre-checkbox").prop('checked', $(this).prop("checked"));
		if($(this).prop("checked"))
			$('ol.wre-grid').find('li').addClass('selected');
		else
			$('ol.wre-grid').find('li').removeClass('selected');
	});
});