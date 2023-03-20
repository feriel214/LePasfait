/* eslint-env jquery */
(function () {
	'use strict';
	jQuery(document).ready(function () {
		jQuery('.cp-info-bar').addClass('ib-display');
		jQuery('.cp-info-bar-container').css({
			position: 'fixed',
			'z-index': 9999999,
		});
		jQuery('body').on('click', '.ib-close, .cp-overlay', function () {
			jQuery('.cp-info-bar').removeClass('ib-display');
			jQuery('#TB_ajaxContent').remove();
			jQuery('#TB_overlay').trigger('click');
			jQuery('body').removeClass('modal-open');
		});
		jQuery('body').on('click', '.cp-info_bar-content', function (e) {
			e.preventDefault();
			e.stopPropagation();
		});
		jQuery(document).bind('keydown', function (e) {
			if (27 === e.which) {
				const cp_overlay = jQuery('.ib-display');
				const info_bar = cp_overlay;
				info_bar.fadeOut('slow').remove();
				jQuery('#TB_ajaxContent').remove();
				jQuery('#TB_window').remove();
				jQuery('#TB_overlay').remove();
			}
		});
	});
})();
