/* eslint-env jquery */
jQuery(document).ready(function () {
	jQuery('#list-dropdown').select2();

	const $example = jQuery('#list-dropdown');
	jQuery(document).on(
		'click',
		'.select2-selection__choice__remove',
		function () {
			$example.select2('close');
		}
	);

	jQuery('#cp-startDate')
		.datetimepicker({
			format: 'DD-MM-YYYY',
			maxDate: new Date(),
			icons: {
				time: 'connects-icon-clock',
				date: 'dashicons dashicons-calendar-alt',
				up: 'dashicons dashicons-arrow-up-alt2',
				down: 'dashicons dashicons-arrow-down-alt2',
				previous: 'dashicons dashicons-arrow-left-alt2',
				next: 'dashicons dashicons-arrow-right-alt2',
				today: 'dashicons dashicons-screenoptions',
				clear: 'dashicons dashicons-trash',
			},
		})
		.on('dp.change', function (e) {
			jQuery('#cp-endDate').data('DateTimePicker').minDate(e.date);
		});

	jQuery('#cp-endDate')
		.datetimepicker({
			format: 'DD-MM-YYYY',
			maxDate: new Date(),
			icons: {
				time: 'dashicons dashicons-clock',
				date: 'dashicons dashicons-calendar-alt',
				up: 'dashicons dashicons-arrow-up-alt2',
				down: 'dashicons dashicons-arrow-down-alt2',
				previous: 'dashicons dashicons-arrow-left-alt2',
				next: 'dashicons dashicons-arrow-right-alt2',
				today: 'dashicons dashicons-screenoptions',
				clear: 'dashicons dashicons-trash',
			},
		})
		.on('dp.change', function (e) {
			jQuery('#cp-startDate').data('DateTimePicker').maxDate(e.date);
		});

	renderChart();
});

let myChart = '';

jQuery(document).on('click', '#submit-query', function () {
	const campaign = jQuery('#list-dropdown').val();
	const startDate = jQuery('#cp-startDate').val();
	const endDate = jQuery('#cp-endDate').val();
	const chartType = jQuery('#cp-chart-type').val();

	let getParams = '?page=contact-manager&view=analytics';

	if (startDate !== '' && endDate !== '') {
		getParams += '&sd=' + startDate + '&ed=' + endDate;
	}

	const campaignParam = campaign.join('||');
	getParams +=
		'&campaign=' +
		encodeURIComponent(campaignParam) +
		'&cType=' +
		chartType;

	window.history.pushState(
		'/admin.php?page=contact-manager&view=analytics',
		'Connects',
		getParams
	);
	renderChart();
});

function renderChart() {
	jQuery('.smile-absolute-loader').css('visibility', 'visible');
	const startDate =
		typeof cpcpGetUrlVars().sd !== 'undefined' ? cpcpGetUrlVars().sd : '';
	const endDate =
		typeof cpcpGetUrlVars().ed !== 'undefined' ? cpcpGetUrlVars().ed : '';
	let campaign =
		typeof cpcpGetUrlVars().campaign !== 'undefined'
			? decodeURIComponent(cpcpGetUrlVars().campaign)
			: 'all';
	const chartType =
		typeof cpcpGetUrlVars().cType !== 'undefined'
			? cpcpGetUrlVars().cType
			: 'line';

	campaign = campaign.split('||');
	let action = 'get_campaign_daywise_data';
	if (chartType === 'donut' || chartType === 'polararea')
		action = 'get_campaign_analytics_data';

	const data = {
		action,
		chartType,
		listid: campaign,
		startDate,
		endDate,
		security_nonce: analytics_nonce.analytics_nonce,
	};
	const campData = jQuery.ajax({
		url: ajaxurl,
		data,
		method: 'POST',
		dataType: 'JSON',
		async: false,
	}).responseText;

	setTimeout(function () {
		jQuery('.cp-analytics-filter-section').fadeIn(1000);
		const formcol5Width = jQuery('.analytics-form .form-col-5').width();
		jQuery('.analytics-form .select2-search__field').attr(
			'style',
			'width:' +
				formcol5Width +
				'px !important; max-width:' +
				formcol5Width +
				'px !important'
		);
		jQuery('.cp-graph-area').removeClass('cp-hidden');
		jQuery('.chart-holder').html('');
		jQuery('.smile-absolute-loader').css('visibility', 'hidden');
	}, 1100);

	setTimeout(function () {
		jQuery('#chart-legend').html('');

		const chartData = JSON.parse(campData);
		let skipYaxisLabels = false;
		if (
			typeof chartData.labels !== 'undefined' &&
			chartData.labels.length > 15
		) {
			skipYaxisLabels = true;
		}
		if (chartData !== 'unavailable') {
			if (chartType === 'line' || chartType === 'bar') {
				let bgcolor = '';

				if (
					typeof chartData.datasets !== 'undefined' &&
					chartData.datasets.length === 1
				) {
					if (chartType === 'line')
						bgcolor = chartData.datasets[0].pointStrokeColor;
					else bgcolor = chartData.datasets[0].strokeColor;
				}

				Chart.defaults.global.pointHitDetectionRadius = 1;
				Chart.defaults.global.customTooltips = function (tooltip) {
					const tooltipEl = jQuery('#chartjs-tooltip');
					if (!tooltip) {
						tooltipEl.css({
							opacity: 0,
						});
						return;
					}
					tooltipEl.removeClass('above below');
					tooltipEl.addClass(tooltip.yAlign);
					let innerHtml = '';
					let seriesLabel = '';
					if (typeof tooltip.labels !== 'undefined') {
						innerHtml += [
							'<span class="chartjs-tooltip-title">No. of contacts on ' +
								tooltip.title +
								'<br></span>',
						].join('');

						for (let i = tooltip.labels.length - 1; i >= 0; i--) {
							const value = tooltip.labels[i].split(':');

							if (value[0].length > 15)
								seriesLabel = value[0].substring(0, 15) + '..';
							else seriesLabel = value[0];

							innerHtml += [
								'<div class="chartjs-tooltip-section">',
								'<span class="chartjs-tooltip-color" style="background-color:' +
									tooltip.legendColors[i].fill +
									'"></span>' +
									seriesLabel +
									': <span class="chartjs-tooltip-value">' +
									value[1] +
									'</span>',
								'</div>',
							].join('');
						}
					}

					if (typeof tooltip.text !== 'undefined') {
						if (bgcolor === '') {
							bgcolor = tooltip.chart.ctx.strokeStyle;
						}
						const value = tooltip.text.split(':');
						seriesLabel = value[1];
						const date = value[0];

						if (chartType === 'line' || chartType === 'bar') {
							innerHtml += [
								'<span class="chartjs-tooltip-title">No. of contacts on ' +
									date +
									'<br></span>',
							].join('');
						} else {
							innerHtml += [
								'<span class="chartjs-tooltip-title">No. of contacts for ' +
									date +
									'<br></span>',
							].join('');
						}

						innerHtml += [
							'<div class="chartjs-tooltip-section">',
							'<span class="chartjs-tooltip-color" style="background-color:' +
								bgcolor +
								'"></span>' +
								seriesLabel +
								': <span class="chartjs-tooltip-value">' +
								value[2] +
								'</span>',
							'</div>',
						].join('');
					}

					tooltipEl.html(innerHtml);
					tooltipEl.css({
						opacity: 1,
						left:
							tooltip.chart.canvas.offsetLeft + tooltip.x + 'px',
						top: tooltip.chart.canvas.offsetTop + tooltip.y + 'px',
					});
				};
			}

			jQuery('.chart-holder').html('<canvas id="line-chart"  />');

			if (myChart !== '') {
				myChart.destroy();
			}
			const ctx = document.getElementById('line-chart').getContext('2d');

			const chartoptions = {
				responsive: true,

				skipXLabels: skipYaxisLabels,

				// Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
				maintainAspectRatio: false,

				//Boolean - Whether to show horizontal lines (except X axis)
				scaleShowHorizontalLines: true,

				//Boolean - Whether to show vertical lines (except Y axis)
				scaleShowVerticalLines: false,

				//Boolean - Whether to fill the dataset with a colour
				datasetFill: true,

				// scaleShowGridLines: false,

				bezierCurveTension: 0.3,

				scaleFontFamily: "'Open Sans',sans-serif",

				// String - Scale label font colour
				scaleFontColor: '#444',

				// String - Tooltip title font declaration for the scale label
				tooltipTitleFontFamily: "'Open Sans',sans-serif",

				// Number - Tooltip title font size in pixels
				tooltipTitleFontSize: 12,
			};

			if (chartType === 'bar' || chartType === 'line') {
				chartoptions.tooltipTemplate =
					'<%= label %>: <%= datasetLabel %>: <%= value %>';
				chartoptions.multiTooltipTemplate =
					'<%= datasetLabel %>: <%= value %>';
			}

			if (chartType === 'bar') {
				chartoptions.legendTemplate =
					'<ul class="legend-list"><% for (var i=0; i<datasets.length; i++){%><li><span style="background-color:<%=datasets[i].strokeColor%>"></span><%if(datasets[i].label){%><%=datasets[i].label%>: <%=datasets[i].bars[0].tpl_var_count%><%}%></li><%}%></ul>';
				myChart = new Chart(ctx).Bar(chartData, chartoptions);
			} else if (chartType === 'line') {
				chartoptions.legendTemplate =
					'<ul class="legend-list"><% for (var i=0; i<datasets.length; i++){%><li><span style="background-color:<%=datasets[i].pointColor%>"></span><%if(datasets[i].label){%><%=datasets[i].label%>: <%=datasets[i].points[0].tpl_var_count%><%}%></li><%}%></ul>';
				myChart = new Chart(ctx).Line(chartData, chartoptions);
			} else if (chartType === 'donut') {
				chartoptions.customTooltips = false;
				chartoptions.legendTemplate =
					'<ul class="legend-list"><% for (var i=0; i<segments.length; i++){%><li><span style="background-color:<%=segments[i].fillColor%>"></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>';
				myChart = new Chart(ctx).Doughnut(chartData, chartoptions);
			} else if (chartType === 'polararea') {
				chartoptions.customTooltips = false;
				//String - A legend template
				chartoptions.legendTemplate =
					'<ul class="legend-list"><% for (var i=0; i<segments.length; i++){%><li><span style="background-color:<%=segments[i].fillColor%>"></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>';
				myChart = new Chart(ctx).PolarArea(chartData, chartoptions);
			}

			//generate the legend
			const legend = myChart.generateLegend();

			// append legend to your page somewhere
			jQuery('#chart-legend').html(legend);
		} else {
			jQuery('.chart-holder').html(
				"<p class='cp-empty-graph'>No data available for selected campaigns. </p>"
			);
		}

		const cpGraphwidth = jQuery('.cp-graph-width').width() + 'px';

		jQuery('#line-chart').css('width', cpGraphwidth, 'important');
	}, 1400);
}

function cpcpGetUrlVars() {
	const vars = {};
	window.location.href.replace(
		/[?&]+([^=&]+)=([^&]*)/gi,
		function (m, key, value) {
			vars[key] = value;
		}
	);
	return vars;
}
