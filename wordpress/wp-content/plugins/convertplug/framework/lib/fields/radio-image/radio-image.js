/* eslint-env jquery */
jQuery(document).ready(function () {
	const rm_container = jQuery('.smile-radio-image-holder');
	rm_container.on('click', function () {
		const $this = jQuery(this);
		jQuery.each(rm_container, function () {
			jQuery(this).removeClass('selected');
		});
		$this.addClass('selected');
		$this.find('input:radio').prop('checked', true);
		$this.find('input.smile-input').trigger('change');

		const r = $this.find('input:radio');
		r.prop('checked', true);

		$this.find('input.smile-radio-image').trigger('change');
		jQuery(document).trigger('smile-radio-image-change', [r]);

		const value = $this.find('input:radio').val();
		const elem = $this.find('input:radio').attr('name');
		jQuery(document).trigger('radio_image_click', [elem, value]);
	});
});
