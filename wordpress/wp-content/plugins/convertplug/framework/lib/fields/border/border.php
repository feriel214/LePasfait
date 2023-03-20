<?php
/**
 * Prohibit direct script loading.
 *
 * @package Convert_Plus.
 */

// Add new input type border.
if ( function_exists( 'smile_add_input_type' ) ) {
	smile_add_input_type( 'border', 'cp_border_settings_field' );
}

add_action( 'admin_enqueue_scripts', 'enqueue_border_param_scripts' );

/**
 * Name: enqueue_border_param_scripts description.
 *
 * @param  string $hook parameter.
 */
function enqueue_border_param_scripts( $hook ) {
	if ( isset( $_REQUEST['cp_admin_page_nonce'] ) && wp_verify_nonce( $_REQUEST['cp_admin_page_nonce'], 'cp_admin_page' ) ) {
		$cp_page = strpos( $hook, CP_PLUS_SLUG );
		$data    = get_option( 'convert_plug_debug' );
		if ( false !== $cp_page && isset( $_GET['style-view'] ) && ( 'edit' == $_GET['style-view'] || 'variant' == $_GET['style-view'] ) ) {
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'jquery-ui-core' );
			wp_enqueue_script( 'convert-plus-border-radius', SMILE_FRAMEWORK_URI . '/lib/fields/border/js/borderRadius.js', array( 'jquery' ), CP_VERSION, false );

			wp_enqueue_style( 'jquery-ui' );

			if ( isset( $data['cp-dev-mode'] ) && '1' == $data['cp-dev-mode'] ) {
				wp_enqueue_style( 'convert-plus-border-layout', SMILE_FRAMEWORK_URI . '/lib/fields/border/css/layout.css', array(), CP_VERSION );
			}
		}
	}
}

/**
 * Function to handle new input type "border".
 *
 * @param  string $name    name parameter.
 * @param  array  $settings settings parameter.
 * @param  string $value    value parameter.
 * @return string/html           parameter.
 */
function cp_border_settings_field( $name, $settings, $value ) {
	$input_name = $name;
	$type       = isset( $settings['type'] ) ? $settings['type'] : '';
	$class      = isset( $settings['class'] ) ? $settings['class'] : '';

	// Apply partials.
	$partials = generate_partial_atts( $settings );

	$output = '<p><textarea id="border-code" class="content form-control smile-input smile-' . esc_attr( $type ) . ' ' . esc_attr( $input_name ) . ' ' . esc_attr( $type ) . ' ' . esc_attr( $class ) . '" name="' . esc_attr( $input_name ) . '" rows="6" cols="6" ' . wp_specialchars_decode( esc_attr( $partials ), ENT_QUOTES ) . '>' . esc_html( $value ) . '</textarea></p>';

	$pairs    = explode( '|', $value );
	$settings = array();
	if ( is_array( $pairs ) && ! empty( $pairs ) && count( $pairs ) > 1 ) {
		foreach ( $pairs as $pair ) {
			$values                 = explode( ':', $pair );
			$settings[ $values[0] ] = $values[1];
		}
	}

	$all_corners       = isset( $settings['br_all'] ) ? $settings['br_all'] : 10;
	$top_left          = isset( $settings['br_tl'] ) ? $settings['br_tl'] : 10;
	$top_right         = isset( $settings['br_tr'] ) ? $settings['br_tr'] : 10;
	$bottom_left       = isset( $settings['br_bl'] ) ? $settings['br_bl'] : 10;
	$bottom_right      = isset( $settings['br_br'] ) ? $settings['br_br'] : 10;
	$border_style      = isset( $settings['style'] ) ? $settings['style'] : 'solid';
	$border_color      = isset( $settings['color'] ) ? $settings['color'] : 'rgb(68,68,68)';
	$br_type           = isset( $settings['br_type'] ) ? $settings['br_type'] : 0;
	$allsides          = isset( $settings['bw_all'] ) ? $settings['bw_all'] : 0;
	$top               = isset( $settings['bw_t'] ) ? $settings['bw_t'] : 0;
	$left              = isset( $settings['bw_l'] ) ? $settings['bw_l'] : 0;
	$right             = isset( $settings['bw_r'] ) ? $settings['bw_r'] : 0;
	$bottom            = isset( $settings['bw_b'] ) ? $settings['bw_b'] : 0;
	$bw_type           = isset( $settings['bw_type'] ) ? $settings['bw_type'] : 0;
	$br_switch_checked = ( $br_type ) ? 'checked="checked"' : '';
	$bw_switch_checked = ( $bw_type ) ? 'checked="checked"' : '';

	$borders = array(
		__( 'Solid', 'smile' )  => 'solid',
		__( 'Dotted', 'smile' ) => 'dotted',
		__( 'Dashed', 'smile' ) => 'dashed',
		__( 'Double', 'smile' ) => 'double',
		__( 'Groove', 'smile' ) => 'groove',
		__( 'Ridge', 'smile' )  => 'ridge',
		__( 'Inset', 'smile' )  => 'inset',
		__( 'Outset', 'smile' ) => 'outset',
		__( 'None', 'smile' )   => 'none',
	);

	$uniq = uniqid();

	ob_start();
	echo wp_kses_post( $output );
	?>
	<div class="box">
		<div class="holder">
			<div class="frame">
				<div class="setting-block">
					<div class="row">
						<strong>
							<label for="vertical-length"><?php esc_html_e( 'Border Style', 'smile' ); ?></label>
						</strong>
						<div class="text-1 border-selector">
							<select id="select-border" class="smile-input">
								<?php
								foreach ( $borders as $title => $border ) {
									echo '<option value="' . esc_attr( $border ) . '" ' . selected( $border_style, $border, false ) . '>' . esc_attr( $title ) . '</option>';
								}
								?>
							</select>
						</div>
					</div>
				</div>

				<div class="setting-block"
				<?php
				if ( 'none' == $border_style ) {
					echo 'style=display:none;';}
				?>
					>
					<div class="row bordercolor-block">
						<strong><label for="br-color"><?php esc_html_e( 'Border Color', 'smile' ); ?></label></strong>
						<div class="text-2">
							<input id="br-color" class="cs-wp-color-picker " data-default-color="<?php echo esc_attr( $border_color ); ?>" type="text" value="<?php echo esc_attr( $border_color ); ?>">
						</div>
					</div>
				</div>

				<div class="setting-block"
				<?php
				if ( 'none' == $border_style ) {
					echo 'style=display:none;';}
				?>
					>
					<div class="borderwidth-block">
						<div class="smile-param-lable">
							<strong><label for="border_radius"><?php esc_html_e( 'Border Width', 'smile' ); ?></label></strong>
						</div>
						<div class="param-advanced-switch">
							<div class="switch-wrapper param-switch">
								<input type="text" <?php echo esc_attr( $bw_switch_checked ); ?> id="smile_adv_borderwidth_opt" class="form-control smile-input smile-switch-input"  value="<?php echo esc_attr( $bw_type ); ?>" />
								<input type="checkbox" <?php echo esc_attr( $bw_switch_checked ); ?> id="smile_adv_borderwidth_opt_btn_<?php echo esc_attr( $uniq ); ?>" class="ios-toggle smile-input smile-switch-input switch-checkbox smile-switch-adv-options" value="<?php echo esc_attr( $bw_type ); ?>" >
								<label class="smile-switch-btn checkbox-label" data-on="ADVANCED"  data-off="BASIC" data-id="smile_adv_borderwidth_opt" for="smile_adv_borderwidth_opt_btn_<?php echo esc_attr( $uniq ); ?>">
								</label>
							</div>
						</div>
						<div class="param-basic-block borderwidth-container
						<?php
						if ( $type ) {
							echo 'smile-param-hidden';}
						?>
							">
							<div class="setting-block all-sides">
								<div class="row">
									<label class="align-right" for="width-allsides">px</label>
									<div class="text-1">
										<input id="width-allsides" class="sm-small-inputs" type="number" min="0" value="<?php echo esc_attr( $allsides ); ?>">
									</div>
								</div>
								<div id="slider-width-allsides" class="slider-bar large ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all">
									<a class="ui-slider-handle ui-state-default ui-corner-all" href="#" style="left: 0%;"></a>
									<span class="range-quantity" ></span>
								</div>
							</div>
						</div>
						<div class="param-advanced-block borderwidth-container
						<?php
						if ( $type ) {
							echo 'smile-param-hidden';}
						?>
							">
							<div class="setting-block">
								<div class="row">
									<label for="top"><?php esc_html_e( 'Top', 'smile' ); ?></label>
									<label class="align-right" for="top">px</label>
									<div class="text-1">
										<input id="width-top" class="sm-small-inputs" type="number" min="0" value="<?php echo esc_attr( $top ); ?>">
									</div>
								</div>
								<div id="slider-width-top" class="slider-bar large ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all">
									<a class="ui-slider-handle ui-state-default ui-corner-all" href="#"></a>
									<span class="range-quantity" ></span>
								</div>
								<div class="row mtop15">
									<label for="width-left"><?php esc_html_e( 'Left', 'smile' ); ?></label>
									<label class="align-right" for="left">px</label>
									<div class="text-1">
										<input id="width-left" class="sm-small-inputs" type="number" min="0" value="<?php echo esc_attr( $left ); ?>">
									</div>
								</div>
								<div id="slider-width-left" class="slider-bar large ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all">
									<a class="ui-slider-handle ui-state-default ui-corner-all" href="#"></a>
									<span class="range-quantity" ></span>
								</div>
								<div class="row mtop15">
									<label for="right"><?php esc_html_e( 'Right', 'smile' ); ?></label>
									<label class="align-right" for="right">px</label>
									<div class="text-1">
										<input id="width-right" class="sm-small-inputs" type="number" min="0" value="<?php echo esc_attr( $right ); ?>">
									</div>
								</div>
								<div id="slider-width-right" class="slider-bar large ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all">
									<a class="ui-slider-handle ui-state-default ui-corner-all" href="#"></a>
									<span class="range-quantity" ></span>
								</div>
								<div class="row mtop15">
									<label for="bottom"><?php esc_html_e( 'Bottom', 'smile' ); ?></label>
									<label class="align-right" for="bottom">px</label>
									<div class="text-1">
										<input id="width-bottom" class="sm-small-inputs" type="number" min="0" value="<?php echo esc_attr( $bottom ); ?>">
									</div>
								</div>
								<div id="slider-width-bottom" class="slider-bar large ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all">
									<a class="ui-slider-handle ui-state-default ui-corner-all" href="#"></a>
									<span class="range-quantity" ></span>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="setting-block">
					<div class="borderradius-block">
						<div class="param-advanced-switch">
							<div class="switch-wrapper param-switch">
								<input type="text" <?php echo esc_attr( $br_switch_checked ); ?> id="smile_adv_border_opt" class="form-control smile-input smile-switch-input"  value="<?php echo esc_attr( $br_type ); ?>" />
								<input type="checkbox" <?php echo esc_attr( $br_switch_checked ); ?> id="smile_adv_border_opt_btn_<?php echo esc_attr( $uniq ); ?>" class="ios-toggle smile-input smile-switch-input switch-checkbox smile-switch-adv-options" value="<?php echo esc_attr( $br_type ); ?>" >
								<label class="smile-switch-btn checkbox-label" data-on="ADVANCED"  data-off="BASIC" data-id="smile_adv_border_opt" for="smile_adv_border_opt_btn_<?php echo esc_attr( $uniq ); ?>">
								</label>
							</div>
						</div>
						<div class="smile-param-lable">
							<strong><label for="border_radius"><?php esc_html_e( 'Border Radius', 'smile' ); ?></label></strong>
						</div>
						<div class="param-basic-block border-container
						<?php
						if ( $type ) {
							echo 'smile-param-hidden';}
						?>
							">
							<div class="setting-block radius-block">
								<div class="row">
									<label class="align-right" for="all-corners">px</label>
									<div class="text-1">
										<input id="all-corners" class="sm-small-inputs" type="number" min="0" value="<?php echo esc_attr( $all_corners ); ?>">
									</div>
								</div>
								<div id="slider-all-corners" class="slider-bar large ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all">
									<a class="ui-slider-handle ui-state-default ui-corner-all" href="#"></a>
									<span class="range-quantity" ></span>
								</div>
							</div>
						</div>
						<div class="param-advanced-block border-container
						<?php
						if ( ! $type ) {
							echo 'smile-param-hidden';}
						?>
							">
							<div class="setting-block">
								<div class="row">
									<label for="top-left"><?php esc_html_e( 'Top Left', 'smile' ); ?></label>
									<label class="align-right" for="top-left">px</label>
									<div class="text-1">
										<input id="top-left" class="sm-small-inputs" type="number" min="0" value="<?php echo esc_attr( $top_left ); ?>">
									</div>
								</div>
								<div id="slider-top-left" class="slider-bar large ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all">
									<a class="ui-slider-handle ui-state-default ui-corner-all" href="#"></a>
									<span class="range-quantity" ></span>
								</div>
								<div class="row mtop15">
									<label for="top-right"><?php esc_html_e( 'Top Right', 'smile' ); ?></label>
									<label class="align-right" for="top-right">px</label>
									<div class="text-1">
										<input id="top-right" class="sm-small-inputs" type="number" min="0" value="<?php echo esc_attr( $top_right ); ?>">
									</div>
								</div>
								<div id="slider-top-right" class="slider-bar large ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all">
									<a class="ui-slider-handle ui-state-default ui-corner-all" href="#"></a>
									<span class="range-quantity" ></span>
								</div>
								<div class="row mtop15">
									<label for="bottom-left"><?php esc_html_e( 'Bottom Left', 'smile' ); ?></label>
									<label class="align-right" for="bottom-left">px</label>
									<div class="text-1">
										<input id="bottom-left" class="sm-small-inputs" type="number" min="0" value="<?php echo esc_attr( $bottom_left ); ?>">
									</div>
								</div>
								<div id="slider-bottom-left" class="slider-bar large ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all">
									<a class="ui-slider-handle ui-state-default ui-corner-all" href="#"></a><span class="range-quantity" ></span>
								</div>
								<div class="row mtop15">
									<label for="bottom-right"><?php esc_html_e( 'Bottom Right', 'smile' ); ?></label>
									<label class="align-right" for="bottom-right">px</label>
									<div class="text-1">
										<input id="bottom-right" class="sm-small-inputs" type="number" min="0" value="<?php echo esc_attr( $bottom_right ); ?>">
									</div>
								</div>
								<div id="slider-bottom-right" class="slider-bar large ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all">
									<a class="ui-slider-handle ui-state-default ui-corner-all" href="#"></a>
									<span class="range-quantity" ></span>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php
	return ob_get_clean();
}
