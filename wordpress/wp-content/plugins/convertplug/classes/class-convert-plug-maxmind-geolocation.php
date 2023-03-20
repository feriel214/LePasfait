<?php
/**
 * MaxMind Geolocation Integration
 *
 * @version 3.9.0
 * @package ConvertPlus
 */

defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

/**
 * Class Convert_Plug_Maxmind_Geolocation.
 */
class Convert_Plug_Maxmind_Geolocation {

	/**
	 * The name of the MaxMind database to utilize.
	 */
	const DATABASE = 'GeoLite2-Country';

	/**
	 * The extension for the MaxMind database.
	 */
	const DATABASE_EXTENSION = '.mmdb';

	/**
	 * A prefix for the MaxMind database filename.
	 *
	 * @var string
	 */
	private $database_prefix = '';

	/**
	 * Convert_Plug_Maxmind_Geolocation constructor.
	 */
	public function __construct() {
		// Generate a prefix to store it in the integration as it would expect it.
		if ( ! get_option( 'convertplus_maxmind_geolocation_settings' ) ) {
			$this->database_prefix = wp_generate_password( 32, false );
			update_option(
				'convertplus_maxmind_geolocation_settings',
				array(
					'database_prefix' => $this->database_prefix,
					'license_key'     => '',
				)
			);
		}
	}

	/**
	 * Fetches the path that the database should be stored.
	 *
	 * @return string The local database path.
	 */
	public function get_cplus_database_path() {
		$uploads_dir = wp_upload_dir();

		$database_path         = trailingslashit( $uploads_dir['basedir'] ) . 'convertplus_uploads/';
		$this->database_prefix = get_option( 'convertplus_maxmind_geolocation_settings' );
		$this->database_prefix = $this->database_prefix['database_prefix'];

		if ( ! empty( $this->database_prefix ) ) {
			$database_path .= $this->database_prefix . '-';
		}
		$database_path .= self::DATABASE . self::DATABASE_EXTENSION;
		/**
		 * Filter the geolocation database storage path.
		 *
		 * @param string $database_path The path to the database.
		 * @param int $version Deprecated since 3.4.0.
		 * @deprecated 3.9.0
		 */
		$database_path = apply_filters_deprecated(
			'convert_plus_geolocation_local_database_path',
			array( $database_path, 2 ),
			'3.9.0',
			'convert_plus_maxmind_geolocation_database_path'
		);

		/**
		 * Filter the geolocation database storage path.
		 *
		 * @since 3.9.0
		 * @param string $database_path The path to the database.
		 */
		return apply_filters( 'convert_plus_maxmind_geolocation_database_path', $database_path );
	}

	/**
	 * Verify license key and download the Geolite2 database.
	 *
	 * @param string $license_key The license key to be used when downloading the database.
	 * @return array The path to the database file or an error if invalid.
	 */
	public function verify_key_and_download_database( $license_key ) {

		$response = array(
			'error'   => false,
			'message' => 'Your settings have been saved.',
		);

		// Empty license keys have no need test downloading a database.
		if ( empty( $license_key ) ) {
			return array(
				'error'   => true,
				'message' => 'MaxMind License Key is required!',
			);
		}

		// Check the license key by attempting to download the Geolocation database.
		$result = $this->download_database( $license_key );
		if ( true === $result['error'] ) {
			return array(
				'error'   => true,
				'message' => $result['message'],
			);
		}

		// We may as well put this archive to good use, now that we've downloaded one.
		self::update_database( $result['message'] );

			$update_geolite2_license_key                = get_option( 'convertplus_maxmind_geolocation_settings' );
			$update_geolite2_license_key['license_key'] = $license_key;
			update_option( 'convertplus_maxmind_geolocation_settings', $update_geolite2_license_key );

		return $response;
	}

	/**
	 * Fetches the database from the MaxMind service.
	 *
	 * @param string $license_key The license key to be used when downloading the database.
	 * @return array The path to the database file or an error if invalid.
	 */
	public function download_database( $license_key ) {
		$download_uri = add_query_arg(
			array(
				'edition_id'  => self::DATABASE,
				'license_key' => sanitize_text_field( $license_key ),
				'suffix'      => 'tar.gz',
			),
			'https://download.maxmind.com/app/geoip_download'
		);

		// Needed for the download_url call right below.
		require_once ABSPATH . 'wp-admin/includes/file.php';

		$tmp_archive_path = download_url( $download_uri );
		if ( is_wp_error( $tmp_archive_path ) ) {
			// Transform the error into something more informative.
			$error_data = $tmp_archive_path->get_error_data();
			if ( isset( $error_data['code'] ) && 401 === $error_data['code'] ) {
				$response = array(
					'error'   => true,
					'message' => __( 'The MaxMind license key is invalid. If you have recently created this key, you may need to wait for it to become active.', 'smile' ),
				);
			} else {
				$response = array(
					'error'   => true,
					'message' => __( 'Failed to download the MaxMind database.', 'smile' ),
				);
			}
			return $response;
		}

		// Extract the database from the archive.
		try {
			$file = new PharData( $tmp_archive_path );

			$tmp_database_path = trailingslashit( dirname( $tmp_archive_path ) ) . trailingslashit( $file->current()->getFilename() ) . self::DATABASE . self::DATABASE_EXTENSION;

			$file->extractTo(
				dirname( $tmp_archive_path ),
				trailingslashit( $file->current()->getFilename() ) . self::DATABASE . self::DATABASE_EXTENSION,
				true
			);
		} catch ( Exception $exception ) {
			return array(
				'error'   => true,
				'message' => $exception->getMessage(),
			);
		}
		// Remove the archive since we only care about a single file in it.
		unlink( $tmp_archive_path );

		return array(
			'error'   => false,
			'message' => $tmp_database_path,
		);
	}

	/**
	 * Updates the database used for geolocation queries.
	 *
	 * @param string|null $new_database_path The path to the new database file. Null will fetch a new archive.
	 */
	public function update_database( $new_database_path = null ) {
		// Allow us to easily interact with the filesystem.
		require_once ABSPATH . 'wp-admin/includes/file.php';
		WP_Filesystem();
		global $wp_filesystem;

		// Remove any existing archives to comply with the MaxMind TOS.
		$target_database_path = $this->get_cplus_database_path();

		// If there's no database path, we can't store the database.
		if ( empty( $target_database_path ) ) {
			return;
		}

		if ( $wp_filesystem->exists( $target_database_path ) ) {
			$wp_filesystem->delete( $target_database_path );
		}

		if ( isset( $new_database_path ) ) {
			$tmp_database_path = $new_database_path;
		}

		// Move the new database into position.
		$wp_filesystem->move( $tmp_database_path, $target_database_path, true );
		$wp_filesystem->delete( dirname( $tmp_database_path ) );
	}
}
