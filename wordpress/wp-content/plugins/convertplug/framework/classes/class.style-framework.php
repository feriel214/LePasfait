<?php
/**
 * Prohibit direct script loading.
 *
 * @package Convert_Plus.
 */

defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

if ( ! function_exists( 'smile_style_dashboard' ) ) {
	/**
	 * Function Name: smile_style_dashboard.
	 *
	 * @param string $class       string parameter.
	 * @param string $option_name string parameter.
	 * @param string $module      string parameter.
	 */
	function smile_style_dashboard( $class, $option_name, $module ) {
		if ( isset( $_REQUEST['cp_admin_page_nonce'] ) && wp_verify_nonce( $_REQUEST['cp_admin_page_nonce'], 'cp_admin_page' ) ) {
			$html          = '';
			$settings      = $class::$options;
			$all_settings  = $settings;
			$styles        = array();
			$style_opts    = array();
			$panels        = array();
			$new_panels    = array();
			$style_content = array();
			$all_styles    = array();
			$url_data      = $_GET;
			$ge_theme      = isset( $url_data['theme'] ) ? esc_attr( $url_data['theme'] ) : '';
			$style_view    = isset( $url_data['style-view'] ) ? esc_attr( $url_data['style-view'] ) : '';

			$category_data = array();
			foreach ( $all_settings as $style => $options ) {
				$all_opts   = array();
				$all_opts[] = $style;
				$all_opts[] = $options['style_name'];
				$all_opts[] = $options['demo_url'];
				$all_opts[] = $options['img_url'];
				$all_opts[] = $options['customizer_js'];
				$all_opts[] = $options['tags'];

				$all_styles[ $options['style_name'] ] = $all_opts;
			}

			$preset_templates = get_option( 'cp_' . $module . '_preset_templates' );

			if ( is_array( $preset_templates ) ) {
				$all_styles = array_merge( $all_styles, $preset_templates );
			}

			if ( ! empty( $settings ) ) {
				$panels         = array();
				$theme_sections = array();
				$theme_array    = array();

				foreach ( $settings as $style => $options ) {
					if ( $style !== $ge_theme && 'edit' === $style_view ) {
						continue;
					}
					$opts         = array();
					$new_panels   = array();
					$new_sections = array();
					$opts[]       = $style;
					$opts[]       = $options['style_name'];
					$opts[]       = esc_url( $options['demo_url'] );
					$opts[]       = esc_url( $options['img_url'] );
					$opts[]       = $options['customizer_js'];

					if ( ! isset( $options['category'] ) || null === $options['category'] ) {
						$category = 'promotions';
					} else {
						$category = $options['category'];
					}

					if ( ! isset( $options['tags'] ) || null === $options['tags'] ) {
						$tags = 'promotions';
					} else {
						$tags = $options['tags'];
					}

					$category_data[] = $category;

					$opts[]               = $category;
					$opts[]               = $tags;
					$styles[ $style ]     = $opts;
					$style_opts[ $style ] = $options['options'];
					$new_options          = $options['options'];

					foreach ( $new_options as $key => $values ) {
						$temp_panel      = array();
						$panel           = $values['panel'];
						$section         = ( isset( $values['section'] ) ) ? $values['section'] : '';
						$values['style'] = $style;

						$section_id   = cp_generate_sp_id( $section );
						$section_icon = ( isset( $values['section_icon'] ) ) ? $values['section_icon'] : false;

						if ( $ge_theme == $style ) {
							if ( ! isset( $new_panels[ $panel ] ) ) {
								$new_panels[ $panel ] = array();
							}
							array_push( $new_panels[ $panel ], $values );

							if ( ! isset( $theme_array[ $section ]['panels'][ $panel ] ) ) {
								$theme_array[ $section ]['panels'][ $panel ] = array();
							}
							array_push( $theme_array[ $section ]['panels'][ $panel ], $values );
							$theme_array[ $section ]['section_id'] = $section_id;
							if ( $section_icon ) {
								$theme_array[ $section ]['icon'] = $section_icon;
							}
						}
					}
					array_push( $panels, $new_panels );
				}
			}

			$category_data = array_values( array_unique( $category_data ) );

			foreach ( $category_data as $key => $category ) {
				$category = explode( ',', $category );
				if ( 1 < count( $category ) ) {
					foreach ( $category as $cat_name ) {
						array_push( $category_data, $cat_name );
					}
					unset( $category_data[ $key ] );
				}
			}

			$category_data = array_unique( $category_data );

			if ( 'variant' !== $style_view ) {
				echo '<ul class="filter-options">';
				foreach ( $category_data as $index => $cat ) {
					$icon = 'connects-icon-ribbon';

					switch ( $cat ) {
						case 'All':
							$icon = 'connects-icon-align-justify';
							break;
						case 'Offer':
							$icon = 'connects-icon-tag';
							break;
						case 'Optins':
							$icon = 'connects-icon-mail';
							break;
						case 'Exit Intent':
							$icon = 'connects-icon-outbox';
							break;
						case 'Updates':
							$icon = 'connects-icon-star';
							break;
						case 'Videos':
							$icon = 'connects-icon-video';
							break;

					}
					echo '<li class="smile-filter-li" data-group="' . esc_attr( $cat ) . '">
				<i class="' . esc_attr( $icon ) . '"></i>
				<a class="smile-filter-anchor">' . esc_html( ucfirst( $cat ) ) . '</a></li>';
				}
				echo '</ul>';
				echo '<ul class="cp-styles-list row" id="grid">';
			}

			$existing_presets = get_option( 'cp_' . $module . '_preset_templates' );
			$fun              = 'cp_add_' . $module . '_template';
			$preset_list      = $fun( array(), '', $module );

			$display_import_link = false;

			if ( is_array( $preset_list ) ) {
				foreach ( $preset_list as $key => $value ) {
					if ( ! isset( $existing_presets[ $key ] ) ) {
						$display_import_link = true;
					}
				}
			}

			if ( ! empty( $styles ) ) {
				$style_name          = '';
				$style_settings      = '';
				$old_style           = '';
				$data_action         = isset( $_GET['variant-test'] ) ? 'update_variant_test_settings' : 'update_style_settings';
				$data_option         = isset( $_GET['variant-test'] ) ? $module . '_variant_tests' : $option_name;
				$smile_variant_tests = get_option( $data_option );
				$variant_style       = isset( $_GET['variant-style'] ) ? sanitize_text_field( $_GET['variant-style'] ) : '';
				$variant_test        = isset( $_GET['variant-test'] ) ? sanitize_text_field( $_GET['variant-test'] ) : '';
				$style_id            = isset( $_GET['style_id'] ) ? sanitize_text_field( $_GET['style_id'] ) : '';
				$smile_variant_tests = isset( $smile_variant_tests[ $style_id ] ) ? $smile_variant_tests[ $style_id ] : '';
				$style_name          = isset( $_GET['style'] ) ? sanitize_text_field( $_GET['style'] ) : '';
				$page                = isset( $_GET['page'] ) ? sanitize_text_field( $_GET['page'] ) : '';

				$option = '';
				if ( 'smile-info_bar-designer' === $page ) {
					$option = 'smile_info_bar_styles';
				} elseif ( 'smile-modal-designer' === $page ) {
					$option = 'smile_modal_styles';
				} else {
					$option = 'smile_slide_in_styles';
				}

				if ( isset( $_GET['variant-style'] ) ) {
					if ( is_array( $smile_variant_tests ) && ! empty( $smile_variant_tests ) ) {
						if ( isset( $_GET['action'] ) && 'new' === $_GET['action'] ) {
							$prev_styles            = get_option( $option );
							$key                    = search_style( $prev_styles, $style_id );
							$style_settings         = $prev_styles[ $key ];
							$style_settings         = maybe_unserialize( $style_settings['style_settings'] );
							$style_settings['live'] = 0;
							$old_style              = $style_settings['style'];
						} else {
							foreach ( $smile_variant_tests as $key => $array ) {
								if ( $array['style_id'] == $variant_style ) {
									$style_settings = $array['style_settings'];
									$style_settings = maybe_unserialize( $style_settings );
									$old_style      = $style_settings['style'];
									break;
								}
							}
						}
					} elseif ( isset( $_GET['action'] ) && 'new' === $_GET['action'] ) {
						$prev_styles            = get_option( $option );
						$key                    = search_style( $prev_styles, $style_id );
						$style_settings         = $prev_styles[ $key ];
						$style_settings         = maybe_unserialize( $style_settings['style_settings'] );
						$style_settings['live'] = 0;
						$old_style              = $style_settings['style'];
					}
				} elseif ( isset( $_GET['style'] ) ) {
					$style_id    = sanitize_text_field( $_GET['style'] );
					$prev_styles = get_option( $data_option );
					$key         = search_style( $prev_styles, $style_id );
					$style_name  = '';
					if ( null !== $key ) {
						$style_settings = $prev_styles[ $key ];
						$style_name     = urldecode( $style_settings['style_name'] );
						$style_settings = maybe_unserialize( $style_settings['style_settings'] );
						$old_style      = $style_settings['style_id'];
					}
				}
				if ( isset( $_GET['theme'] ) ) {
					$theme                = sanitize_text_field( $_GET['theme'] );
					$edit_style[ $theme ] = $styles[ $theme ];
					$styles               = $edit_style;
				}

				if ( 'new' === $style_view ) { // if on template list screen.
					// append preset templates.
					if ( is_array( $preset_templates ) ) {
						$styles = array_merge( $styles, $preset_templates );
					}

					foreach ( $styles as $key => $value ) {
						if ( isset( $preset_list[ $key ] ) ) {
							unset( $preset_list[ $key ] );
						}
					}

					$styles = array_merge( $styles, $preset_list );
				} else {
					if ( ! isset( $_GET['variant-style'] ) ) {
						$prev_styles = get_option( $data_option );
						$key         = search_style( $prev_styles, esc_attr( $_GET['style'] ) );

						if ( null === $key ) {

							// if current style is preset.
							if ( isset( $_GET['preset'] ) ) {
								$preset = sanitize_text_field( $_GET['preset'] );

								$settings = get_option( 'cp_' . $module . '_' . $preset, '' );

								if ( '' === $settings ) {
									$demo_dir = CP_BASE_DIR . 'modules/' . $module . '/presets/' . $preset . '.txt';
									$settings = Convert_Plug_Filesystem::prefix_get_filesystem()->get_contents( $demo_dir );
									$settings = json_decode( $settings, true );
								}

								$style_settings = $settings['style_settings'];

								$import_style = array();
								foreach ( $style_settings as $title => $value ) {
									if ( ! is_array( $value ) ) {
										$value                  = htmlspecialchars_decode( $value );
										$import_style[ $title ] = $value;
									} else {
										foreach ( $value as $ex_title => $ex_val ) {
											$val[ $ex_title ] = htmlspecialchars_decode( $ex_val );
										}
										$import_style[ $title ] = $val;
									}
								}

								$style_settings = $import_style;
								$styles         = array();

								$temp_arr                    = $preset_templates[ $preset ];
								$modal_temp_array            = array();
								$modal_temp_array[ $preset ] = $temp_arr;
								$styles                      = array_merge( $styles, $modal_temp_array );

								$styles[ $theme ] = $styles[ $preset ];
								unset( $styles[ $preset ] );
							}
						}
					}
				}

				if ( cp_is_connected() ) {
					$cp_connected = true;
				} else {
					$cp_connected = false;
				}

				$cp_screenshots_images = get_option( 'cp_screenshots_images', array() );

				foreach ( $styles as $style => $options ) {
					foreach ( $cp_screenshots_images as $image => $slug ) {
						if ( isset( $options[7] ) && $options[7] === $slug['preset_slug'] ) { // Check the preset slug if already present.
							$options[3] = $image;
						}
					}
					$rand               = substr( md5( uniqid() ), wp_rand( 0, 26 ), 5 );
					$dynamic_style_name = 'cp_id_' . $rand;
					$new_style_id       = ( isset( $style_id ) && '' !== $style_id ) ? $style_id : $dynamic_style_name;
					if ( isset( $_GET['variant-test'] ) && 'new' === $_GET['variant-test'] ) {
						$new_style_id = $dynamic_style_name;
					}
					$active = ( $old_style == $options[0] ) ? 'active ' : '';

					$page = isset( $_GET['page'] ) ? sanitize_text_field( $_GET['page'] ) : '';

					$callback_url   = 'admin.php?page=' . $page;
					$hide_new_style = '';

					if ( isset( $style_view ) && 'variant' !== $style_view ) {
						$preset = ( isset( $options[7] ) ) ? '&preset=' . $options[7] : '';

						$url = add_query_arg(
							array(
								'page'       => $page,
								'style-view' => 'edit',
								'action'     => 'new',
								'style'      => $dynamic_style_name,
								'theme'      => $options[0] . $preset,
							),
							admin_url( 'admin.php' )
						);

						$callback_url = add_query_arg(
							array(
								'page' => $page,
							),
							admin_url( 'admin.php' )
						);
					} else {
						$sid = isset( $_GET['style_id'] ) ? sanitize_text_field( $_GET['style_id'] ) : sanitize_text_field( $_GET['variant-style'] );

						$pid = isset( $_GET['parent-style'] ) ? sanitize_text_field( $_GET['parent-style'] ) : sanitize_text_field( $_GET['style_id'] );

						$callback_url = add_query_arg(
							array(
								'page'          => $page,
								'style-view'    => 'variant',
								'variant-style' => $sid,
								'style'         => stripslashes( $pid ),
								'theme'         => $theme,
							),
							admin_url( 'admin.php' )
						);

						$url = add_query_arg(
							array(
								'page'          => $page,
								'style-view'    => 'variant',
								'variant-test'  => 'edit',
								'action'        => 'new',
								'variant-style' => $dynamic_style_name,
								'style'         => rawurlencode( stripslashes( $style_name ) ),
								'style_id'      => $variant_style,
								'theme'         => $options[0],
							),
							admin_url( 'admin.php' )
						);

						$hide_new_style = 'cp-hidden-variant-style';
					}

					if ( ! isset( $style_name ) ) {
						$sanitize_style_name = isset( $_GET['style-name'] ) ? stripslashes( ucwords( sanitize_text_field( $_GET['style-name'] ) ) ) : '';
						$style_name          = $sanitize_style_name;
					}

					if ( isset( $_GET['action'] ) && 'new' === $_GET['action'] && isset( $_GET['variant-style'] ) ) {
						$style_name = '';
					}

					$is_importable = false;

					// check if this style is importable.
					if ( isset( $options[7] ) ) {
						$preset_option_data = get_option( 'cp_' . $module . '_' . $options[7] );

						if ( is_array( $preset_option_data ) && ! empty( $preset_option_data ) ) {
							$is_importable = false;
						} else {
							$is_importable = true;
						}
					}

					$data_view = ( isset( $style_view ) && 'new' === $style_view ) || ( isset( $_GET['variant-test'] ) && 'new' === $_GET['variant-test'] ) ? 'data-view="new" ' : 'data-view="edit"';

					if ( 'variant' === $style_view ) {
						if ( isset( $_GET['variant-test'] ) && 'new' === $_GET['variant-test'] ) {
							$el_class = ' variant-test';
						} else {
							$el_class = '';
						}
						echo '<a id="' . esc_attr( $style ) . '" class="cp-style-split-link button button-primary customize' . esc_attr( $el_class ) . '" href="' . esc_attr( esc_url( $url ) ) . '" ' . wp_kses_post( $data_view ) . ' data-module="' . esc_attr( ucwords( str_replace( '_', ' ', $module ) ) ) . '" data-id="' . esc_attr( $style ) . '" data-style="panel-' . esc_attr( $options[0] ) . '">' . esc_html__( 'Start Customizing', 'smile' ) . '</a>';
					} else {
						if ( isset( $_GET['style-view'] ) && 'edit' !== $style_view ) {
							$options[5] = explode( ',', $options[5] );
							$result     = array();
							foreach ( $options[5]  as $a1 ) {
								$result[] = '"' . $a1 . '"';
							}
							$options[5] = implode( ',', $result );

							echo "<li class='col-xs-6 col-sm-4 col-md-4 cp-style-item " . esc_attr( $active ) . 'cp-style-' . esc_attr( $options[0] ) . "' data-groups='[" . esc_attr( $options[5] ) . "]' data-tags=['" . esc_attr( $options[6] ) . "']>";
							echo '<a id="' . esc_attr( $options[0] ) . '" class="cp-style-item-link customize" data-module="' . esc_attr( ucwords( str_replace( '_', ' ', $module ) ) ) . '" href="' . esc_attr( esc_url( $url ) ) . '" ' . wp_kses_post( $data_view ) . ' data-id="' . esc_attr( $options[0] ) . '" data-style="panel-' . esc_attr( $options[0] ) . '"></a>';
							echo '<div class="cp-style-item-box">';
							echo '<div class="cp-style-screenshot">';

							$display_action_links = true;

							if ( $is_importable ) {
								if ( $cp_connected ) {
									echo '<img src="' . esc_attr( esc_url( $options[3] ) ) . '"/>';
								} else {
									$display_action_links = false;
									echo '<img src="' . esc_attr( esc_url( CP_BASE_URL ) ) . 'admin/assets/img/internet-issue.png" />';
								}
							} else {
								echo '<img src="' . esc_attr( esc_url( $options[3] ) ) . '"/>';
							}

							echo '</div>';
							echo '<h3 class="cp-style-name">' . esc_html( $options[1] ) . '</h3>';

							if ( $display_action_links ) {
								echo '<div class="cp-style-actions">';

								if ( ! $is_importable ) {
									echo '<a id="' . esc_attr( $options[0] ) . '" class="cp-style-item-link customize" data-module="' . esc_attr( ucwords( str_replace( '_', ' ', $module ) ) ) . '" href="' . esc_attr( esc_url( $url ) ) . '" ' . wp_kses_post( $data_view ) . ' data-id="' . esc_attr( $options[0] ) . '" data-style="panel-' . esc_attr( $options[0] ) . '">
								<span class="cp-action-link customize"><span class="cp-action-link-icon connects-icon-cog"></span>' . esc_html__( 'Use This', 'smile' ) . '</span>';
									echo '</a>';
								} else {
									echo '<a id="' . esc_attr( $options[0] ) . '" href="javascript:void(0);" class="cp-style-import-link" data-module="' . esc_attr( $module ) . '" data-href="' . esc_attr( esc_url( $url ) ) . '" ' . wp_kses_post( $data_view ) . ' data-preset="' . esc_attr( $options[7] ) . '" data-id="' . esc_attr( $options[0] ) . '" data-style="panel-' . esc_attr( $options[0] ) . '">
								<span class="cp-action-link"><span class="cp-action-link-icon"><i class="connects-icon-inbox"></i></span><span class="cp-action-text">' . esc_html__( 'Import This', 'smile' ) . '</span></span>';
									echo '</a>';
								}
							}

							if ( isset( $options[7] ) ) {
								$style_settings_method = 'external';
								$template_name         = $options[7];
							} else {
								$style_settings_method = 'internal';
								$template_name         = '';
							}
							$data_url = CP_BASE_URL . 'modules/' . $module . '/assets/demos/' . $options[0] . '/' . $options[0] . '.min.css';
							if ( $display_action_links ) {
								echo '<span class="cp-action-link style-demo"
								data-style="' . esc_attr( $options[0] ) . '"
								data-title="' . esc_attr( $options[1] ) . '"
								data-url="' . esc_url( $data_url ) . '"
								data-style-settings-method="' . esc_attr( $style_settings_method ) . '"
								data-temp-name="' . esc_attr( $template_name ) . '"
								data-module="' . esc_attr( $module ) . '"><span class="cp-action-link-icon connects-icon-link"></span>' . esc_html__( 'Live Preview', 'smile' ) . '</span></div>';

								echo '</div>'; // cp-style-item-box.
							}
						} else {
							echo '<a id="' . esc_attr( $style ) . '" class="cp-style-item-link customize" data-module="' . esc_attr( ucwords( str_replace( '_', ' ', $module ) ) ) . '" href="' . esc_attr( esc_url( $url ) ) . '" ' . wp_kses_post( $data_view ) . ' data-id="' . esc_attr( $options[0] ) . '" data-style="panel-' . esc_attr( $options[0] ) . '">' . esc_html__( 'Customize', 'smile' ) . '</a>';
						}
					}
					$start_date = isset( $style_settings['schedule']['start'] ) ? $style_settings['schedule']['start'] : '';
					$end_date   = isset( $style_settings['schedule']['end'] ) ? $style_settings['schedule']['end'] : '';

					if ( isset( $style_view ) && ( 'edit' === $style_view || 'variant' === $style_view && 'edit' === $_GET['variant-test'] ) ) {
						?>
					<div class="customizer-wrapper smile-customizer-wrapper panel-<?php echo esc_attr( $style ); ?>" style="display: none;">
						<div id="cp-designer-form" class="design-form ecedcfsfdc">
							<form class="cp-cust-form" id="form-<?php echo esc_attr( $options[0] ); ?>" data-action="<?php echo esc_attr( $data_action ); ?>" data-start="<?php echo esc_attr( $start_date ); ?>" data-end="<?php echo esc_attr( $end_date ); ?>">
								<?php if ( isset( $_GET['preset'] ) && null === $key ) { ?>
								<input type='hidden' name='style_preset' value="<?php echo esc_attr( sanitize_text_field( $_GET['preset'] ) ); ?>">
								<?php } ?>
								<input type="hidden" name="style" value="<?php echo esc_attr( $options[0] ); ?>" />
								<input type="hidden" name="style_id" value="<?php echo esc_attr( $new_style_id ); ?>" />
								<input type="hidden" name="style_type" value="<?php echo esc_attr( $module ); ?>">
								<input type="hidden" name="option" value="<?php echo esc_attr( $data_option ); ?>" />
								<input type="hidden" name="start_date" value="<?php echo esc_attr( $start_date ); ?>" />
								<input type="hidden" name="end_date" value="<?php echo esc_attr( $end_date ); ?>" />
								<?php if ( isset( $_GET['variant-style'] ) ) { ?>
									<?php if ( isset( $_GET['action'] ) ) { ?>
								<input type="hidden" name="variant-action" value="<?php echo esc_attr( sanitize_text_field( $_GET['action'] ) ); ?>" />
								<?php } ?>
								<input type="hidden" name="variant-style" value="<?php echo esc_attr( sanitize_text_field( $_GET['variant-style'] ) ); ?>" />
								<input type="hidden" name="variant_style_id" value="<?php echo esc_attr( sanitize_text_field( $_GET['variant-style'] ) ); ?>" />
								<?php } ?>

								<?php
								$timezone_settings            = get_option( 'convert_plug_settings' );
								$timezone_name                = $timezone_settings['cp-timezone'];
								$delete_style_nonce_for_admin = wp_create_nonce( 'cp-delete-style' );
								?>
								<input type="hidden" name="cp_gmt_offset" class ="cp_gmt_offset" value="<?php echo esc_attr( get_option( 'gmt_offset' ) ); ?>" />
								<input type="hidden" name="cp_counter_timezone" class ="cp_counter_timezone" value="<?php echo esc_attr( $timezone_name ); ?>" />
								<input type="hidden" id="cp-delete-style-nonce" value="<?php echo esc_attr( $delete_style_nonce_for_admin ); ?>" />                      		<div class="customizer metro" id="accordion-panel-<?php echo esc_attr( $options[0] ); ?>">
									<div class="cp-new-cust-section">
										<div class="cp-vertical-nav">
											<div class="cp-vertical-nav-top cp-customize-section">
												<?php
												foreach ( $theme_array as $key => $sections ) {
													$section_id   = ( isset( $sections['section_id'] ) ) ? $sections['section_id'] : '';
													$section_icon = ( isset( $sections['icon'] ) ) ? $sections['icon'] : '';
													?>
													<a href="#<?php echo esc_attr( $section_id ); ?>" class="cp-section" data-section-id="<?php echo esc_attr( $section_id ); ?>">
													<span class="cp-tooltip-icon has-tip" data-position="right" title="<?php echo esc_attr( $key ); ?>">
													<i class="<?php echo esc_attr( $section_icon ); ?>"></i>
														</span>
													</a>
													<?php
												}
												?>
											</div>
											<div class="cp-vertical-nav-center cp-customize-section">
												<?php
												$dashboard_link = '';
												if ( isset( $_GET['page'] ) ) {
													$dashboard_link = add_query_arg(
														array(
															'page'  => sanitize_text_field( $_GET['page'] ),
														),
														admin_url( 'admin.php' )
													);
												}
												?>
												<a data-redirect="<?php echo esc_attr( esc_url( $dashboard_link ) ); ?>" href="javascript:void(0)" target="_blank" class="cp-section cp-dashboard-link">
													<span class="cp-tooltip-icon has-tip" data-position="right" title="Dashboard">
														<i class="connects-icon-esc"></i>
													</span>
												</a>
												<a data-redirect="<?php echo esc_attr( esc_url( site_url() ) ); ?>" href="javascript:void(0)" target="_blank" class="cp-section cp-website-link" >
													<span class="cp-tooltip-icon has-tip" data-position="right" title="See Website">
														<i class="connects-icon-globe"></i>
													</span>
												</a>
											</div>

											<div class="cp-vertical-nav-bottom <?php echo esc_attr( $hide_new_style ); ?>">

												<a href="#" class="customize-footer-actions customize-collpase-act" >
													<span class="cp-tooltip-icon has-tip customizer-collapse" title="Collapse">
														<i class="connects-icon-arrow-left"></i>
														<i class="connects-icon-arrow-right"></i>
													</span>
												</a>
												<a href="#responsive-sect" data-section-id="responsive-sect" class="cp-section cp-customize-section" >
													<span class="cp-tooltip-icon has-tip" data-position="top" title="Responsive">
														<i class="connects-icon-responsive2"></i>
													</span>
												</a>
												<a href="#cp-themes" class="cp-section cp-themes" data-section-id="cp-themes">
													<span class="cp-tooltip-icon has-tip" data-position="top" title="
													<?php
													/* translators:%s module name*/
													echo sprintf( __( 'Create New %s ', 'smile' ), ucwords( str_replace( '_', ' ', $module ) ) ); //PHPCS:ignore:WordPress.Security.EscapeOutput.OutputNotEscaped
													?>
													">
													<i class="connects-icon-plus"></i>
												</span>
											</a>
											<a href="#" class="cp-save" id="button-save-panel-<?php echo esc_attr( $style ); ?>" data-style="<?php echo esc_attr( $style ); ?>">

												<span class="cp-tooltip-icon has-tip" data-position="top" title="Save">
													<i class="connects-icon-inbox"></i>
												</span>
											</a>

											<a data-redirect="<?php echo esc_attr( esc_url( $callback_url ) ); ?>" href="javascript:void(0)" class="close-button">
												<span class="cp-tooltip-icon has-tip" data-position="top" title="Close">
													<i class="connects-icon-cross"></i>
												</span>
											</a>

										</div><!-- .cp-vertical-nav-bottom -->

									</div><!-- .cp-vertical-nav -->
									<div class="cp-customizer-tabs-wrapper" style="height:100%;">
										<div class="preview-notice">
											<span class="theme-name site-title"><?php echo esc_html( $options[1] ); ?></span>
										</div>
										<?php
										$count = 0;
										foreach ( $theme_array as $key => $sections ) {
											$panels     = $sections['panels'];
											$section_id = $sections['section_id'];
											?>
											<div id="<?php echo esc_attr( $section_id ); ?>" class="cp-customizer-tab accordion with-marker cp-tab-<?php echo esc_attr( $count ); ?>" data-role="accordion" data-closeany="true">
																<?php
																$cnt = 0;
																foreach ( $panels as $panel_key => $panel ) {
																	?>
													<div class="accordion-frame">
														<a href="#" class="heading 
																	<?php
																	if ( 'Name' !== $panel_key ) {
																		echo 'collapsed';
																	}
																	?>
															" ><?php echo esc_attr( $panel_key ); ?></a>
															<div class="content" 
																	<?php
																	if ( 'Name' === $panel_key ) {
																		echo 'style="display:block;"';
																	}
																	?>
																>
																	<?php

																	if ( 'Name' === $panel_key && 0 === $cnt ) {
																		?>
																	<div class="smile-element-container">
																		<strong>
																			<label for="cp_style_title"><?php esc_html_e( 'Name This Design', 'smile' ); ?></label>
																		</strong>
																		<span class="cp-tooltip-icon has-tip" data-position="right" style="cursor: help;float: right;" title="<?php esc_attr_e( 'A unique & descriptive name will help you in future as it would appear in the dashboard, analytics, etc.', 'smile' ); ?>">
																			<i class="dashicons dashicons-editor-help"></i>
																		</span>
																		<p>
																			<input type="text" id="cp_style_title"  class="form-control smile-input smile-textfield style_title textfield " name="new_style" data-style="<?php echo esc_attr( stripslashes( $style_name ) ); ?>" value="<?php echo esc_attr( stripslashes( $style_name ) ); ?>">
																		</p>
																	</div>

																		<?php
																	}
																	?>

																	<?php
																	$html = '';
																	foreach ( $panel as $key => $values ) {
																		$name = $values['name'];
																		$type = $values['type'];

																		$default_value = isset( $values['opts']['value'] ) ? urldecode( $values['opts']['value'] ) : '';
																		$input_value   = isset( $style_settings[ $name ] ) ? urldecode( $style_settings[ $name ] ) : $values['opts']['value'];

																		if ( function_exists( 'do_input_type_settings_field' ) ) {
																				$values['opts']['type'] = $type;
																				$dependency             = isset( $values['dependency'] ) ? $values['dependency'] : '';
																				$dependency             = smile_framework_create_dependency( $name, $dependency );
																				$html                  .= '<div class="smile-element-container" ' . $dependency . '>';
																			if ( 'section' !== $type && 'google_fonts' !== $type ) {
																				$html .= '<strong><label for="smile_' . $name . '">' . ucwords( $values['opts']['title'] ) . '</label></strong>';
																				if ( isset( $values['opts']['description'] ) ) {
																						$html .= '<span class="cp-tooltip-icon has-tip" data-position="right" title="' . $values['opts']['description'] . '" style="cursor: help;float: right;"><i class="dashicons dashicons-editor-help"></i></span>';
																				}
																			}

																			$input_value   = stripslashes( $input_value );
																			$default_value = stripslashes( $default_value );

																			$html .= do_input_type_settings_field( $name, $type, $values['opts'], $input_value, $default_value );
																			$html .= '</div>';
																		}
																	}
																	echo $html; //PHPCS:ignore:WordPress.Security.EscapeOutput.OutputNotEscaped
																	?>
																</div><!-- .content -->
															</div><!-- .accordion-frame -->
																	<?php
																				$count++;
																}
																?>
													</div><!-- .cp-customizer-tab -->
													<?php
										}
										?>
												<div id="responsive-sect" class="cp-customizer-tab" data-role="accordion" data-closeany="true">
													<div class="accordion-frame">
														<div class="content">
															<div class="cp-responsive-bar">
																<!-- iphone -->
																<div class="cp-resp-bar-icon cp-iphone" data-res-class="cp-iphone-device"><i class="connects-icon-iPhone"></i></div>
																<!-- iphone-horizontal -->
																<div class="cp-resp-bar-icon cp-iphone-h"  data-res-class="cp-iphone-device-hr"><i class="connects-icon-iPhone"></i></div>
																<!-- ipad -->
																<div class="cp-resp-bar-icon cp-ipad" data-res-class="cp-ipad-device"><i class="connects-icon-iPad"></i></div>
																<!-- ipad-horizontal -->
																<div class="cp-resp-bar-icon cp-ipad-h" data-res-class="cp-ipad-device-hr"><i class="connects-icon-iPad"></i></div>
																<!-- laptop -->
																<div class="cp-resp-bar-icon cp-mac cp-resp-active" data-res-class="cp-monitor-device"><i class="connects-icon-tv"></i></div>
															</div>
															<div class="cp-responsive-notice">
																<div class="smile-element-container">
																	<div class="link-title" style="display: block;padding: 50px 20px;">
																		<?php echo esc_html__( 'Responsive preview here is roughly displayed and might not be 100% correct. For accurate preview, please check output on the actual device.', 'smile' ); ?>
																	</div>
																</div>
															</div>
														</div><!-- .content -->
													</div><!-- .accordion-frame -->
												</div><!-- .cp-customizer-tab -->

												<div id="cp-themes" class="cp-customizer-tab" data-rome="accordion" data-closeany="true">
													<div class="accordion-frame">
														<div class="content cp-themes-area">
															<div class="row smile-style-search">
																<div class="container">
																	<div class="col-sm-12">
																		<input type="search" class="js-shuffle-search" id="style-search" name="style-search" placeholder="<?php esc_attr_e( 'Search Template', 'smile' ); ?>">
																	</div>
																</div>
															</div>
															<div class="cp-styles-list row" id="cp_grid" style="margin:0px;">
																<?php

																foreach ( $all_styles as $style_title => $style_options ) {
																	$display = true;

																	// check if this style is imported.
																	if ( isset( $style_options[7] ) ) {
																		$style_option_data = get_option( 'cp_' . $module . '_' . $style_options[7] );

																		if ( ! $style_option_data || empty( $style_option_data ) ) {
																			$display = false;
																		}
																	}

																	if ( ! $display ) {
																		continue;
																	}

																	$rand               = substr( md5( uniqid() ), wp_rand( 0, 26 ), 5 );
																	$dynamic_style_name = 'cp_id_' . $rand;
																	$new_style_id       = ( isset( $style_id ) && '' !== $style_id ) ? $style_id : $dynamic_style_name;
																	if ( isset( $_GET['variant-test'] ) && 'new' === $_GET['variant-test'] ) {
																		$new_style_id = $dynamic_style_name;
																	}
																	$active = ( $old_style == $style_title ) ? 'active ' : '';

																	if ( isset( $style_options[5] ) ) {
																		$tags = $style_options[5];
																	} else {
																		$tags = 'promotions';
																	}

																	$page = isset( $_GET['page'] ) ? sanitize_text_field( $_GET['page'] ) : '';

																	$callback_url = 'admin.php?page=' . $page;

																	if ( isset( $style_view ) && 'variant' !== $style_view ) {
																		$preset = ( isset( $style_options[7] ) ) ? '&preset=' . $style_options[7] : '';
																		$url    = 'admin.php?page=' . $page . '&style-view=edit&action=new&style=' . $dynamic_style_name . '&theme=' . $style_options[0] . $preset;

																		$callback_url = 'admin.php?page=' . $page;
																	} else {
																		$sid          = isset( $_GET['style_id'] ) ? $_GET['style_id'] : esc_attr( $_GET['variant-style'] );
																		$pid          = isset( $_GET['parent-style'] ) ? $_GET['parent-style'] : esc_attr( $_GET['style_id'] );
																		$callback_url = 'admin.php?page=' . $page . '&style-view=variant&variant-style=' . $sid . '&style=' . $pid . '&theme=' . $theme;

																		$url = 'admin.php?page=' . $page . '&style-view=variant&variant-test=edit&action=new&variant-style=' . $dynamic_style_name . '&style=' . $style_name . '&style_id=' . $variant_style . '&theme=' . $style_options[0];
																	}

																	echo '<div class="cp-style-item ' . esc_attr( $active ) . 'cp-style-' . esc_attr( $style_title ) . '" data-tags=["' . esc_attr( $tags ) . '"] style="margin: 15px;">';
																	echo '<div class="cp-style-item-box">';

																	echo '<a id="' . esc_attr( $style_title ) . '" class="cp-new-style-link" href="' . esc_attr( esc_url( $url ) ) . '" ' . wp_kses_post( $data_view ) . ' data-id="' . esc_attr( $style_title ) . '" data-style-title="' . esc_attr( $style_options[0] ) . '" data-style="' . esc_attr( $style_id ) . '" data-option="smile_' . esc_attr( $module ) . '_styles">';
																	echo '<div class="cp-style-screenshot">';
																	echo '<img src="' . esc_attr( esc_url( $style_options[3] ) ) . '"/>';
																	echo '</div>';
																	echo '<h3 class="cp-style-name">' . esc_html( $style_options[1] ) . '</h3>';
																	echo '</a>';
																	echo '</div>'; // .cp-style-item-box.
																		echo '</div>'; // .cp-style-item .
																}
																?>
																</div>
																<div class="col-xs-6 col-sm-4 col-md-4 shuffle_sizer"></div>
								</div>
							</div>
						</div>
					</div><!-- .cp-customizer-tabs-wrapper -->
				</div><!-- .cp-new-cust-section -->
			</div><!-- .customizer -->
		</form><!-- .cp-cust-form -->
	</div> <!-- .design-form -->
						<?php
						$sanitize_theme = isset( $_GET['theme'] ) ? sanitize_text_field( $_GET['theme'] ) : '';

						$iframe_url = add_query_arg(
							array(
								'page'        => 'cp_customizer',
								'module'      => $module,
								'class'       => $class,
								'theme'       => $sanitize_theme,
								'hidemenubar' => 'true',
							),
							admin_url( 'admin.php' )
						);

						?>
					<div class="design-content" data-demo-id="<?php echo esc_attr( $sanitize_theme ); ?>" data-class="<?php echo esc_attr( $class ); ?>" data-module="<?php echo esc_attr( $module ); ?>" data-js-url="<?php echo esc_attr( esc_url( $options[4] ) ); ?>" data-iframe-url="<?php echo esc_attr( esc_url( $iframe_url ) ); ?>">
						<div class="live-design-area">
							<div class="design-area-loading">
								<!-- <span class="spinner"></span> -->
								<div class="smile-absolute-loader" style="visibility: visible;">
									<div class="smile-loader">
										<div class="smile-loading-bar"></div>
										<div class="smile-loading-bar"></div>
										<div class="smile-loading-bar"></div>
										<div class="smile-loading-bar"></div>
									</div>
								</div>
							</div>
						</div>
					</div><!-- .design-content -->
				</div><!-- .customizer-wrapper -->
						<?php
						echo '</li>'; /*--- .customizer-wrapper ---*/
					}
				}

				if ( 'variant' !== $style_view ) {
					echo '</ul>';
				}
				?>

				<?php
			}
		}
	}
}
if ( ! function_exists( 'smile_search_array' ) ) {
	/**
	 * Function Name: smile_search_array.
	 *
	 * @param  array  $arrays  array parameter.
	 * @param  string $field  string parameter.
	 * @param  string $value  string parameter.
	 * @return boolval(var)   true/false parameter.
	 */
	function smile_search_array( $arrays, $field, $value ) {
		$keys = array();
		foreach ( $arrays as $key => $array ) {
			foreach ( $array as $k => $arr ) {
				if ( $arr[ $field ] === $value ) {
					array_push( $keys, $key );
				}
			}
		}
		if ( ! empty( $keys ) ) {
			return $keys;
		} else {
			return false;
		}
	}
}

if ( ! function_exists( 'smile_manage_toolbar' ) ) {

	/**
	 * Function Name: smile_manage_toolbar.
	 */
	function smile_manage_toolbar() {
		if ( isset( $_REQUEST['cp_admin_page_nonce'] ) && wp_verify_nonce( $_REQUEST['cp_admin_page_nonce'], 'cp_admin_page' ) ) {
			$user_ID = get_current_user_id();
			$display = _get_admin_bar_pref( 'front', $user_ID );
			if ( isset( $_GET['hidemenubar'] ) ) {
				$display = false;
			}
			return $display;
		}
	}
}

if ( is_user_logged_in() ) {
	if ( isset( $_REQUEST['cp_admin_page_nonce'] ) && wp_verify_nonce( $_REQUEST['cp_admin_page_nonce'], 'cp_admin_page' ) ) {
		$data           = get_option( 'convert_plug_debug' );
		$hide_admin_bar = isset( $data['cp-hide-bar'] ) ? $data['cp-hide-bar'] : 'css';
		if ( 'WordPress' === $hide_admin_bar ) {
			add_filter( 'show_admin_bar', 'smile_manage_toolbar' );
		} elseif ( isset( $_GET['hidemenubar'] ) ) {
			add_filter( 'body_class', 'cp_body_class_names' );
			add_action( 'wp_head', 'cp_admin_bar_css' );
		}
	}
}

if ( ! function_exists( 'cp_admin_bar_css' ) ) {
	/**
	 * Function Name: cp_admin_bar_css.
	 */
	function cp_admin_bar_css() {
		echo '<style id="cp-admin-bar">.cp-hide-admin-bar #wpadminbar{ display: none !important; }</style>';
	}
}

if ( ! function_exists( 'cp_body_class_names' ) ) {
	/**
	 * Function Name: cp_body_class_names.
	 *
	 * @param  array $classes  array parameter.
	 */
	function cp_body_class_names( $classes ) {
		$classes[] = 'cp-hide-admin-bar';
		return $classes;
	}
}
if ( ! function_exists( 'cp_generate_sp_id' ) ) {
	/**
	 * Function Name: cp_generate_sp_id.
	 *
	 * @param  string $key  array parameter.
	 */
	function cp_generate_sp_id( $key ) {
		$key = strtolower( $key );
		$key = preg_replace( '![^a-z0-9]+!i', '-', $key );
		return $key;
	}
}

if ( ! function_exists( 'live_preview_style_css' ) ) {
	/**
	 * Function Name: live_preview_style_css.
	 *
	 * @param  array $hook  array parameter.
	 */
	function live_preview_style_css( $hook ) {
		$screen = get_current_screen();
		$page   = $screen->base;

		if ( fasle !== strpos( $page, CP_PLUS_SLUG ) ) {
			echo '<link rel="stylesheet" type="text/css" id="style_preview_css" href="#" />'; //PHPCS:ignore:WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet
		}
	}
}

if ( ! function_exists( 'generate_partial_atts' ) ) {
	/**
	 * Function Name: generate_partial_atts.
	 *
	 * @param  array $s  array parameter.
	 */
	function generate_partial_atts( $s ) {
		$partials  = isset( $s['css_property'] ) ? ' data-css-property="' . $s['css_property'] . '" ' : '';
		$partials .= isset( $s['css_selector'] ) ? ' data-css-selector="' . $s['css_selector'] . '" ' : '';
		$partials .= isset( $s['css_preview'] ) ? ' data-css-preview="' . $s['css_preview'] . '" ' : ' data-css-preview="false" ';
		$partials .= isset( $s['unit'] ) ? ' data-unit="' . $s['unit'] . '" ' : ' data-unit="px" ';
		$partials .= isset( $s['css-image-url'] ) ? ' data-css-image-url="' . $s['css-image-url'] . '" ' : ' data-css-image-url="" ';
		return $partials;
	}
}
