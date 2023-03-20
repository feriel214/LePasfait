<?php
/**
 * Widget: Audio player for Local hosted audio and Soundcloud and other embeded audio
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.2
 */

/** Load widget */
if ( ! function_exists( 'trx_addons_widget_audio_load' ) ) {
	add_action( 'widgets_init', 'trx_addons_widget_audio_load' );
	function trx_addons_widget_audio_load() {
		register_widget( 'trx_addons_widget_audio' );
	}
}

/** Widget Class */
class trx_addons_widget_audio extends TRX_Addons_Widget {
	/** Widget base constructor. */
	function __construct() {
		$widget_ops = array(
			'classname'   => 'widget_audio',
			'description' => esc_html__( 'Play audio from Soundcloud and other audio hostings or Local hosted audio', 'trx_addons' ),
		);
		parent::__construct( 'trx_addons_widget_audio', esc_html__( 'ThemeREX Audio player', 'trx_addons' ), $widget_ops );
	}

	/** Show widget */
	function widget( $args, $instance ) {

		$title        = apply_filters( 'widget_title', isset( $instance['title'] ) ? $instance['title'] : '' );
		$subtitle     = isset( $instance['subtitle'] ) ? $instance['subtitle'] : '';
		$next_btn     = isset( $instance['next_btn'] ) ? $instance['next_btn'] : '1';
		$prev_btn     = isset( $instance['prev_btn'] ) ? $instance['prev_btn'] : '1';
		$next_text    = isset( $instance['next_text'] ) ? $instance['next_text'] : '';
		$prev_text    = isset( $instance['prev_text'] ) ? $instance['prev_text'] : '';
		$now_text     = isset( $instance['now_text'] ) ? $instance['now_text'] : '';
		$track_time   = isset( $instance['track_time'] ) ? $instance['track_time'] : '';
		$track_scroll = isset( $instance['track_scroll'] ) ? $instance['track_scroll'] : '';
		$track_volume = isset( $instance['track_volume'] ) ? $instance['track_volume'] : '';
		$media        = isset( $instance['media'] ) ? $instance['media'] : array();
		trx_addons_get_template_part(
			TRX_ADDONS_PLUGIN_WIDGETS . 'audio/tpl.default.php',
			'trx_addons_args_widget_audio',
			apply_filters(
				'trx_addons_filter_widget_args',
				array_merge( $args, compact( 'title', 'subtitle', 'next_btn', 'next_text', 'prev_btn', 'prev_text', 'now_text', 'track_time', 'track_scroll', 'track_volume', 'media' ) ),
				$instance, 'trx_addons_widget_audio'
			)
		);
	}

	/** Update the widget settings. */
	function update( $new_instance, $instance ) {
		$instance                 = array_merge( $instance, $new_instance );
		$instance['title']        = strip_tags( $new_instance['title'] );
		$instance['subtitle']     = strip_tags( $new_instance['subtitle'] );
		$instance['next_btn']     = strip_tags( $new_instance['next_btn'] );
		$instance['prev_btn']     = strip_tags( $new_instance['prev_btn'] );
		$instance['next_text']    = strip_tags( $new_instance['next_text'] );
		$instance['prev_text']    = strip_tags( $new_instance['prev_text'] );
		$instance['now_text']     = strip_tags( $new_instance['now_text'] );
		$instance['track_time']   = strip_tags( $new_instance['track_time'] );
		$instance['track_scroll'] = strip_tags( $new_instance['track_scroll'] );
		$instance['track_volume'] = strip_tags( $new_instance['track_volume'] );
		$instance['media']        = array();
		if ( is_array( $new_instance['media'] ) ) {
			for ( $i = 0; $i < count( $new_instance['media'] ); $i++ ) {
				if ( empty( $new_instance['media'][ $i ]['url'] ) && empty( $new_instance['media'][ $i ]['embed'] ) ) {
					continue;
				}
				if ( empty( $new_instance['media'][ $i ]['new_window'] ) ) {
					$new_instance['media'][ $i ]['new_window'] = 0;
				}
				$instance['media'][] = $new_instance['media'][ $i ];
			}
		}
		return apply_filters( 'trx_addons_filter_widget_args_update', $instance, $new_instance, 'trx_addons_widget_audio' );
	}

	/** Displays the widget settings controls on the widget panel. */
	function form( $instance ) {
		/* Remove empty media array */
		if ( isset( $instance['media'] ) && ( ! is_array( $instance['media'] ) || count( $instance['media'] ) == 0 ) ) {
			unset( $instance['media'] );
		}
		/* Set up some default widget settings */
		$instance = wp_parse_args(
			(array) $instance, apply_filters(
				'trx_addons_filter_widget_args_default', array(
					'title'        => '',
					'subtitle'     => '',
					'next_btn'     => '1',
					'prev_btn'     => '1',
					'next_text'    => '',
					'prev_text'    => '',
					'now_text'     => '',
					'track_time'   => '1',
					'track_scroll' => '1',
					'track_volume' => '1',
					'media'        => array(
						array(
							'url'         => '',
							'embed'       => '',
							'caption'     => '',
							'author'      => '',
							'description' => '',
							'cover'       => '',
						),
						array(
							'url'         => '',
							'embed'       => '',
							'caption'     => '',
							'author'      => '',
							'description' => '',
							'cover'       => '',
						),
					),
				), 'trx_addons_widget_audio'
			)
		);

		do_action( 'trx_addons_action_before_widget_fields', $instance, 'trx_addons_widget_audio' );

		$this->show_field(
			array(
				'name'  => 'title',
				'title' => __( 'Title:', 'trx_addons' ),
				'value' => $instance['title'],
				'type'  => 'text',
			)
		);

		do_action( 'trx_addons_action_after_widget_title', $instance, 'trx_addons_widget_audio' );

		$this->show_field(
			array(
				'name'  => 'subtitle',
				'title' => __( 'Subtitle:', 'trx_addons' ),
				'value' => $instance['subtitle'],
				'type'  => 'text',
			)
		);

		$this->show_field(
			array(
				'name'  => 'next_btn',
				'title' => __( 'Show next button:', 'trx_addons' ),
				'value' => $instance['next_btn'],
				'type'  => 'checkbox',
			)
		);

		$this->show_field(
			array(
				'name'  => 'prev_btn',
				'title' => __( 'Show prev button:', 'trx_addons' ),
				'value' => $instance['prev_btn'],
				'type'  => 'checkbox',
			)
		);

		$this->show_field(
			array(
				'name'  => 'next_text',
				'title' => __( 'Next button text:', 'trx_addons' ),
				'value' => $instance['next_text'],
				'type'  => 'text',
			)
		);

		$this->show_field(
			array(
				'name'  => 'prev_text',
				'title' => __( 'Prev button text:', 'trx_addons' ),
				'value' => $instance['prev_text'],
				'type'  => 'text',
			)
		);

		$this->show_field(
			array(
				'name'  => 'now_text',
				'title' => __( '"Now playing" text:', 'trx_addons' ),
				'value' => $instance['now_text'],
				'type'  => 'text',
			)
		);

		$this->show_field(
			array(
				'name'  => 'track_time',
				'title' => __( 'Show tack time:', 'trx_addons' ),
				'value' => $instance['track_time'],
				'type'  => 'checkbox',
			)
		);

		$this->show_field(
			array(
				'name'  => 'track_scroll',
				'title' => __( 'Show track scroll bar:', 'trx_addons' ),
				'value' => $instance['track_scroll'],
				'type'  => 'checkbox',
			)
		);

		$this->show_field(
			array(
				'name'  => 'track_volume',
				'title' => __( 'Show track volume bar:', 'trx_addons' ),
				'value' => $instance['track_volume'],
				'type'  => 'checkbox',
			)
		);

		foreach ( $instance['media'] as $k => $item ) {
			$this->show_field(
				array(
					'name'  => sprintf( 'item%d', $k + 1 ),
					'title' => sprintf( __( 'Media item %d', 'trx_addons' ), $k + 1 ),
					'type'  => 'info',
				)
			);
			$this->show_field(
				array(
					'name'  => "media[{$k}][url]",
					'title' => __( 'Media URL:', 'trx_addons' ),
					'value' => $item['url'],
					'type'  => 'text',
				)
			);
			$this->show_field(
				array(
					'name'  => "media[{$k}][embed]",
					'title' => __( 'Embed code:', 'trx_addons' ),
					'value' => $item['embed'],
					'type'  => 'textarea',
				)
			);
			$this->show_field(
				array(
					'name'  => "media[{$k}][caption]",
					'title' => __( 'Audio caption:', 'trx_addons' ),
					'value' => $item['caption'],
					'type'  => 'text',
				)
			);
			$this->show_field(
				array(
					'name'  => "media[{$k}][author]",
					'title' => __( 'Author name:', 'trx_addons' ),
					'value' => $item['author'],
					'type'  => 'text',
				)
			);
			$this->show_field(
				array(
					'name'  => "media[{$k}][description]",
					'title' => __( 'Description:', 'trx_addons' ),
					'value' => $item['description'],
					'type'  => 'textarea',
				)
			);

			$this->show_field(
				array(
					'name'  => "media[{$k}][cover]",
					'title' => __( 'Cover image', 'trx_addons' ),
					'value' => $item['cover'],
					'type'  => 'image',
				)
			);
		}
		do_action( 'trx_addons_action_after_widget_fields', $instance, 'trx_addons_widget_audio' );
	}
}


/** Load required styles and scripts for the frontend */
if ( ! function_exists( 'trx_addons_widget_audio_load_scripts_front' ) ) {
	add_action( 'wp_enqueue_scripts', 'trx_addons_widget_audio_load_scripts_front' );
	function trx_addons_widget_audio_load_scripts_front() {
		if ( trx_addons_is_on( trx_addons_get_option( 'debug_mode' ) ) ) {
			wp_enqueue_script( 'trx_addons-widget_audio', trx_addons_get_file_url( TRX_ADDONS_PLUGIN_WIDGETS . 'audio/audio.js' ), array( 'jquery' ), null, true );
		}
	}
}


/** Merge widget specific styles into single stylesheet */
if ( ! function_exists( 'trx_addons_widget_audio_merge_styles' ) ) {
	add_filter( 'trx_addons_filter_merge_styles', 'trx_addons_widget_audio_merge_styles' );
	function trx_addons_widget_audio_merge_styles( $list ) {
		$list[] = TRX_ADDONS_PLUGIN_WIDGETS . 'audio/_audio.scss';
		return $list;
	}
}

/** Merge widget specific scripts into single file */
if ( ! function_exists( 'trx_addons_widget_audio_merge_scripts' ) ) {
	add_action( 'trx_addons_filter_merge_scripts', 'trx_addons_widget_audio_merge_scripts' );
	function trx_addons_widget_audio_merge_scripts( $list ) {
		$list[] = TRX_ADDONS_PLUGIN_WIDGETS . 'audio/audio.js';
		return $list;
	}
}


/*
 Widget trx_widget_audio
 -------------------------------------------------------------
[trx_widget_audio id="unique_id" title="Widget title"]
*/
if ( ! function_exists( 'trx_addons_sc_widget_audio' ) ) {
	function trx_addons_sc_widget_audio( $atts, $content = null ) {
		$atts = trx_addons_sc_prepare_atts(
			'trx_widget_audio', $atts, array(
				/* Individual params */
				'title'        => '',
				'subtitle'     => '',
				'media'        => '',
				'next_btn'     => '1',
				'prev_btn'     => '1',
				'next_text'    => '',
				'prev_text'    => '',
				'now_text'     => '',
				'track_time'   => '1',
				'track_scroll' => '1',
				'track_volume' => '1',
				/* Common params */
				'id'           => '',
				'class'        => '',
				'css'          => '',
			)
		);

		if ( function_exists( 'vc_param_group_parse_atts' ) && ! is_array( $atts['media'] ) ) {
			$atts['media'] = (array) vc_param_group_parse_atts( $atts['media'] );
		}
		$output = '';
		if ( is_array( $atts['media'] ) && count( $atts['media'] ) > 0 ) {
			foreach ( $atts['media'] as $k => $v ) {
				if ( ! empty( $v['description'] ) ) {
					$atts['media'][ $k ]['description'] = preg_replace( '/\\[(.*)\\]/', '<b>$1</b>', function_exists( 'vc_value_from_safe' ) ? vc_value_from_safe( $v['description'] ) : $v['description'] );
				}
				if ( ! empty( $v['embed'] ) && function_exists( 'vc_value_from_safe' ) ) {
					$atts['media'][ $k ]['embed'] = trim( vc_value_from_safe( $v['embed'] ) );
				}
				if ( ! empty( $v['cover'] ) ) {
					$atts['media'][ $k ]['cover'] = trx_addons_get_attachment_url( $v['cover'], 'full' );
				}
			}

			extract( $atts );
			$type   = 'trx_addons_widget_audio';
			$output = '';
			global $wp_widget_factory;
			if ( is_object( $wp_widget_factory ) && isset( $wp_widget_factory->widgets, $wp_widget_factory->widgets[ $type ] ) ) {
				$output = '<div' . ( $id ? ' id="' . esc_attr( $id ) . '"' : '' )
								. ' class="widget_area sc_widget_audio'
									. ( trx_addons_exists_vc() ? ' vc_widget_audio wpb_content_element' : '' )
									. ( ! empty( $class ) ? ' ' . esc_attr( $class ) : '' )
									. '"'
								. ( $css ? ' style="' . esc_attr( $css ) . '"' : '' )
							. '>';
				ob_start();
				the_widget( $type, $atts, trx_addons_prepare_widgets_args( $id ? $id . '_widget' : 'widget_audio', 'widget_audio' ) );
				$output .= ob_get_contents();
				ob_end_clean();
				$output .= '</div>';
			}
		}
		return apply_filters( 'trx_addons_sc_output', $output, 'trx_widget_audio', $atts, $content );
	}
}


/* Add [trx_widget_audio] in the VC shortcodes list */
if ( ! function_exists( 'trx_addons_sc_widget_audio_add_in_vc' ) ) {
	function trx_addons_sc_widget_audio_add_in_vc() {

		add_shortcode( 'trx_widget_audio', 'trx_addons_sc_widget_audio' );

		if ( ! trx_addons_exists_vc() ) {
			return;
		}

		vc_lean_map( 'trx_widget_audio', 'trx_addons_sc_widget_audio_add_in_vc_params' );
		class WPBakeryShortCode_Trx_Widget_Audio extends WPBakeryShortCode {}
	}
	add_action( 'init', 'trx_addons_sc_widget_audio_add_in_vc', 20 );
}

/* Return params */
if ( ! function_exists( 'trx_addons_sc_widget_audio_add_in_vc_params' ) ) {
	function trx_addons_sc_widget_audio_add_in_vc_params() {
		return apply_filters(
			'trx_addons_sc_map', array(
				'base'                    => 'trx_widget_audio',
				'name'                    => esc_html__( 'Audio player', 'trx_addons' ),
				'description'             => wp_kses_data( __( 'Insert widget with embedded audio from popular audio hosting: SoundCloud, etc. or with local hosted audio', 'trx_addons' ) ),
				'category'                => esc_html__( 'ThemeREX', 'trx_addons' ),
				'icon'                    => 'icon_trx_widget_audio',
				'class'                   => 'trx_widget_audio',
				'content_element'         => true,
				'is_container'            => false,
				'show_settings_on_create' => true,
				'params'                  => array_merge(
					array(
						array(
							'param_name'  => 'title',
							'heading'     => esc_html__( 'Widget title', 'trx_addons' ),
							'description' => wp_kses_data( __( 'Title of the widget', 'trx_addons' ) ),
							'admin_label' => true,
							'type'        => 'textfield',
						),
						array(
							'param_name'  => 'subtitle',
							'heading'     => esc_html__( 'Widget subtitle', 'trx_addons' ),
							'description' => wp_kses_data( __( 'Subtitle of the widget', 'trx_addons' ) ),
							'admin_label' => true,
							'type'        => 'textfield',
						),
						array(
							'param_name'       => 'next_btn',
							'heading'          => esc_html__( 'Next button', 'trx_addons' ),
							'description'      => wp_kses_data( __( 'Show next button', 'trx_addons' ) ),
							'edit_field_class' => 'vc_col-sm-6',
							'admin_label'      => true,
							'std'              => '1',
							'value'            => array( esc_html__( 'Show', 'trx_addons' ) => '1' ),
							'type'             => 'checkbox',
						),
						array(
							'param_name'       => 'prev_btn',
							'heading'          => esc_html__( 'Prev button', 'trx_addons' ),
							'description'      => wp_kses_data( __( 'Show prev button', 'trx_addons' ) ),
							'edit_field_class' => 'vc_col-sm-6',
							'admin_label'      => true,
							'std'              => '1',
							'value'            => array( esc_html__( 'Show', 'trx_addons' ) => '1' ),
							'type'             => 'checkbox',
						),
						array(
							'param_name'       => 'next_text',
							'heading'          => esc_html__( 'Next button caption', 'trx_addons' ),
							'description'      => wp_kses_data( __( 'Insert button caption', 'trx_addons' ) ),
							'edit_field_class' => 'vc_col-sm-6',
							'dependency'       => array(
								'element' => 'next_btn',
								'value'   => '1',
							),
							'admin_label'      => true,
							'type'             => 'textfield',
						),
						array(
							'param_name'       => 'prev_text',
							'heading'          => esc_html__( 'Prev button caption', 'trx_addons' ),
							'description'      => wp_kses_data( __( 'Insert button caption', 'trx_addons' ) ),
							'edit_field_class' => 'vc_col-sm-6',
							'dependency'       => array(
								'element' => 'prev_btn',
								'value'   => '1',
							),
							'admin_label'      => true,
							'type'             => 'textfield',
						),
						array(
							'param_name'  => 'now_text',
							'heading'     => esc_html__( "'Now Playing' text", 'trx_addons' ),
							'description' => wp_kses_data( __( "Change text of 'Now Playing' label. Write # if you want to hide label.", 'trx_addons' ) ),
							'admin_label' => true,
							'type'        => 'textfield',
						),
						array(
							'param_name'       => 'track_time',
							'heading'          => esc_html__( 'Track time', 'trx_addons' ),
							'description'      => wp_kses_data( __( 'Show track time', 'trx_addons' ) ),
							'edit_field_class' => 'vc_col-sm-4',
							'admin_label'      => true,
							'std'              => '1',
							'value'            => array( esc_html__( 'Show', 'trx_addons' ) => '1' ),
							'type'             => 'checkbox',
						),
						array(
							'param_name'       => 'track_scroll',
							'heading'          => esc_html__( 'Track scroll bar', 'trx_addons' ),
							'description'      => wp_kses_data( __( 'Show track scroll bar', 'trx_addons' ) ),
							'edit_field_class' => 'vc_col-sm-4',
							'admin_label'      => true,
							'std'              => '1',
							'value'            => array( esc_html__( 'Show', 'trx_addons' ) => '1' ),
							'type'             => 'checkbox',
						),
						array(
							'param_name'       => 'track_volume',
							'heading'          => esc_html__( 'Track volume bar', 'trx_addons' ),
							'description'      => wp_kses_data( __( 'Show track volume bar', 'trx_addons' ) ),
							'edit_field_class' => 'vc_col-sm-4',
							'admin_label'      => true,
							'std'              => '1',
							'value'            => array( esc_html__( 'Show', 'trx_addons' ) => '1' ),
							'type'             => 'checkbox',
						),
						array(
							'type'        => 'param_group',
							'param_name'  => 'media',
							'heading'     => esc_html__( 'Media', 'trx_addons' ),
							'description' => wp_kses_data( __( 'Specify values for each media item', 'trx_addons' ) ),
							'value'       => urlencode(
								json_encode(
									apply_filters(
										'trx_addons_sc_param_group_value', array(
											array(
												'url'     => '',
												'embed'   => '',
												'caption' => '',
												'author'  => '',
												'description' => '',
												'cover'   => '',
											),
										), 'trx_widget_audio'
									)
								)
							),
							'params'      => apply_filters(
								'trx_addons_sc_param_group_params', array_merge(
									array(
										array(
											'param_name'  => 'url',
											'heading'     => esc_html__( 'Audio URL', 'trx_addons' ),
											'description' => wp_kses_data( __( 'URL for local hosted audio file', 'trx_addons' ) ),
											'admin_label' => true,
											'type'        => 'textfield',
										),
										array(
											'param_name'  => 'embed',
											'heading'     => esc_html__( 'Embed code', 'trx_addons' ),
											'description' => wp_kses_data( __( 'or paste HTML code to embed audio', 'trx_addons' ) ),
											'type'        => 'textarea_safe',
										),
										array(
											'param_name'  => 'caption',
											'heading'     => esc_html__( 'Audio caption', 'trx_addons' ),
											'description' => wp_kses_data( __( 'Caption of this audio', 'trx_addons' ) ),
											'edit_field_class' => 'vc_col-sm-6',
											'admin_label' => true,
											'type'        => 'textfield',
										),
										array(
											'param_name'  => 'author',
											'heading'     => esc_html__( 'Author name', 'trx_addons' ),
											'description' => wp_kses_data( __( 'Name of the author', 'trx_addons' ) ),
											'edit_field_class' => 'vc_col-sm-6',
											'type'        => 'textfield',
										),
										array(
											'param_name'  => 'description',
											'heading'     => esc_html__( 'Description', 'trx_addons' ),
											'description' => wp_kses_data( __( 'Short description', 'trx_addons' ) ),
											'edit_field_class' => 'vc_col-sm-6',
											'type'        => 'textarea_safe',
										),
										array(
											'param_name'  => 'cover',
											'heading'     => esc_html__( 'Cover image', 'trx_addons' ),
											'description' => wp_kses_data( __( 'Select or upload cover image or write URL from other site', 'trx_addons' ) ),
											'edit_field_class' => 'vc_col-sm-6',
											'type'        => 'attach_image',
										),
									)
								), 'trx_widget_audio'
							),
						),
					),
					trx_addons_vc_add_id_param()
				),
			), 'trx_widget_audio'
		);
	}
}




/*
 Elementor Widget */
/* ------------------------------------------------------ */
if ( ! function_exists( 'trx_addons_sc_widget_audio_add_in_elementor' ) ) {
	add_action( 'elementor/widgets/widgets_registered', 'trx_addons_sc_widget_audio_add_in_elementor' );
	function trx_addons_sc_widget_audio_add_in_elementor() {

		if ( ! class_exists( 'TRX_Addons_Elementor_Widget' ) ) {
			return;
		}

		class TRX_Addons_Elementor_Widget_Audio extends TRX_Addons_Elementor_Widget {

			/**
			 * Widget base constructor.
			 *
			 * Initializing the widget base class.
			 *
			 * @since 1.6.41
			 * @access public
			 *
			 * @param array      $data Widget data. Default is an empty array.
			 * @param array|null $args Optional. Widget default arguments. Default is null.
			 */
			public function __construct( $data = [], $args = null ) {
				parent::__construct( $data, $args );
				$this->add_plain_params(
					[
						'cover' => 'url'
					]
				);
			}

			/**
			 * Retrieve widget name.
			 *
			 * @since 1.6.41
			 * @access public
			 *
			 * @return string Widget name.
			 */
			public function get_name() {
				return 'trx_widget_audio';
			}

			/**
			 * Retrieve widget title.
			 *
			 * @since 1.6.41
			 * @access public
			 *
			 * @return string Widget title.
			 */
			public function get_title() {
				return __( 'Widget: Audio', 'trx_addons' );
			}

			/**
			 * Retrieve widget icon.
			 *
			 * @since 1.6.41
			 * @access public
			 *
			 * @return string Widget icon.
			 */
			public function get_icon() {
				return 'eicon-posts-ticker';
			}

			/**
			 * Retrieve the list of categories the widget belongs to.
			 *
			 * Used to determine where to display the widget in the editor.
			 *
			 * @since 1.6.41
			 * @access public
			 *
			 * @return array Widget categories.
			 */
			public function get_categories() {
				return [ 'trx_addons-widgets' ];
			}

			/**
			 * Register widget controls.
			 *
			 * Adds different input fields to allow the user to change and customize the widget settings.
			 *
			 * @since 1.6.41
			 * @access protected
			 */
			protected function register_controls() {
				$this->start_controls_section(
					'section_sc_audio',
					[
						'label' => __( 'Widget: Audio', 'trx_addons' ),
					]
				);

				$this->add_control(
					'title',
					[
						'label'       => __( 'Title', 'trx_addons' ),
						'label_block' => false,
						'type'        => \Elementor\Controls_Manager::TEXT,
						'placeholder' => __( 'Widget title', 'trx_addons' ),
						'default'     => '',
					]
				);

				$this->add_control(
					'subtitle',
					[
						'label'       => __( 'Subtitle', 'trx_addons' ),
						'label_block' => false,
						'type'        => \Elementor\Controls_Manager::TEXT,
						'placeholder' => __( 'Widget subtitle', 'trx_addons' ),
						'default'     => '',
					]
				);

				$this->add_control(
					'next_btn',
					[
						'label'        => __( 'Show "NEXT" button', 'trx_addons' ),
						'label_block'  => false,
						'type'         => \Elementor\Controls_Manager::SWITCHER,
						'label_off'    => __( 'Hide', 'trx_addons' ),
						'label_on'     => __( 'Show', 'trx_addons' ),
						'return_value' => '1',
					]
				);

				$this->add_control(
					'prev_btn',
					[
						'label'        => __( 'Show "PREV" button', 'trx_addons' ),
						'label_block'  => false,
						'type'         => \Elementor\Controls_Manager::SWITCHER,
						'label_off'    => __( 'Hide', 'trx_addons' ),
						'label_on'     => __( 'Show', 'trx_addons' ),
						'return_value' => '1',
					]
				);

				$this->add_control(
					'next_text',
					[
						'label'       => __( '"NEXT" button caption', 'trx_addons' ),
						'label_block' => false,
						'type'        => \Elementor\Controls_Manager::TEXT,
						'placeholder' => __( 'Next', 'trx_addons' ),
						'default'     => '',
					]
				);

				$this->add_control(
					'prev_text',
					[
						'label'       => __( '"PREV" button caption', 'trx_addons' ),
						'label_block' => false,
						'type'        => \Elementor\Controls_Manager::TEXT,
						'placeholder' => __( 'Prev', 'trx_addons' ),
						'default'     => '',
					]
				);

				$this->add_control(
					'now_text',
					[
						'label'       => __( '"Now Playing" text', 'trx_addons' ),
						'label_block' => false,
						'type'        => \Elementor\Controls_Manager::TEXT,
						'placeholder' => __( 'Now Playing', 'trx_addons' ),
						'default'     => '',
					]
				);

				$this->add_control(
					'track_time',
					[
						'label'        => __( 'Track time', 'trx_addons' ),
						'label_block'  => false,
						'type'         => \Elementor\Controls_Manager::SWITCHER,
						'label_off'    => __( 'Hide', 'trx_addons' ),
						'label_on'     => __( 'Show', 'trx_addons' ),
						'default'      => '1',
						'return_value' => '1',
					]
				);

				$this->add_control(
					'track_scroll',
					[
						'label'        => __( 'Track scroll bar', 'trx_addons' ),
						'label_block'  => false,
						'type'         => \Elementor\Controls_Manager::SWITCHER,
						'label_off'    => __( 'Hide', 'trx_addons' ),
						'label_on'     => __( 'Show', 'trx_addons' ),
						'default'      => '1',
						'return_value' => '1',
					]
				);

				$this->add_control(
					'track_volume',
					[
						'label'        => __( 'Track volume bar', 'trx_addons' ),
						'label_block'  => false,
						'type'         => \Elementor\Controls_Manager::SWITCHER,
						'label_off'    => __( 'Hide', 'trx_addons' ),
						'label_on'     => __( 'Show', 'trx_addons' ),
						'default'      => '1',
						'return_value' => '1',
					]
				);

				$this->add_control(
					'media',
					[
						'label'   => '',
						'type'    => \Elementor\Controls_Manager::REPEATER,
						'default' => apply_filters(
							'trx_addons_sc_param_group_value', [
								[
									'url'         => '',
									'embed'       => '',
									'caption'     => __( 'Song', 'trx_addons' ),
									'author'      => __( 'Author', 'trx_addons' ),
									'description' => $this->get_default_description(),
									'cover'       => [ 'url' => '' ],
								],
							], 'trx_widget_audio'
						),
						'fields'  => apply_filters(
							'trx_addons_sc_param_group_params', array_merge(
								[
									[
										'name'        => 'url',
										'label'       => __( 'URL', 'trx_addons' ),
										'label_block' => false,
										'type'        => \Elementor\Controls_Manager::TEXT,
										'default'     => '',
										'placeholder' => __( 'http://audio.url', 'trx_addons' ),
									],
									[
										'name'        => 'embed',
										'label'       => __( 'Embed code', 'trx_addons' ),
										'label_block' => true,
										'description' => wp_kses_data( __( 'Paste HTML code to embed audio (to use it instead URL from the field above)', 'trx_addons' ) ),
										'type'        => \Elementor\Controls_Manager::TEXTAREA,
										'default'     => '',
										'rows'        => 10,
									],
									[
										'name'        => 'caption',
										'label'       => __( 'Audio caption', 'trx_addons' ),
										'label_block' => false,
										'type'        => \Elementor\Controls_Manager::TEXT,
										'placeholder' => __( 'Caption', 'trx_addons' ),
										'default'     => '',
									],
									[
										'name'        => 'author',
										'label'       => __( 'Author', 'trx_addons' ),
										'label_block' => false,
										'type'        => \Elementor\Controls_Manager::TEXT,
										'placeholder' => __( 'Author name', 'trx_addons' ),
										'default'     => '',
									],
									[
										'name'        => 'description',
										'label'       => __( 'Description', 'trx_addons' ),
										'label_block' => true,
										'description' => wp_kses_data( __( 'Short description', 'trx_addons' ) ),
										'type'        => \Elementor\Controls_Manager::TEXTAREA,
										'default'     => '',
										'rows'        => 10,
									],
									[
										'name'        => 'cover',
										'label'       => __( 'Cover image', 'trx_addons' ),
										'label_block' => true,
										'type'        => \Elementor\Controls_Manager::MEDIA,
										'default'     => [
											'url' => '',
										],
									],
								]
							),
							'trx_widget_audio'
						),
					]
				);

				$this->end_controls_section();
			}

			/**
			 * Render widget's template for the editor.
			 *
			 * Written as a Backbone JavaScript template and used to generate the live preview.
			 *
			 * @since 1.6.41
			 * @access protected
			 */
			protected function _content_template() {
				trx_addons_get_template_part(
					TRX_ADDONS_PLUGIN_WIDGETS . 'audio/tpe.audio.php',
					'trx_addons_args_widget_audio',
					array( 'element' => $this )
				);
			}
		}

		/* Register widget */
		\Elementor\Plugin::$instance->widgets_manager->register_widget_type( new TRX_Addons_Elementor_Widget_Audio() );
	}
}


/*
 Disable our widgets (shortcodes) to use in Elementor */
/* because we create special Elementor's widgets instead */
if ( ! function_exists( 'trx_addons_widget_audio_black_list' ) ) {
	add_action( 'elementor/widgets/black_list', 'trx_addons_widget_audio_black_list' );
	function trx_addons_widget_audio_black_list( $list ) {
		$list[] = 'trx_addons_widget_audio';
		return $list;
	}
}

