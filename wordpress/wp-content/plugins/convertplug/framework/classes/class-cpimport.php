<?php
/**
 * Prohibit direct script loading.
 *
 * @package Convert_Plus.
 */

defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

if ( ! class_exists( 'Convert_Plug_Import' ) ) {
	/**
	 * Class = Convert_Plug_Import.
	 */
	class Convert_Plug_Import {

		/**
		 * Absolute path to Presets directory.
		 *
		 * @var string
		 */
		private $cp_preset_dir = '';

		/**
		 * Module Name for which the presets are to be imported.
		 *
		 * @var string
		 */
		private $module = '';

		/**
		 * $presets_list list forprest.
		 *
		 * @var string.
		 */
		public $presets_list;

		/**
		 * Constructor.
		 *
		 * @param string $module module name.
		 * @param string $preset preset name.
		 */
		public function __construct( $module = '', $preset = '' ) {

			// module to be imported.
			$this->module = strtolower( $module );
			$this->preset = $preset;

			$this->cp_presets_list( $this->module, $this->preset );

			$this->cp_import_preset_frontend( $this->module, $this->preset );

		}

		/**
		 * Function Name: cp_import_preset_frontend CP Presets Importer.
		 *
		 * @param  string $module  modal | info bar |slide in.
		 * @param  string $preset  preset.
		 */
		public function cp_import_preset_frontend( $module, $preset ) {

			$this->cp_preset_dir = CP_BASE_URL . 'modules/' . $module . '/presets/';

			$option = 'cp_' . $module . '_preset_templates';

			$this->presets_list = get_option( $option );

			foreach ( $this->presets_list as $current_slug => $name ) {

				if ( '' !== $preset && $current_slug != $preset ) {
					continue;
				}

				$preset_atts = wp_remote_get( $this->cp_preset_dir . $current_slug . '.txt' );

				if ( null === $preset_atts ) {
					wp_send_json_error();
				} else {
					$preset_atts = json_decode( $preset_atts, true );
				}

				// Generate list of images to be downloaded.
				$images        = $this->cp_list_images( $preset_atts, $current_slug );
				$image_present = 0;

				foreach ( $images as $img_url => $image_atts ) {
					if ( in_array( $current_slug, $images[ $img_url ]['preset_slug'] ) ) {
						$image_present++;
					}
				}

				if ( 0 < $image_present ) {
					$this->cp_import_preset( $images, $preset_atts, $current_slug );
				} else {
					update_option( 'cp_' . $module . '_' . $current_slug, $preset_atts );
				}
			}

			wp_send_json_success();

		}

		/**
		 * Function Name: cp_import_preset.
		 *
		 * @param  string $images       string parameter.
		 * @param  array  $preset_atts  array parameter.
		 * @param  string $current_slug string parameter.
		 */
		public function cp_import_preset( $images, $preset_atts, $current_slug ) {

			if ( ! empty( $images ) ) {

				set_time_limit( 0 );

				foreach ( $images as $img_url => $image_atts ) {

					if ( ! isset( $image_atts['id'] ) || false === get_post_status( $image_atts['id'] ) ) {
						$img_id                   = $this->download_image( $img_url );
						$images[ $img_url ]['id'] = $img_id;
					}

					$slug_id = $images[ $img_url ]['id'];
					$attr    = $images[ $img_url ]['attr_name'];

					if ( isset( $images[ $img_url ]['preset_slug'] ) ) {
						if ( in_array( $current_slug, $images[ $img_url ]['preset_slug'] ) ) {
							$this->cp_create_preset( $preset_atts, $current_slug, $slug_id, $attr );
						}
					}
				}
				update_option( 'cp_import_images', $images );
			}
		}

		/**
		 * Function Name: cp_create_preset.
		 *
		 * @param  array  $preset_atts  array parameter.
		 * @param  string $current_slug string val.
		 * @param  string $slug_id           string val.
		 * @param  array  $attr         array parameter.
		 */
		public function cp_create_preset( $preset_atts, $current_slug, $slug_id, $attr ) {

			$cp_preset = get_option( 'cp_' . $this->module . '_' . $current_slug, array() );

			if ( empty( $cp_preset ) ) {
				$cp_preset = $preset_atts;
			}

			if ( function_exists( 'wp_get_attachment_image_url' ) ) {
				$url = wp_get_attachment_image_url( $slug_id, 'full' );
			} else {
				$url = wp_get_attachment_url( $slug_id );
			}

			$url = preg_replace( '(^https?:)', '', $url );

			$cp_preset['style_settings'][ $attr ] = $url;

			update_option( 'cp_' . $this->module . '_' . $current_slug, $cp_preset );

		}

		/**
		 * Function Name: cp_list_images Populates array $this->import_images with the list of URLs of images to be imported.
		 *
		 * @param  array  $preset_atts  Style atts of a preset.
		 * @param  string $current_slug string parameter.
		 * @return array               style of array.
		 */
		public function cp_list_images( $preset_atts, $current_slug ) {

			$image_list = get_option( 'cp_import_images', array() );

			// _bg_image_custom_url.
			if ( isset( $preset_atts['style_settings'][ $this->module . '_bg_image_custom_url' ] ) && '' !== $preset_atts['style_settings'][ $this->module . '_bg_image_custom_url' ] ) {

				if ( ! isset( $image_list[ $preset_atts['style_settings'][ $this->module . '_bg_image_custom_url' ] ]['id'] ) ) {

					$image_list[ $preset_atts['style_settings'][ $this->module . '_bg_image_custom_url' ] ] = array(
						'attr_name'   => $this->module . '_bg_image_custom_url',
						'preset_slug' => array( strtolower( $current_slug ) ),
					);

				} else {

					$slug    = $image_list[ $preset_atts['style_settings'][ $this->module . '_bg_image_custom_url' ] ]['preset_slug'];
					$slug_id = $image_list[ $preset_atts['style_settings'][ $this->module . '_bg_image_custom_url' ] ]['id'];

					$slug[] = strtolower( $current_slug );

					$image_list[ $preset_atts['style_settings'][ $this->module . '_bg_image_custom_url' ] ] = array(
						'id'          => $slug_id,
						'attr_name'   => $this->module . '_bg_image_custom_url',
						'preset_slug' => array_unique( $slug ),
					);

				}
			}

			// _img_custom_url.
			if ( isset( $preset_atts['style_settings'][ $this->module . '_img_custom_url' ] ) && '' !== $preset_atts['style_settings'][ $this->module . '_img_custom_url' ] ) {

				if ( ! isset( $image_list[ $preset_atts['style_settings'][ $this->module . '_img_custom_url' ] ]['id'] ) ) {

					$image_list[ $preset_atts['style_settings'][ $this->module . '_img_custom_url' ] ] = array(
						'attr_name'   => $this->module . '_img_custom_url',
						'preset_slug' => array( strtolower( $current_slug ) ),
					);

				} else {

					$slug    = $image_list[ $preset_atts['style_settings'][ $this->module . '_img_custom_url' ] ]['preset_slug'];
					$slug_id = $image_list[ $preset_atts['style_settings'][ $this->module . '_img_custom_url' ] ]['id'];

					$slug[] = strtolower( $current_slug );

					$image_list[ $preset_atts['style_settings'][ $this->module . '_img_custom_url' ] ] = array(
						'id'          => $slug_id,
						'attr_name'   => $this->module . '_img_custom_url',
						'preset_slug' => array_unique( $slug ),
					);

				}
			}

			// close_img_custom_url.
			if ( isset( $preset_atts['style_settings'][ $this->module . '_close_img_custom_url' ] ) && '' !== $preset_atts['style_settings'][ $this->module . '_close_img_custom_url' ] ) {

				if ( ! isset( $image_list[ $preset_atts['style_settings'][ $this->module . '_close_img_custom_url' ] ]['id'] ) ) {

					$image_list[ $preset_atts['style_settings'][ $this->module . '_close_img_custom_url' ] ] = array(
						'attr_name'   => $this->module . '_close_img_custom_url',
						'preset_slug' => array( strtolower( $current_slug ) ),
					);

				} else {

					$slug    = $image_list[ $preset_atts['style_settings'][ $this->module . '_close_img_custom_url' ] ]['preset_slug'];
					$slug_id = $image_list[ $preset_atts['style_settings'][ $this->module . '_close_img_custom_url' ] ]['id'];

					$slug[] = strtolower( $current_slug );

					$image_list[ $preset_atts['style_settings'][ $this->module . '_close_img_custom_url' ] ] = array(
						'id'          => $slug_id,
						'attr_name'   => $this->module . '_close_img_custom_url',
						'preset_slug' => array_unique( $slug ),
					);

				}
			}

			return $image_list;

		}

		/**
		 * Function Name: download_image .
		 *
		 * @param  string $image_path path of the image.
		 * @return string            file id.
		 */
		public function download_image( $image_path ) {

			require_once ABSPATH . 'wp-admin/includes/image.php';
			$timeout_seconds = 5;
			$temp_file       = download_url( $image_path, $timeout_seconds );
			$filetype        = wp_check_filetype( basename( $image_path ), null );
			if ( ! is_wp_error( $temp_file ) ) {
				// Array based on $_FILE as seen in PHP file uploads.
				$file      = array(
					'name'     => basename( $image_path ), // ex: wp-header-logo.png.
					'type'     => $filetype,
					'tmp_name' => $temp_file,
					'error'    => 0,
					'size'     => filesize( $temp_file ),
				);
				$overrides = array(
					'test_form'   => false,
					'test_size'   => true,
					'test_upload' => true,
				);

				$results = wp_handle_sideload( $file, $overrides );

				if ( ! empty( $results['error'] ) ) {
					return 'can not import template image';
				} else {

					$wp_upload_dir  = wp_upload_dir();
					$file_path      = $wp_upload_dir['basedir'] . str_replace( $wp_upload_dir['baseurl'], '', $results['url'] );
					$parent_post_id = 0;
					$filetype       = wp_check_filetype( basename( $file_path ), null );
					$file_data      = array(
						'guid'           => $wp_upload_dir['url'] . '/' . basename( $file_path ),
						'post_mime_type' => $filetype['type'],
						'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $file_path ) ),
						'post_content'   => '',
						'post_status'    => 'inherit',
					);

					$file_id       = wp_insert_attachment( $file_data, $file_path, $parent_post_id );
					$file_metadata = wp_generate_attachment_metadata( $file_id, $file_path );
					wp_update_attachment_metadata( $file_id, $file_metadata );

					return (string) $file_id;

				}
			}

		}

		/**
		 * Function Name: cp_presets_list.
		 *
		 * @param  string $module string parameter.
		 * @param  array  $preset array of attributes.
		 */
		public function cp_presets_list( $module, $preset ) {

			$styles = array();
			$fun    = 'cp_add_' . $module . '_template';

			// Get preset array list.
			$styles = $fun( $styles, '', $module );

			$option = 'cp_' . $module . '_preset_templates';

			$existing_templates = get_option( $option );

			if ( is_array( $existing_templates ) ) {
				foreach ( $existing_templates as $key => $value ) {
					if ( isset( $styles[ $key ] ) ) {
						unset( $styles[ $key ] );
					}
				}

				$styles = array_merge( $existing_templates, $styles );
			}

			// get screen shot images.
			$screenshot_images = get_option( 'cp_screenshots_images', array() );

			// upload screen shot URL to uploads directory.
			foreach ( $styles as $key => $style ) {

				if ( '' !== $preset && $key != $preset ) {
					continue;
				}

				$screenshot_url = $style[3];

				// if screen shot URL is not present.
				if ( ! isset( $screenshot_images[ $screenshot_url ] ) ) {

					$source        = $screenshot_url;
					$wp_upload_dir = wp_upload_dir();
					$dir           = $wp_upload_dir['basedir'] . '/cp_preset_screenshots';
					$upload_url    = $wp_upload_dir['baseurl'];
					$ext           = pathinfo( $source, PATHINFO_EXTENSION );
					$file_name     = 'cp_' . $style[7] . '_screenshot.' . $ext;

					// upload image to WP upload directory.
					$result = $this->cp_upload_image( $dir, $source, $file_name );

					if ( 'success' === $result || 'Already Present' === $result ) {

						$url = $upload_url . '/cp_preset_screenshots/' . $file_name;

						$screenshot_images[ $screenshot_url ] = array(
							'preset_slug' => $style[7],
						);

					} else {
						wp_send_json_error();
					}
				} else {

					$url = $screenshot_url;

				}

				// replace URL in array.
				$styles[ $key ][3] = $url;

			}

			$option = 'cp_' . $module . '_preset_templates';

			// save all screen shot URLs to option.
			update_option( 'cp_screenshots_images', $screenshot_images );

			// Save array to option.
			update_option( $option, $styles );

			$this->presets_list = $styles;

		}

		/**
		 * Function Name:cp_upload_image.
		 *
		 * @param  string $dir      directroy name parameter.
		 * @param  string $source   source parameter.
		 * @param  string $file_name filename parameter.
		 * @return string message content.
		 */
		public function cp_upload_image( $dir, $source, $file_name ) {

			$result = 'success';
			require_once ABSPATH . 'wp-admin/includes/file.php';
			$timeout_seconds = 5;

			// Download file to temp dir.
			$temp_file = download_url( $source, $timeout_seconds );
			$filetype  = wp_check_filetype( basename( $source ), null );

			if ( ! is_wp_error( $temp_file ) ) {
				// Array based on $_FILE as seen in PHP file uploads.
				$file = array(
					'name'     => basename( $file_name ), // ex: wp-header-logo.png.
					'type'     => $filetype,
					'tmp_name' => $temp_file,
					'error'    => 0,
					'size'     => filesize( $temp_file ),
				);

				$overrides = array(
					'test_form'   => false,
					'test_size'   => true,
					'test_upload' => true,
				);

				// Move the temporary file into the uploads directory.
				$results = wp_handle_sideload( $file, $overrides );

				$file_content = wp_remote_get( $results['url'] );

				$file = array(
					'base'    => $dir,
					'file'    => $file_name,
					'content' => $file_content,
				);

				if ( wp_mkdir_p( $file['base'] ) && ! file_exists( trailingslashit( $file['base'] ) . $file['file'] ) ) {
					$write_file = trailingslashit( $file['base'] ) . $file['file'];
					if ( Convert_Plug_Filesystem::prefix_get_filesystem()->put_contents( $write_file, $file['content']['body'] ) ) {
						$result = 'success';
					} else {
						$result = 'failure';
					}
				} else {
					$result = 'Already Present';
				}

				return $result;

			}

		}

	}


}

