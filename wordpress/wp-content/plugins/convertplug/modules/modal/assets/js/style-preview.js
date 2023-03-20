/* eslint-env jquery */
(function () {
	'use strict';
	jQuery(document).ready(function () {
		jQuery('#TB_ajaxContent').appendTo('body');
		jQuery('#TB_overlay').remove();
		jQuery('body').on('click', '.cp-overlay', function () {
			jQuery(this).removeClass('cp-open');
			jQuery('#TB_ajaxContent').remove();
			jQuery('#TB_window').remove();
			jQuery('#TB_overlay').trigger('click');
			jQuery('#TB_overlay').remove();
			jQuery('body').removeClass('modal-open');
		});
		jQuery('body').on('click', '.cp-modal-content', function (e) {
			e.preventDefault();
			e.stopPropagation();
		});
		jQuery(document).bind('keydown', function (e) {
			if (e.which === 27) {
				const cp_overlay = jQuery('.cp-open');
				const modal = cp_overlay;
				modal.fadeOut('slow').remove();
				jQuery('#TB_ajaxContent').remove();
				jQuery('#TB_window').remove();
				jQuery('#TB_overlay').remove();
			}
		});
	});
})();
