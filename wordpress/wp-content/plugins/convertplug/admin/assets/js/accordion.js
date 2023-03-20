/* eslint-env jquery */
(function () {
	jQuery.widget('metro.accordion', {
		version: '1.0.0',
		options: {
			closeAny: true,
            open() { }, // eslint-disable-line
            action() { }, // eslint-disable-line
		},
		_frames: {},
		_create() {
			const element = this.element;
			if (element.data('closeany') !== undefined)
				this.options.closeAny = element.data('closeany');
			this.init();
		},
		init() {
			const that = this;
			that.element.on(
				'click',
				'.accordion-frame > .heading',
				function (e) {
					e.preventDefault();
					e.stopPropagation();
					if (
						jQuery(this).attr('disabled') ||
						jQuery(this).data('action') === 'none'
					)
						return;
					if (that.options.closeAny) that._closeFrames();
					const frame = jQuery(this).parent(),
						content = frame.children('.content');
					if (jQuery(content).is(':hidden')) {
						jQuery(content).slideDown();
						jQuery(this).removeClass('collapsed');
						that._trigger('frame', e, { frame });
						that.options.open(frame);
					} else {
						jQuery(content).slideUp();
						jQuery(this).addClass('collapsed');
					}
					that.options.action(frame);
				}
			);
			const frames = this.element.children('.accordion-frame');
			frames.each(function () {
				const frame = this,
					a = jQuery(this).children('.heading'),
					content = jQuery(this).children('.content');
				if (
					jQuery(frame).hasClass('active') &&
					!jQuery(frame).attr('disabled') &&
					jQuery(frame).data('action') !== 'none'
				) {
					jQuery(content).show();
					jQuery(a).removeClass('collapsed');
				} else {
					jQuery(a).addClass('collapsed');
				}
			});
		},
		_closeFrames() {
			const frames = this.element.find('.accordion-frame');
			jQuery.each(frames, function () {
				const frame = jQuery(this);
				frame.find('.heading').addClass('collapsed');
				frame.find('.content').slideUp();
			});
		},
		_destroy() {},
		_setOption(key, value) {
			this._super('_setOption', key, value);
		},
	});
})(jQuery);
