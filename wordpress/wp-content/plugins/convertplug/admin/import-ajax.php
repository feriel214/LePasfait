<?php
/**
 * Prohibit direct script loading.
 *
 * @package Convert_Plus.
 */

defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

add_action( 'wp_ajax_cp_import_modal', 'cp_import_modal' );
add_action( 'wp_ajax_cp_import_info_bar', 'cp_import_info_bar' );
add_action( 'wp_ajax_cp_import_slide_in', 'cp_import_slide_in' );

if ( ! function_exists( 'cp_import_modal' ) ) {
	/**
	 * Function name: cp_import_modal cp-import-modal.
	 */
	function cp_import_modal() {

		if ( ! current_user_can( 'access_cp' ) ) {
			die( -1 );
		}

		check_admin_referer( 'cp_import_module_nonce', 'security_nonce' );

		$file     = $_POST['file'];
		$title    = sanitize_title( $_POST['file']['title'] );
		$filename = sanitize_file_name( $_POST['file']['filename'] );
		$file     = realpath( get_attached_file( intval( $_POST['file']['id'] ) ) );

		// Get the name of the directory inside the exported zip.
		$zip = new ZipArchive();
		$res = $zip->open( $file );
		if ( ! is_bool( $res ) ) {
			/* translators: %s: zip error code */
			echo sprintf( esc_html__( 'Failed to Open. Error Code: %s ', 'smile' ), esc_attr( $res ) );
			die();
		}

		// Set the path variable for extracting the zip.
		$paths             = array();
		$paths             = wp_upload_dir();
		$paths['export']   = 'cp_export';
		$paths['tempdir']  = trailingslashit( $paths['basedir'] ) . 'cp_modal';
		$paths['temp']     = trailingslashit( $paths['basedir'] ) . 'cp_modal/' . $title;
		$paths['tempurl']  = trailingslashit( $paths['baseurl'] ) . 'cp_modal/';
		$paths['basepath'] = $paths['basedir'] . '/cp_modal/';
		$folder_path       = $paths['basedir'] . '/cp_modal/' . $title;

		// Create the respective directory inside wp-uploads directory.
		if ( ! is_dir( $paths['temp'] ) ) {
			$tempdir = smile_backend_create_folder( $paths['temp'], false );
		}

		WP_Filesystem();
		$destination_path = $paths['tempdir'];

		// Extract the zip to our newly created directory.
		$unzipfile = unzip_file( $file, $destination_path );

		if ( ! $unzipfile ) {
			die( esc_html__( 'Unable to extract the file.', 'smile' ) );
		}

		// Sanitize folder name.
		$new_folder_name = sanitize_file_name( $title );

		// Grant permission.
		chmod( $folder_path, 0755 );

		$new_folder_path = $paths['basepath'] . $new_folder_name;

		// Rename folder.
		rename( $folder_path, $new_folder_path );

		// Rename settings file.
		rename( $new_folder_path . '/' . $title . '.txt', $new_folder_path . '/' . $new_folder_name . '.txt' );

		// Set the json file file url to get the settings for the style.
		$json_file = $paths['tempurl'] . $new_folder_name . '/' . $new_folder_name . '.txt';

		$data_option    = 'smile_modal_styles';
		$variant_option = 'modal_variant_tests';

		// Read the text file containing the json formatted settings of style and decode it.
		$content = wp_remote_get( $json_file );

		$json = $content['body'];

		$obj          = json_decode( $json, true );
		$import_style = array();
		$new_style_id = $obj['style_id'];
		$cp_module    = $obj['module'];
		$style_name   = $obj['style_name'];

		if ( 'modal' !== $cp_module ) {
			wp_send_json(
				array(
					'status'      => 'error',
					'description' => /* translators:%s module name .*/
					sprintf( esc_html__( 'Seems that the file have uploaded the wrong file. This file can be imported for %s ', 'smile' ), str_replace( '_', ' ', $cp_module ) ),
				)
			);
		}

		if ( ! isset( $obj['style_id'] ) ) {
			wp_send_json(
				array(
					'status'      => 'error',
					'description' => esc_html__( 'Seems that the file is different from the exported modal zip. Please try with another zip file.', 'smile' ),
				)
			);
		}
		$style_settings = (array) $obj['style_settings'];

		if ( isset( $obj['media'] ) ) {
			$media = (array) $obj['media'];
		}

		if ( isset( $obj['variants'] ) ) {
			foreach ( $obj['variants'] as $key => $value ) {
				$variant_analytics = maybe_unserialize( $value['style_settings'] );
				if ( ! empty( $variant_analytics['analytics'] ) ) {
					$analytics_value = $variant_analytics['analytics'];
					$style_id        = $variant_analytics['variant_style_id'];
					smile_update_custom_conversions( $analytics_value, $style_id );
				}
			}
		}

		if ( isset( $style_settings['analytics'] ) && ! empty( $style_settings['analytics'] ) ) {
			$analytics_value = $style_settings['analytics'];
			$style_id        = $style_settings['style_id'];
			smile_update_custom_conversions( $analytics_value, $style_id );
		}

		if ( isset( $style_settings['cp_google_fonts'] ) ) {
			$google_fonts = explode( ',', $style_settings['cp_google_fonts'] );
			cp_import_google_fonts( $google_fonts );
		}

		$media_ids = array();

		if ( isset( $media ) && is_array( $media ) ) {
			// Import media if any.
			foreach ( $media as $option => $value ) {

				$value = str_replace( $title, $new_folder_name, $value );

				// $filename should be the path to a file in the upload directory.
				$filename = $paths['tempdir'] . '/' . $value;

				// Check the type of file. We'll use this as the 'post_mime_type'.
				$filetype = wp_check_filetype( basename( $filename ), null );

				// Get the path to the upload directory.
				$wp_upload_dir = wp_upload_dir();

				// Prepare an array of post data for the attachment.
				$attachment = array(
					'guid'           => $wp_upload_dir['url'] . '/' . basename( $filename ),
					'post_mime_type' => $filetype['type'],
					'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
					'post_content'   => '',
					'post_status'    => 'inherit',
				);

				// Insert the attachment.
				$option               = ( 'close_image' === $option ) ? 'close_img' : $option;
				$media_ids[ $option ] = wp_insert_attachment( $attachment, $filename );

				// Make sure that this file is included, as wp_generate_attachment_metadata() .depends on it.
				require_once ABSPATH . 'wp-admin/includes/image.php';

				// Generate the metadata for the attachment, and update the database record.
				$attach_data = wp_generate_attachment_metadata( $media_ids[ $option ], $filename );
				wp_update_attachment_metadata( $media_ids[ $option ], $attach_data );

				// Get the attachment id and update the setting for media in style.
				if ( isset( $style_settings[ $option ] ) ) {
					$media_image = $style_settings[ $option ];
					$media_image = str_replace( '%7C', '|', $media_image );
					if ( false !== strpos( $media_image, 'http' ) ) {
						$media_image = explode( '|', $media_image );
						$media_image = $media_image[1];
					} else {
						$media_image = explode( '|', $media_image );
						$media_image = $media_image[1];
					}
					$media_image               = $media_ids[ $option ] . '|' . $media_image;
					$style_settings[ $option ] = $media_image;

				}
			}
		}

		$prev_styles   = get_option( $data_option );
		$variant_tests = get_option( $variant_option );

		$prev_styles = empty( $prev_styles ) ? array() : $prev_styles;
		$update      = false;

		foreach ( $style_settings as $title => $value ) {
			if ( ! is_array( $value ) ) {
				$value                  = htmlspecialchars_decode( $value );
				$import_style[ $title ] = $value;
			} else {
				foreach ( $value as $ex_title => $ex_val ) {
					foreach ( $ex_val as $key1 => $value1 ) {
						$val[ $key1 ] = htmlspecialchars_decode( $value1 );
					}
				}
				$import_style[ $title ] = $val;
			}
		}
		$import                   = $obj;
		$import['style_settings'] = maybe_serialize( $import_style );

		if ( isset( $import['variants'] ) ) {
			unset( $import['variants'] );
		}

		if ( ! empty( $prev_styles ) ) {
			foreach ( $prev_styles as $key => $style ) {
				$style_id = $style['style_id'];
				if ( $new_style_id == $style_id ) {
					$update = false;
					wp_send_json(
						array(
							'status'      => 'error',
							'description' => esc_html__( 'Style Already Exists! Please try importing another style.', 'smile' ),
						)
					);

				} else {
					$update = true;
				}
			}
		} else {
			$update = true;
		}

		if ( $update ) {
			array_push( $prev_styles, $import );
			$status = update_option( $data_option, $prev_styles );

			// Import variants.
			if ( isset( $obj['variants'] ) ) {
				$variant_tests[ $new_style_id ] = $obj['variants'];
				$status                         = update_option( $variant_option, $variant_tests );
			}
		} else {
			$status = false;
		}

		// Check the status of import and return the object accordingly.
		if ( $status ) {
			wp_send_json(
				array(
					'status'      => 'success',
					'description' => ucwords( str_replace( '_', ' ', $style_name ) ) . ' ' . __( 'imported successfully!', 'smile' ),
				)
			);
		} else {
			wp_send_json(
				array(
					'status'      => 'error',
					'description' => __( 'Something went wrong! Please try again with different file.', 'smile' ),
				)
			);
		}
	}
}

if ( ! function_exists( 'cp_import_info_bar' ) ) {
	/**
	 * Function Name: cp_import_info_bar cp-import-style.
	 */
	function cp_import_info_bar() {

		if ( ! current_user_can( 'access_cp' ) ) {
			die( -1 );
		}
		check_admin_referer( 'cp_import_module_nonce', 'security_nonce' );

		$file     = $_POST['file'];
		$title    = sanitize_title( $_POST['file']['title'] );
		$filename = sanitize_file_name( $_POST['file']['filename'] );
		$file     = realpath( get_attached_file( intval( $_POST['file']['id'] ) ) );

		// Get the name of the directory inside the exported zip.
		$zip = new ZipArchive();
		$res = $zip->open( $file );
		if ( ! is_bool( $res ) ) {
			/* translators: %s: zip error code */
			echo sprintf( esc_html__( 'Failed to Open. Error Code: %s ', 'smile' ), esc_attr( $res ) );
			die();
		}

		// Set the path variable for extracting the zip.
		$paths             = array();
		$paths             = wp_upload_dir();
		$paths['export']   = 'cp_export';
		$paths['tempdir']  = trailingslashit( $paths['basedir'] ) . 'cp_modal';
		$paths['temp']     = trailingslashit( $paths['basedir'] ) . 'cp_modal/' . $title;
		$paths['tempurl']  = trailingslashit( $paths['baseurl'] ) . 'cp_modal/';
		$paths['basepath'] = $paths['basedir'] . '/cp_modal/';
		$folder_path       = $paths['basedir'] . '/cp_modal/' . $title;

		// Create the respective directory inside wp-uploads directory.
		if ( ! is_dir( $paths['temp'] ) ) {
			$tempdir = smile_backend_create_folder( $paths['temp'], false );
		}

		WP_Filesystem();
		$destination_path = $paths['tempdir'];

		// Extract the zip to our newly created directory.
		$unzipfile = unzip_file( $file, $destination_path );

		if ( ! $unzipfile ) {
			die( esc_html__( 'Unable to extract the file.', 'smile' ) );
		}

		// Sanitize folder name.
		$new_folder_name = sanitize_file_name( $title );

		// Grant permission.
		chmod( $folder_path, 0755 );

		$new_folder_path = $paths['basepath'] . $new_folder_name;

		// Rename folder.
		rename( $folder_path, $new_folder_path );

		// rename settings file.
		rename( $new_folder_path . '/' . $title . '.txt', $new_folder_path . '/' . $new_folder_name . '.txt' );

		// Set the json file file url to get the settings for the style.
		$json_file = $paths['tempurl'] . $new_folder_name . '/' . $new_folder_name . '.txt';

		$data_option    = 'smile_info_bar_styles';
		$variant_option = 'info_bar_variant_tests';

		// Read the text file containing the json formatted settings of style and decode it.
		$content = wp_remote_get( $json_file );

		$json = $content['body'];

		$obj          = json_decode( $json, true );
		$import_style = array();
		$new_style_id = $obj['style_id'];
		$cp_module    = $obj['module'];
		$style_name   = $obj['style_name'];

		if ( 'info_bar' !== $cp_module ) {
			wp_send_json(
				array(
					'status'      => 'error',
					'description' => /* translators:%s module name .*/
					sprintf( __( 'Seems that the file have uploaded the wrong file. This file can be imported for %s ', 'smile' ), str_replace( '_', ' ', $cp_module ) ),
				)
			);
		}

		if ( ! isset( $obj['style_id'] ) ) {
			wp_send_json(
				array(
					'status'      => 'error',
					'description' => __( 'Seems that the file is different from the exported info bar zip. Please try with another zip file.', 'smile' ),
				)
			);
		}

		$style_settings = (array) $obj['style_settings'];

		if ( isset( $obj['variants'] ) ) {
			foreach ( $obj['variants'] as $key => $value ) {
				$variant_analytics = maybe_unserialize( $value['style_settings'] );
				if ( ! empty( $variant_analytics['analytics'] ) ) {
					$analytics_value = $variant_analytics['analytics'];
					$style_id        = $variant_analytics['variant_style_id'];
					smile_update_custom_conversions( $analytics_value, $style_id );
				}
			}
		}

		if ( isset( $style_settings['analytics'] ) && ! empty( $style_settings['analytics'] ) ) {
			$analytics_value = $style_settings['analytics'];
			$style_id        = $style_settings['style_id'];
			smile_update_custom_conversions( $analytics_value, $style_id );
		}

		if ( isset( $obj['media']['infobar_image'] ) ) {
			$old_ib_image = $obj['media']['infobar_image'];
			unset( $obj['media']['infobar_image'] );
			$obj['media']['info_bar_image'] = $old_ib_image;
		}

		if ( isset( $obj['media']['infobar_bg_image'] ) ) {
			$old_ib_bg_image = $obj['media']['infobar_bg_image'];
			unset( $obj['media']['infobar_bg_image'] );
			$obj['media']['info_bar_bg_image'] = $old_ib_bg_image;
		}

		if ( isset( $obj['media'] ) ) {
			$media = (array) $obj['media'];
		}

		if ( isset( $style_settings['cp_google_fonts'] ) ) {
			$google_fonts = explode( ',', $style_settings['cp_google_fonts'] );
			cp_import_google_fonts( $google_fonts );
		}

		$media_ids = array();

		if ( isset( $media ) && is_array( $media ) ) {

			// Import media if any.
			foreach ( $media as $option => $value ) {

				$value = str_replace( $title, $new_folder_name, $value );

				// $filename should be the path to a file in the upload directory.
				$filename = $paths['tempdir'] . '/' . $value;

				// Check the type of file. We'll use this as the 'post_mime_type'.
				$filetype = wp_check_filetype( basename( $filename ), null );

				// Get the path to the upload directory.
				$wp_upload_dir = wp_upload_dir();

				// Prepare an array of post data for the attachment.
				$attachment = array(
					'guid'           => $wp_upload_dir['url'] . '/' . basename( $filename ),
					'post_mime_type' => $filetype['type'],
					'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
					'post_content'   => '',
					'post_status'    => 'inherit',
				);

				// Insert the attachment.
				$option               = ( 'close_image' === $option ) ? 'close_img' : $option;
				$media_ids[ $option ] = wp_insert_attachment( $attachment, $filename );

				// Make sure that this file is included, as wp_generate_attachment_metadata(). depends on it.
				require_once ABSPATH . 'wp-admin/includes/image.php';

				// Generate the metadata for the attachment, and update the database record.
				$attach_data = wp_generate_attachment_metadata( $media_ids[ $option ], $filename );
				wp_update_attachment_metadata( $media_ids[ $option ], $attach_data );

				// Get the attachment id and update the setting for media in style.
				if ( isset( $style_settings[ $option ] ) ) {
					$media_image = $style_settings[ $option ];
					$media_image = str_replace( '%7C', '|', $media_image );
					if ( false !== strpos( $media_image, 'http' ) ) {
						$media_image = explode( '|', $media_image );
						$media_image = $media_image[1];
					} else {
						$media_image = explode( '|', $media_image );
						$media_image = $media_image[1];
					}
					$media_image               = $media_ids[ $option ] . '|' . $media_image;
					$style_settings[ $option ] = $media_image;
				}
			}
		}

		$prev_styles   = get_option( $data_option );
		$variant_tests = get_option( $variant_option );

		$prev_styles = empty( $prev_styles ) ? array() : $prev_styles;

		$update = false;

		foreach ( $style_settings as $title => $value ) {

			if ( 'infobar_bg_image' === $title ) {
				$title = 'info_bar_bg_image';
			}

			if ( 'infobar_image' === $title ) {
				$title = 'info_bar_image';
			}

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
		$import                   = $obj;
		$import['style_settings'] = maybe_serialize( $import_style );

		if ( isset( $import['variants'] ) ) {
			unset( $import['variants'] );
		}

		if ( ! empty( $prev_styles ) ) {
			foreach ( $prev_styles as $key => $style ) {
				$style_id = $style['style_id'];
				if ( $new_style_id == $style_id ) {
					$update = false;
					wp_send_json(
						array(
							'status'      => 'error',
							'description' => __( 'Style Already Exists! Please try importing another style.', 'smile' ),
						)
					);
				} else {
					$update = true;
				}
			}
		} else {
			$update = true;
		}

		if ( $update ) {
			array_push( $prev_styles, $import );
			$status = update_option( $data_option, $prev_styles );

			// Import variants.
			if ( isset( $obj['variants'] ) ) {
				$variant_tests[ $new_style_id ] = $obj['variants'];
				$status                         = update_option( $variant_option, $variant_tests );
			}
		} else {
			$status = false;
		}

		// Check the status of import and return the object accordingly.
		if ( $status ) {
			wp_send_json(
				array(
					'status'      => 'success',
					'description' => ucwords( str_replace( '_', ' ', $style_name ) ) . ' ' . __( 'imported successfully!', 'smile' ),
				)
			);
		} else {
			wp_send_json(
				array(
					'status'      => 'error',
					'description' => __( 'Something went wrong! Please try again with different file.', 'smile' ),
				)
			);

		}
	}
}

if ( ! function_exists( 'cp_import_slide_in' ) ) {
	/**
	 * Function Name: cp_import_slide_in.
	 */
	function cp_import_slide_in() {

		if ( ! current_user_can( 'access_cp' ) ) {
			die( -1 );
		}

		check_admin_referer( 'cp_import_module_nonce', 'security_nonce' );

		$file     = $_POST['file'];
		$title    = sanitize_title( $_POST['file']['title'] );
		$filename = sanitize_file_name( $_POST['file']['filename'] );
		$file     = realpath( get_attached_file( intval( $_POST['file']['id'] ) ) );

		// Get the name of the directory inside the exported zip.
		$zip = new ZipArchive();
		$res = $zip->open( $file );
		if ( ! is_bool( $res ) ) {
			/* translators: %s: zip error code */
			echo sprintf( esc_html__( 'Failed to Open. Error Code: %s ', 'smile' ), esc_attr( $res ) );
			die();
		}

		// Set the path variable for extracting the zip.
		$paths             = array();
		$paths             = wp_upload_dir();
		$paths['export']   = 'cp_export';
		$paths['tempdir']  = trailingslashit( $paths['basedir'] ) . 'cp_modal';
		$paths['temp']     = trailingslashit( $paths['basedir'] ) . 'cp_modal/' . $title;
		$paths['tempurl']  = trailingslashit( $paths['baseurl'] ) . 'cp_modal/';
		$paths['basepath'] = $paths['basedir'] . '/cp_modal/';
		$folder_path       = $paths['basedir'] . '/cp_modal/' . $title;

		// Create the respective directory inside wp-uploads directory.
		if ( ! is_dir( $paths['temp'] ) ) {
			$tempdir = smile_backend_create_folder( $paths['temp'], false );
		}

		WP_Filesystem();
		$destination_path = $paths['tempdir'];

		// Extract the zip to our newly created directory.
		$unzipfile = unzip_file( $file, $destination_path );

		if ( ! $unzipfile ) {
			die( esc_html__( 'Unable to extract the file.', 'smile' ) );
		}

		// Sanitize folder name.
		$new_folder_name = sanitize_file_name( $title );

		// Grant permission.
		chmod( $folder_path, 0755 );

		$new_folder_path = $paths['basepath'] . $new_folder_name;

		// Rename folder.
		rename( $folder_path, $new_folder_path );

		// Rename settings file.
		rename( $new_folder_path . '/' . $title . '.txt', $new_folder_path . '/' . $new_folder_name . '.txt' );

		// Set the json file file url to get the settings for the style.
		$json_file = $paths['tempurl'] . $new_folder_name . '/' . $new_folder_name . '.txt';

		$data_option    = 'smile_slide_in_styles';
		$variant_option = 'slide_in_variant_tests';

		// Read the text file containing the json formatted settings of style and decode it.
		$content = wp_remote_get( $json_file );

		$json         = $content['body'];
		$obj          = json_decode( $json, true );
		$import_style = array();
		$new_style_id = $obj['style_id'];
		$cp_module    = $obj['module'];
		$style_name   = $obj['style_name'];

		if ( 'slide_in' !== $cp_module ) {
			wp_send_json(
				array(
					'status'      => 'error',
					'description' => /* translators:%s module name .*/
					sprintf( __( 'Seems that the file have uploaded the wrong file. This file can be imported for %s ', 'smile' ), str_replace( '_', ' ', $cp_module ) ),
				)
			);
		}

		if ( ! isset( $obj['style_id'] ) ) {
			wp_send_json(
				array(
					'status'      => 'error',
					'description' => __( 'Seems that the file is different from the exported modal zip. Please try with another zip file.', 'smile' ),
				)
			);
		}

		$style_settings = (array) $obj['style_settings'];

		if ( isset( $obj['variants'] ) ) {
			foreach ( $obj['variants'] as $key => $value ) {
				$variant_analytics = maybe_unserialize( $value['style_settings'] );
				if ( ! empty( $variant_analytics['analytics'] ) ) {
					$analytics_value = $variant_analytics['analytics'];
					$style_id        = $variant_analytics['variant_style_id'];
					smile_update_custom_conversions( $analytics_value, $style_id );
				}
			}
		}

		if ( isset( $style_settings['analytics'] ) && ! empty( $style_settings['analytics'] ) ) {
			$analytics_value = $style_settings['analytics'];
			$style_id        = $style_settings['style_id'];
			smile_update_custom_conversions( $analytics_value, $style_id );
		}

		if ( isset( $style_settings['cp_google_fonts'] ) ) {
			$google_fonts = explode( ',', $style_settings['cp_google_fonts'] );
			cp_import_google_fonts( $google_fonts );
		}
		if ( isset( $obj['media']['slidein_bg_image'] ) ) {
			$old_image = $obj['media']['slidein_bg_image'];
			unset( $obj['media']['slidein_bg_image'] );
			$obj['media']['slide_in_bg_image'] = $old_image;
		}

		if ( isset( $obj['media'] ) ) {
			$media     = (array) $obj['media'];
			$media_ids = array();

			if ( isset( $media ) && is_array( $media ) ) {
				// Import media if any.
				foreach ( $media as $option => $value ) {

					$value = str_replace( $title, $new_folder_name, $value );

					// $filename should be the path to a file in the upload directory.
					$filename = $paths['tempdir'] . '/' . $value;

					// Check the type of file. We'll use this as the 'post_mime_type'.
					$filetype = wp_check_filetype( basename( $filename ), null );

					// Get the path to the upload directory.
					$wp_upload_dir = wp_upload_dir();

					// Prepare an array of post data for the attachment.
					$attachment = array(
						'guid'           => $wp_upload_dir['url'] . '/' . basename( $filename ),
						'post_mime_type' => $filetype['type'],
						'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
						'post_content'   => '',
						'post_status'    => 'inherit',
					);

					// Insert the attachment.
					$option               = ( 'close_image' === $option ) ? 'close_img' : $option;
					$media_ids[ $option ] = wp_insert_attachment( $attachment, $filename );

					// Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
					require_once ABSPATH . 'wp-admin/includes/image.php';

					// Generate the metadata for the attachment, and update the database record.
					$attach_data = wp_generate_attachment_metadata( $media_ids[ $option ], $filename );
					wp_update_attachment_metadata( $media_ids[ $option ], $attach_data );

					// Get the attachment id and update the setting for media in style.
					if ( isset( $style_settings[ $option ] ) ) {
						$media_image = $style_settings[ $option ];
						$media_image = str_replace( '%7C', '|', $media_image );
						if ( false !== strpos( $media_image, 'http' ) ) {
							$media_image = explode( '|', $media_image );
							$media_image = $media_image[1];
						} else {
							$media_image = explode( '|', $media_image );
							$media_image = $media_image[1];
						}
						$media_image               = $media_ids[ $option ] . '|' . $media_image;
						$style_settings[ $option ] = $media_image;
					}
				}
			}
		}

		$prev_styles   = get_option( $data_option );
		$variant_tests = get_option( $variant_option );

		$prev_styles = empty( $prev_styles ) ? array() : $prev_styles;
		$update      = false;

		foreach ( $style_settings as $title => $value ) {

			if ( 'slidein_bg_image' === $title ) {
				$title = 'slide_in_bg_image';
			}

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

		$import                   = $obj;
		$import['style_settings'] = maybe_serialize( $import_style );

		if ( isset( $import['variants'] ) ) {
			unset( $import['variants'] );
		}

		if ( ! empty( $prev_styles ) ) {
			foreach ( $prev_styles as $key => $style ) {
				$style_id = $style['style_id'];
				if ( $new_style_id === $style_id ) {
					$update = false;
					wp_send_json(
						array(
							'status'      => 'error',
							'description' => __( 'Style Already Exists! Please try importing another style.', 'smile' ),
						)
					);
				} else {
					$update = true;
				}
			}
		} else {
			$update = true;
		}

		if ( $update ) {
			array_push( $prev_styles, $import );
			$status = update_option( $data_option, $prev_styles );

			// Import variants .
			if ( isset( $obj['variants'] ) ) {
				$variant_tests[ $new_style_id ] = $obj['variants'];
				$status                         = update_option( $variant_option, $variant_tests );
			}
		} else {
			$status = false;
		}

		// Check the status of import and return the object accordingly.
		if ( $status ) {
			wp_send_json(
				array(
					'status'      => 'success',
					'description' => ucwords( str_replace( '_', ' ', $style_name ) ) . ' ' . __( 'imported successfully!', 'smile' ),
				)
			);
		} else {
			wp_send_json(
				array(
					'status'      => 'error',
					'description' => __( 'Something went wrong! Please try again with different file.', 'smile' ),
				)
			);
		}
	}
}


if ( ! function_exists( 'cp_import_google_fonts' ) ) {
	/**
	 * Function Name: cp_import_google_fonts  Add Google fonts while importing.
	 *
	 * @param  array $google_fonts array of fonts.
	 */
	function cp_import_google_fonts( $google_fonts ) {

		$basic_fonts = array(
			'Arial',
			'Arial Black',
			'Comic Sans MS',
			'Courier New',
			'Georgia',
			'Impact',
			'Lucida Sans Unicode',
			'Palatino Linotype',
			'Tahoma',
			'Times New Roman',
			'Trebuchet MS',
			'Verdana',
		);

		$default_google_fonts = array(
			'Lato',
			'Open Sans',
			'Libre Baskerville',
			'Montserrat',
			'Neuton',
			'Raleway',
			'Roboto',
			'Sacramento',
			'Varela Round',
			'Pacifico',
			'Bitter',
		);

		$saved_fonts = get_option( 'cplus_ultimate_selected_google_fonts', array() );

		$gfonts = array();

		if ( is_array( $saved_fonts ) ) {
			foreach ( $saved_fonts as $font ) {
				if ( isset( $font['font_family'] ) ) {
					$font_family_name = $font['font_family'];
					$font_family_name = str_replace( '+', ' ', $font['font_family'] );
					$gfonts[]         = $font_family_name;
				}
			}
		}

		$fonts_to_exclude = array_merge( $basic_fonts, $default_google_fonts, $gfonts );

		foreach ( $google_fonts as $font ) {
			// Make sure font is not already present in array.
			if ( ! in_array( $font, $fonts_to_exclude ) ) {

				if ( '' !== $font ) {
					$font_arr = array(
						'font_family' => $font,
						'font_name'   => $font,
						'variants'    => null,
						'subsets'     => null,
					);

					$saved_fonts[] = $font_arr;
				}
			}
		}

		// Update google font option.
		$saved_fonts = array_filter( $saved_fonts );

		update_option( 'ultimate_selected_google_fonts', $saved_fonts );
	}
}

if ( ! function_exists( 'smile_update_custom_conversions' ) ) {
	/**
	 * Function Name: smile_update_custom_conversions update style conversions.
	 *
	 * @param  array  $analytics_value  Analytics value.
	 * @param  string $style_id  style id.
	 */
	function smile_update_custom_conversions( $analytics_value, $style_id ) {

			$condition     = true;
			$existing_data = get_option( 'smile_style_analytics' );

			global $cp_analytics_end_time;
			$date = $cp_analytics_end_time;

			$analytics_data = array();
			$analytics      = array();

		if ( $condition ) {
			$count = 0;
			foreach ( $analytics_value as $key => $value ) {
				if ( empty( $analytics_data ) && is_array( $value ) ) {
					$old_impressions = $value['impressions'];
					$old_conversions = $value['conversions'];
					$analytics_data  = array(
						$style_id => array(
							$key => array(
								'impressions' => $old_impressions,
								'conversions' => $old_conversions,
							),
						),
					);
				} elseif ( isset( $analytics_data[ $style_id ] ) && is_array( $value ) ) {
					$old_impressions                     = $value['impressions'];
					$old_conversions                     = $value['conversions'];
					$analytics_data[ $style_id ][ $key ] = array(
						'impressions' => $old_impressions,
						'conversions' => $old_conversions + 1,
					);
				}
				if ( ! empty( $existing_data ) ) {
					$analytics = array_merge( $existing_data, $analytics_data );
				} else {
					$analytics = $analytics_data;
				}
			}
		}
		update_option( 'smile_style_analytics', $analytics );

	}
}


