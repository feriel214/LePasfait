<?php
/**
 * Wrapper for MaxMind GeoLite2 Reader
 *
 * This class provide an interface to handle geolocation and error handling.
 *
 * Requires PHP 5.4+.
 *
 * @package Convert Pro\Classes
 * @since   3.4.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Geolite integration class.
 */
class Convert_Plug_CP_Geolite_Integration_Target {

	/**
	 * MaxMind GeoLite2 database path.
	 *
	 * @var string
	 */
	private $database = '';

	/**
	 * Logger instance.
	 *
	 * @var CP_Logger
	 */
	private $log = null;

	/**
	 * Constructor.
	 *
	 * @param string $database MaxMind GeoLite2 database path.
	 */
	public function __construct( $database ) {
		$this->database = $database;

		if ( ! class_exists( 'CPlus\\MaxMind\\Db\\Reader', false ) ) {
			$this->require_geolite_library();
		}
	}

	/**
	 * Get country 2-letters ISO by IP address.
	 * Retuns empty string when not able to find any ISO code.
	 *
	 * @param string $ip_address User IP address.
	 * @return string
	 */
	public function get_country_iso( $ip_address ) {
		$iso_code = '';
		$error    = false;
		try {
			$reader   = new CPlus\MaxMind\Db\Reader( $this->database ); // phpcs:ignore PHPCompatibility.PHP.NewLanguageConstructs.t_ns_separatorFound
			$data     = $reader->get( $ip_address );
			$iso_code = isset( $data['country']['iso_code'] ) ? $data['country']['iso_code'] : '';

			$reader->close();
		} catch ( Exception $e ) {
			$error = true;
		}

		return sanitize_text_field( strtoupper( $iso_code ) );
	}

	/**
	 * Logging method.
	 *
	 * @param string $message Log message.
	 * @param string $level   Log level.
	 *                        Available options: 'emergency', 'alert',
	 *                        'critical', 'error', 'warning', 'notice',
	 *                        'info' and 'debug'.
	 *                        Defaults to 'info'.
	 */
	private function log( $message, $level = 'info' ) {
		$error = false;
		if ( ! is_null( $this->log ) ) {
			$error = true;
			// Log in future if required.
		}
	}

	/**
	 * Require geolite library.
	 */
	private function require_geolite_library() {
		if ( version_compare( PHP_VERSION, '5.4.0', '>=' ) ) {
			require_once CP_BASE_DIR . '/framework/lib/geolite2/Reader.php';
			require_once CP_BASE_DIR . '/framework/lib/geolite2/Reader/Decoder.php';
			require_once CP_BASE_DIR . '/framework/lib/geolite2/Reader/InvalidDatabaseException.php';
			require_once CP_BASE_DIR . '/framework/lib/geolite2/Reader/Metadata.php';
			require_once CP_BASE_DIR . '/framework/lib/geolite2/Reader/Util.php';
		}
	}
}
