/* eslint-env jquery */
(function () {
	'use strict';
	jQuery(document).ready(function () {
		jQuery('.slidein-overlay').addClass('si-open');
		jQuery('body').on('click', '.slidein-overlay', function () {
			jQuery(this).removeClass('si-open');
			jQuery('#TB_ajaxContent').remove();
			jQuery('#TB_window').remove();
			jQuery('#TB_overlay').trigger('click');
			jQuery('body').removeClass('modal-open');
			jQuery('#TB_overlay').remove();
		});
		jQuery('body').on('click', '.cp-slidein-content', function (e) {
			e.preventDefault();
			e.stopPropagation();
		});
	});
})();
