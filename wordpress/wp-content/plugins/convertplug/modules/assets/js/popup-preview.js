/* eslint-env jquery */
(function () {
	'use strict';
	jQuery('.style-demo').on('click', function () {
		const style = jQuery(this).data('style'),
			title = jQuery(this).data('title'),
			url = jQuery(this).data('url'),
			style_settings_method = jQuery(this).data('style-settings-method'),
			temp_name = jQuery(this).data('temp-name'),
			module = jQuery(this).data('module');
		displayPopup(
			style,
			title,
			url,
			style_settings_method,
			temp_name,
			module
		);
	});
	function displayPopup(
		style,
		title,
		url,
		style_settings_method,
		temp_name,
		module
	) {
		jQuery('#style_preview_css').remove();
		jQuery('head').append(
			'<link rel="stylesheet" type="text/css" id="style_preview_css" href="#" >'
		);

		jQuery('#style_preview_css').attr('href', url);
		// load thickbox.
		tb_show(
			'Preview - ' + title,
			ajaxurl +
				'?action=cp_display_preview_' +
				module +
				'&style=' +
				style +
				'&method=' +
				style_settings_method +
				'&temp_name=' +
				temp_name
		);

		const loader =
			'<div class="smile-absolute-loader" style="visibility: visible;overflow: hidden;width: 80px;height: 80px;background-color: transparent;"><div class="smile-loader"><div class="smile-loading-bar"></div><div class="smile-loading-bar"></div><div class="smile-loading-bar"></div><div class="smile-loading-bar"></div></div></div>';
		jQuery('#TB_load').html(loader);
		jQuery('#TB_ajaxContent').addClass('cp-live-preview');
		setTimeout(function () {
			if ('modal' === module) {
				jQuery('.cp-overlay').addClass('cp-open');
			}
			jQuery('.slidein-overlay').addClass('si-open');
			jQuery('.cp-info-bar')
				.addClass('ib-display')
				.css('z-index', '100051');
			jQuery('#TB_ajaxContent').appendTo('body');
		}, 2500);
		jQuery('#TB_load').css({
			width: '0',
			height: '0',
			'background-color': 'transparent',
			border: 'none',
			padding: '0',
			margin: '0 auto',
		});
	}
})();
