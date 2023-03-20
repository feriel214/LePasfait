/* global jQuery:false */
/* global TRX_ADDONS_STORAGE:false */

jQuery(document).ready(function(){	
	"use strict";
	
	// Show value slider on hover
	if (jQuery(".sc_reviews .rating_item").length > 0) {		
		jQuery(".sc_reviews .rating_item.allow_voting .mark_wrap").mousemove(function(c) {	
			var mark_wrap = jQuery(this);
			var rating_max = mark_wrap.parent().data('level');
				
			var b = mark_wrap.width();
			var a = c.pageX - mark_wrap.offset().left + 1;
			if (a == 1) {
				a = 0;
			}
			if (a <= b) {
				var d = Math.round(a / b * 100);
				mark_wrap.find(".rat_bubble").css({"left": d + "%", "opacity" : "1"}).find('.value').text(trx_addons_rating2show(d, rating_max));
			}
		});
		
		// Hide value slider
		jQuery(".sc_reviews .rating_item.allow_voting").mouseleave(function() {
			var rating_item = jQuery(this);
			setTimeout(function(){
				rating_item.find(".rat_bubble").css({"opacity" : "0"});
			}, 2500);
		});
		
		// Change rating
		jQuery(".sc_reviews .rating_item.allow_voting").on('click', '.mark_wrap', function(e) {
			var rating_item = jQuery(this).parents('.rating_item'),
				post_id = rating_item.data('post_id'),
				stored_marks = trx_addons_get_storage('trx_addons_rating_marks');

			// If visitor already marked this post
			if (stored_marks)
				stored_marks = JSON.parse(stored_marks);
			else
				stored_marks = {};

			if (typeof stored_marks[post_id] != 'undefined' ) {
				// Show error message
				rating_item
						.find('.message_error')
							.text(TRX_ADDONS_STORAGE['msg_rating_already_marked'].replace('{{X}}', stored_marks[post_id]))
							.slideDown(function() {
													setTimeout(function() {
														rating_item.find('.message_error').slideUp();
													}, 3000);
							});

			// Save vote of current user
			} else {
				var rating_max = rating_item.data('level'),
					mark = rating_item.find(".rat_bubble .value").text();
				
				rating_item.find('.rat_bubble').addClass('loading');	
				
				jQuery.post(TRX_ADDONS_STORAGE['ajax_url'], {
					action: 'post_rating',
					nonce: TRX_ADDONS_STORAGE['ajax_nonce'],
					post_id: post_id,
					mark: mark,
					mark_max: rating_max
				}).done(function(response) {
					var rez = {};
					try {
						rez = JSON.parse(response);
					} catch (e) {
						console.log(response);
					}
					rating_item.find('.rat_bubble').removeClass('loading');	

					if (rez.rating !== -1) {
						// Save mark to the storage
						stored_marks[post_id] = mark;
						trx_addons_set_storage('trx_addons_rating_marks', JSON.stringify(stored_marks));

						// Update stars
						var rating_width =  Math.round( rez.rating * 100 / rating_max * 10) / 10;		
						var	pos = 'left';
						if (rating_width < 50) pos = 'right';					
						rating_item.find('.mark_hover').css('width', rating_width + '%');		
						rating_item.find('.mark_hover .mark').addClass(pos).text(rez.rating);	
						
						// Update totals
						rating_item.find('.rating_text_value').html(rez.rating);
						rating_item.find('.rating_text_votes').html(parseInt(rating_item.find('.rating_text_votes').text(), 10) + 1);
						
						// Show result message
						rating_item
							.find('.message_success')
								.slideDown(function() {
														setTimeout(function(){
															rating_item.find('.message_success').slideUp();
														}, 3000);
								});	
					} 
					if (rez.error !== '') {
						// Show result message
						rating_item.find('.message_error').text(rez.error).slideDown(function() {
							setTimeout(function() {
								rating_item.find('.message_error').slideUp();
							}, 3000);
						});
					}
				});
			}
			e.preventDefault();
			return false;
		});
	}

	// Convert rating width to the display equivalent
	function trx_addons_rating2show(mark, rating_max) {
	    if (rating_max != 100) {
	        mark = Math.round(mark / 100 * rating_max * 10) / 10;
	        if (String(mark).indexOf(".") < 0) {
	            mark += ".0";
	        }
	    }
	    return mark;
	}
});

