/* eslint-env jquery */
(function ($) {
	'use strict';
	function renderChart() {
		jQuery('.smile-absolute-loader').css('visibility', 'visible');
		const e =
			'undefined' !== typeof cpcpGetUrlVars().sd
				? cpcpGetUrlVars().sd
				: '';
		const cpEndDate =
			'undefined' !== typeof cpcpGetUrlVars().ed
				? cpcpGetUrlVars().ed
				: '';
		let cpStyleId =
			'undefined' !== typeof cpcpGetUrlVars().style
				? decodeURIComponent(cpcpGetUrlVars().style)
				: 'all';
		const cpGraphLine =
			'undefined' !== typeof cpcpGetUrlVars().cType
				? cpcpGetUrlVars().cType
				: 'line';
		const cpCompFactor =
			'undefined' !== typeof cpcpGetUrlVars().compFactor
				? cpcpGetUrlVars().compFactor
				: 'imp';
		cpStyleId = cpStyleId.split('||');
		const cpStyleAnalyticsData = 'get_style_analytics_data';
		const cpAnalyticsData = {
			action: cpStyleAnalyticsData,
			module: $('#cp-module').val(),
			chartType: cpGraphLine,
			compFactor: cpCompFactor,
			styleid: cpStyleId,
			startDate: e,
			endDate: cpEndDate,
			security: jQuery('#cp_analytics_nonce').val(),
		};

		const cpAnalyticsResponse = jQuery.ajax({
			url: ajaxurl,
			data: cpAnalyticsData,
			method: 'POST',
			dataType: 'JSON',
			async: !1,
			success(response) {
				const val = JSON.stringify(response);
				jQuery('#cp-module-data').val(val);
			},
		}).responseText;

		setTimeout(function () {
			jQuery('.cp-analytics-filter-section').fadeIn(1e3);
			const analytics_form_width = jQuery(
				'.analytics-form .col-sm-2'
			).width();
			jQuery('.analytics-form .select2-search__field').attr(
				'style',
				'width:' +
					analytics_form_width +
					'px !important; max-width:' +
					analytics_form_width +
					'px !important'
			);
			jQuery('.cp-graph-area').removeClass('cp-hidden');
			jQuery('.smile-absolute-loader').css('visibility', 'hidden');
		}, 1100);
		setTimeout(function () {
			const style_analytics = JSON.parse(cpAnalyticsResponse);
			let t = !1;
			if (
				(jQuery('#chart-legend').html(''),
				'undefined' !== typeof style_analytics.labels &&
					style_analytics.labels.length > 15)
			) {
				t = !0;
			}
			if ('unavailable' !== style_analytics) {
				const a = jQuery('#cp-chart-comp-type option:selected').text(),
					s = jQuery('#cp-chart-comp-type option:selected').val();
				let value = '';
				Chart.defaults.global.pointHitDetectionRadius = 1;
				Chart.defaults.global.customTooltips = function (
					cpStyleAnalytics
				) {
					t = jQuery('#chartjs-tooltip');
					if (!cpStyleAnalytics)
						return void t.css({
							opacity: 0,
						});
					t.removeClass('above below');
					t.addClass(cpStyleAnalytics.yAlign);
					let l = '';
					if ('undefined' !== typeof cpStyleAnalytics.labels) {
						l += [
							'<span class="chartjs-tooltip-title">' +
								a +
								' on ' +
								cpStyleAnalytics.title +
								'<br></span>',
						].join('');
						for (
							let r = cpStyleAnalytics.labels.length - 1;
							r >= 0;
							r--
						) {
							value = cpStyleAnalytics.labels[r].split(':');
							const o = value[0];
							let n = '';
							if ('convRate' === s) n = '%';
							l += [
								'<div class="chartjs-tooltip-section">',
								'<span class="chartjs-tooltip-color" style="background-color:' +
									cpStyleAnalytics.legendColors[r].fill +
									'"></span>' +
									o +
									': <span class="chartjs-tooltip-value">' +
									value[1] +
									' ' +
									n +
									'</span>',
								'</div>',
							].join('');
						}
					}
					if ('undefined' !== typeof cpStyleAnalytics.text) {
						const i =
							(cpStyleAnalytics.chart.ctx,
							cpStyleAnalytics.chart.ctx.strokeStyle);
						const cpAnalyticsValue =
							cpStyleAnalytics.text.split(':');
						const o =
							cpAnalyticsValue[1].length > 15
								? cpAnalyticsValue[1].substring(0, 15) + '..'
								: cpAnalyticsValue[1];
						const c = cpAnalyticsValue[0];
						l += [
							'<span class="chartjs-tooltip-title">' +
								a +
								' On ' +
								c +
								'<br></span>',
						].join('');
						l += [
							'<div class="chartjs-tooltip-section">',
							'<span class="chartjs-tooltip-color" style="background-color:' +
								i +
								'"></span>' +
								o +
								': <span class="chartjs-tooltip-value">' +
								cpAnalyticsValue[2] +
								'</span>',
							'</div>',
						].join('');
					}
					t.html(l);
					t.css({
						opacity: 1,
						left:
							cpStyleAnalytics.chart.canvas.offsetLeft +
							cpStyleAnalytics.x +
							'px',
						top:
							cpStyleAnalytics.chart.canvas.offsetTop +
							cpStyleAnalytics.y +
							'px',
					});
				};
				jQuery('.chart-holder').html('<canvas id="line-chart"  />');
				if ('' !== myChart) myChart.destroy();
				const r = document
						.getElementById('line-chart')
						.getContext('2d'),
					o = {
						responsive: !0,
						skipXLabels: t,
						maintainAspectRatio: !1,
						scaleShowHorizontalLines: !0,
						scaleShowVerticalLines: !1,
						datasetFill: !0,
						bezierCurveTension: 0.3,
						scaleFontFamily: "'Open Sans',sans-serif",
						scaleFontColor: '#444',
						tooltipTitleFontFamily: "'Open Sans',sans-serif",
						tooltipTitleFontSize: 12,
					};
				if ('bar' === cpGraphLine || 'line' === cpGraphLine) {
					o.tooltipTemplate =
						'<%= label %>: <%= datasetLabel %>: <%= value %>';
					o.multiTooltipTemplate =
						'<%= datasetLabel %>: <%= value %>';
				}
				if ('bar' === cpGraphLine) {
					o.legendTemplate =
						'<ul class="legend-list"><% for (var i=0; i<datasets.length; i++){%><li><span style="background-color:<%=datasets[i].strokeColor%>"></span><%if(datasets[i].label){%><%=datasets[i].label%>: <%=datasets[i].bars[0].tpl_var_count%><%}%></li><%}%></ul>';
					myChart = new Chart(r).Bar(style_analytics, o);
				} else if ('line' === cpGraphLine) {
					o.legendTemplate =
						'<ul class="legend-list"><% for (var i=0; i<datasets.length; i++){%><li><span style="background-color:<%=datasets[i].pointColor%>"></span><%if(datasets[i].label){%><%=datasets[i].label%>: <%=datasets[i].points[0].tpl_var_count%><%}%></li><%}%></ul>';
					myChart = new Chart(r).Line(style_analytics, o);
				} else if ('donut' === cpGraphLine) {
					o.customTooltips = !1;
					o.legendTemplate =
						'<ul class="legend-list"><% for (var i=0; i<segments.length; i++){%><li><span style="background-color:<%=segments[i].fillColor%>"></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>';
					myChart = new Chart(r).Doughnut(style_analytics, o);
				} else if ('polararea' === cpGraphLine) {
					o.customTooltips = !1;
					o.legendTemplate =
						'<ul class="legend-list"><% for (var i=0; i<segments.length; i++){%><li><span style="background-color:<%=segments[i].fillColor%>"></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>';
					myChart = new Chart(r).PolarArea(style_analytics, o);
				}

				const i = myChart.generateLegend();
				jQuery('#chart-legend').html(i);
			} else jQuery('.chart-holder').html("<p class='cp-empty-graph'>No data available for selected styles. </p>");
			const c = jQuery('.cp-graph-width').width() + 'px';
			jQuery('#line-chart').css('width', c, 'important');
		}, 1800);
	}

	function cpcpGetUrlVars() {
		const e = {};
		return (
			window.location.href.replace(
				/[?&]+([^=&]+)=([^&]*)/gi,
				function (t, a, l) {
					e[a] = l;
				}
			),
			e
		);
	}

	jQuery(document).ready(function () {
		function cplug_style_graph() {
			const e = jQuery('#style-dropdown').val();
			if (null !== e) {
				if (
					jQuery.inArray('all', e) > -1 ||
					'all' === e ||
					e.length > 1
				) {
					jQuery('#cp-chart-comp-type')
						.find("option[value='impVsconv']")
						.remove();
					jQuery('#cp-chart-comp-type').prop('disabled', !1);
				} else {
					if (
						void 0 ===
						jQuery(
							"#cp-chart-comp-type option[value='impVsconv']"
						).val()
					) {
						jQuery('#cp-chart-comp-type')
							.prepend(
								"<option value='impVsconv'>Impression Vs Conversion</option>"
							)
							.val('impVsconv');
					}
					jQuery('#cp-chart-comp-type').val('impVsconv');
					jQuery('#cp-chart-comp-type').prop('disabled', !1);
				}
			}
		}
		cplug_style_graph();
		jQuery('#style-dropdown').select2();
		const t = jQuery('#style-dropdown');
		jQuery(document).on(
			'click',
			'.select2-selection__choice__remove',
			function () {
				t.select2('close');
			}
		);
		t.on('select2:select', function () {
			cplug_style_graph();
		});
		t.on('select2:unselect', function () {
			cplug_style_graph();
		});
		const a = {
			time: 'connects-icon-clock',
			date: 'dashicons dashicons-calendar-alt',
			up: 'dashicons dashicons-arrow-up-alt2',
			down: 'dashicons dashicons-arrow-down-alt2',
			previous: 'dashicons dashicons-arrow-left-alt2',
			next: 'dashicons dashicons-arrow-right-alt2',
			today: 'dashicons dashicons-screenoptions',
			clear: 'dashicons dashicons-trash',
		};
		jQuery('#cp-startDate')
			.datetimepicker({
				format: 'DD-MM-YYYY',
				maxDate: new Date(),
				icons: a,
			})
			.on('dp.change', function (cpStartDate) {
				jQuery('#cp-endDate')
					.data('DateTimePicker')
					.minDate(cpStartDate.date);
			});
		jQuery('#cp-endDate')
			.datetimepicker({
				format: 'DD-MM-YYYY',
				maxDate: new Date(),
				icons: a,
			})
			.on('dp.change', function (cpEndDate) {
				jQuery('#cp-startDate')
					.data('DateTimePicker')
					.maxDate(cpEndDate.date);
			});
		jQuery('#cp-startDate').on('click', function () {
			jQuery('#cp-startDate').data('DateTimePicker').show();
		});
		jQuery('#cp-endDate').on('click', function () {
			jQuery('#cp-endDate').data('DateTimePicker').show();
		});
		renderChart();
	});

	jQuery(document).on('click', '#submit-query', function () {
		const e = jQuery('#style-dropdown').val(),
			t = jQuery('#cp-startDate').val(),
			a = jQuery('#cp-endDate').val(),
			l = jQuery('#cp-chart-type').val(),
			s = jQuery('#cp-chart-comp-type').val();

		const module = jQuery('#cp-module').val();
		let r = '?page=smile-' + module + '-designer&style-view=analytics';
		if ('' !== t && '' !== a) {
			r += '&sd=' + t + '&ed=' + a;
		}
		const o = e.join('||');
		r +=
			'&style=' +
			encodeURIComponent(o) +
			'&cType=' +
			l +
			'&compFactor=' +
			s;
		window.history.pushState(
			'/admin.php?page=smile-' +
				module +
				'-designer&style-view=analytics',
			'Connects',
			r
		);
		renderChart();
	});

	let myChart = '';
})(jQuery);
