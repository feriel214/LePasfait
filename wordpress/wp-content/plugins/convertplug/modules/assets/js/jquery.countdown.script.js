//for count down modal
/* eslint-env jquery */
function cp_start_count_timer() {
	jQuery('.cp_count_down_main').each(function () {
		const date = jQuery(this).data('date');
		const countupto = new Date(date);
		let counter_option = jQuery(this).data('timeformat');
		let format = '';
		const cp_defaultCountdown = jQuery(this);
		const timezonename = jQuery(this)
			.closest('.global_modal_container')
			.data('timezonename');
		const show_datepicker = jQuery(this).data('showcounter');
		const advnce_countdown = jQuery(this).data('advnce-countdown');
		let layoutopt = '';
		let layoutformat = '';
		const gmt_offset = jQuery(this)
			.closest('.global_modal_container')
			.data('tz-offset');
		const vw = jQuery(window).width();
		let labelsname = jQuery(this).data('counter-labels');
		const cp_labelsname = jQuery(this).data('counter-compact-labels');

		if (show_datepicker === 'show') {
			if (counter_option.length > 0) {
				counter_option = counter_option.split('|');
				jQuery.each(counter_option, function (i, v) {
					format += v;
				});
			} else {
				format = 'YOWDHMS';
			}

			for (let i = 0, len = format.length; i < len; i++) {
				const lower = format[i].toLowerCase();
				layoutformat += '{' + lower + 'n}';
				if (i + 1 !== len) {
					layoutformat += ' {' + lower + 'l}, ';
				} else {
					layoutformat += ' {' + lower + 'l} ';
				}
			}

			//enable-disable layouts
			if (advnce_countdown !== 'style_2') {
				layoutopt = layoutformat;
			} else {
				const lt = format.length;
				//if counter digit greater than 4 then compress labels
				if (vw <= 610 && lt > 3) {
					// labelsname = ['Y','M','W','D','H','Mn','S'];
					labelsname = cp_labelsname;
				}
			}

			//destry prev counter
			cp_defaultCountdown.cp_countdown('destroy');

			if (timezonename === 'wordpress') {
				cp_defaultCountdown.cp_countdown({
					until: countupto,
					timezone: gmt_offset,
					format,
					layout: layoutopt,
					labels: labelsname,
				});
			} else {
				cp_defaultCountdown.cp_countdown({
					until: countupto,
					format,
					layout: layoutopt,
					labels: labelsname,
				});
			}
		}
	});
}

jQuery(document).ready(function () {
	cp_start_count_timer();
});

jQuery(window).on('resize', function () {
	cp_start_count_timer();
});
