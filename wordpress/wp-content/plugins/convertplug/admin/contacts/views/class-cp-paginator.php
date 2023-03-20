<?php
/**
 * Prohibit direct script loading.
 *
 * @package Convert_Plus.
 */

/**
 * Class CP_Paginator.
 */
class CP_Paginator {
	/**
	 * $_conn variable.
	 *
	 * @var string.
	 */
	private $conn;

	/**
	 * $_limit variable.
	 *
	 * @var string.
	 */
	private $limit;

	/**
	 * $campaign_page  no of pages dispaly.
	 *
	 * @var string
	 */
	private $campaign_page;

	/**
	 * $contacts description.
	 *
	 * @var array.
	 */
	private $contacts;

	/**
	 * $_total var total.
	 *
	 * @var integer.
	 */
	private $total;
	/**
	 * Constructor.
	 *
	 * @param mixed $contacts description.
	 */
	public function __construct( $contacts ) {

		$this->contacts = $contacts;
		$this->_total   = count( $contacts );
	}

	/**
	 * Function returns data to display.
	 * *
	 *
	 * @param  string $intial_object           Function returns data to display parameter.
	 * @param  string $next_object             Function returns data to display parameter.
	 * @return array                           result array.
	 */
	public function verify_type( $intial_object, $next_object ) {

		if ( isset( $intial_object[ $this->orderby ] ) && isset( $next_object[ $this->orderby ] ) ) {

			// If type of data is integer.
			if ( 'integer' === gettype( $intial_object[ $this->orderby ] ) ) {
					return $this->cp_int_cmp( $intial_object[ $this->orderby ], $next_object[ $this->orderby ] );
			}

			if ( 'date' === $this->orderby ) {
				return strcmp( strtotime( $intial_object[ $this->orderby ] ), strtotime( $next_object[ $this->orderby ] ) );
			} else {
				return strcmp( strtolower( $intial_object[ $this->orderby ] ), strtolower( $next_object[ $this->orderby ] ) );
			}
		}
	}


	/**
	 * Function returns data to display.
	 *
	 * @param  integer $limit          limit.
	 * @param  integer $campaign_page            page no.
	 * @param  string  $dashboard_orderby        Function returns data to display parameter.
	 * @param  string  $dashboard_order          Function returns data to display parameter.
	 * @param  string  $sq     Function returns data to display parameter.
	 * @param  string  $serach_in_params Function returns data to display parameter.
	 * @param  string  $maintain_keys  Function returns data to display parameter.
	 * @return array                  result array.
	 */
	public function get_data( $limit, $campaign_page, $dashboard_orderby, $dashboard_order, $sq, $serach_in_params, $maintain_keys ) {
		$this->serach_in_params = '';
		$this->_limit           = $limit;
		$this->_page            = $campaign_page;
		$this->_offset          = ( ( $this->_page - 1 ) * $this->_limit );
		$this->_upto            = $this->_offset + $this->_limit;
		$this->orderby          = $dashboard_orderby;
		$this->order            = $dashboard_order;
		$this->serach_in_params = $serach_in_params;
		$this->maintainkeys     = $maintain_keys;

		$data = $this->contacts;
		if ( '' !== $sq ) {

			$data = array_filter(
				$data,
				function ( $item ) use ( $sq ) {

					$found = false;

					foreach ( $this->serach_in_params as $param ) { //phpcs:ignore PHPCompatibility.FunctionDeclarations.NewClosure.ThisFound

						if ( array_key_exists( $param, $item ) ) {
							if ( false !== stripos( strtolower( urldecode( $item[ $param ] ) ), $sq ) ) {
								$found = true;
							}
						}
					}
					if ( $found ) {
						return true;
					}
					return false;
				}
			);

			$this->_total = count( $data );
		}

		if ( $this->order ) {

			if ( ! $this->maintainkeys ) {
				$data = array_values( $data );
			}

			if ( 'asc' === $this->order ) {
				uasort(
					$data,
					array( $this, 'verify_type' )
				);
			} else {
				uasort(
					$data,
					array( $this, 'verify_type' )
				);

				$data = array_reverse( $data );
			}

			$data = array_slice( $data, $this->_offset, $this->_limit, true );

		} else {
			$data = array_slice( $data, $this->_offset, $this->_limit, true );
		}

		$result       = new stdClass();
		$result->data = $data;

		return $result;
	}

	/**
	 * Function compare two integers.
	 *
	 * @param  integer $a integer val.
	 * @param  integer $b integer val.
	 * @return integer    integer val.
	 */
	public function cp_int_cmp( $a, $b ) {
		return ( $a - $b ) ? ( $a - $b ) / abs( $a - $b ) : 0;
	}

	/**
	 * Function create links for pagination.
	 *
	 * @param  string $links          string parameter.
	 * @param  string $list_class     string parameter.
	 * @param  string $list_id         string parameter.
	 * @param  string $sq             string parameter.
	 * @param  string $base_page_link string parameter.
	 * @return string                 string parameter.
	 */
	public function create_links( $links, $list_class, $list_id, $sq, $base_page_link ) {

		if ( isset( $_REQUEST['cp_admin_page_nonce'] ) && ! wp_verify_nonce( $_REQUEST['cp_admin_page_nonce'], 'cp_admin_page' ) ) {
			wp_die( 'No direct script access allowed!' );
		}

		if ( 'all' === $this->_limit ) {
			return '';
		}

		if ( '' !== $list_id ) {
			$base_page_link .= '&list=' . $list_id;
		}

		$url_link = '';
		if ( isset( $_GET['orderby'] ) ) {
			$url_link .= '&orderby=' . esc_attr( $_GET['orderby'] );
		}

		if ( isset( $_GET['order'] ) ) {
			$url_link .= '&order=' . esc_attr( $_GET['order'] );
		}

		$last = ceil( $this->_total / $this->_limit );

		$start = ( 0 < ( $this->_page - $links ) ) ? $this->_page - $links : 1;
		$end   = ( ( $this->_page + $links ) < $last ) ? $this->_page + $links : $last;

		$html = '<ul class="' . $list_class . '">';

		$class           = ( 1 === $this->_page ) ? 'disabled' : '';
		$prev_page_link  = ( 1 === $this->_page ) ? 'javascript:void(0)' : $base_page_link . '&limit=' . $this->_limit . '&sq=' . $sq . '&cont-page=' . ( $this->_page - 1 ) . $url_link;
		$first_page_link = $base_page_link . '&limit=' . $this->_limit . '&sq=' . $sq . '&cont-page=1' . $url_link;
		$html           .= '<li class="' . $class . '"><a href="' . $first_page_link . '"><span class="connects-icon-rewind"></span></a></li>';
		$html           .= '<li class="' . $class . '"><a href="' . $prev_page_link . '"><span class="dashicons dashicons-arrow-left-alt2"></span></a></li>';

		if ( 1 < $this->_page ) {
			$start = $this->_page - 1;
		} else {
			$start = 1;
		}

		for ( $i = $start; $i <= $end; $i++ ) {
			$class = ( $this->_page === $i ) ? 'active' : '';
			$html .= '<li class="' . $class . '"><a href="' . $base_page_link . '&limit=' . $this->_limit . '&sq=' . $sq . '&cont-page=' . $i . $url_link . '">' . $i . '</a></li>';
		}

		if ( $end < $last ) {
			$html .= '<li class="disabled"><span>...</span></li>';
			$html .= '<li><a href="' . $base_page_link . '&limit=' . $this->_limit . '&sq=' . $sq . '&cont-page=' . $last . $url_link . '">' . $last . '</a></li>';
		}

		$class          = ( $this->_page === $last ) ? 'disabled' : '';
		$next_page_link = ( $this->_page === $last ) ? 'javascript:void(0)' : $base_page_link . '&limit=' . $this->_limit . '&sq=' . $sq . '&cont-page=' . ( $this->_page + 1 ) . $url_link;
		$last_page_link = ( $this->_page === $last ) ? 'javascript:void(0)' : $base_page_link . '&limit=' . $this->_limit . '&sq=' . $sq . '&cont-page=' . ( $last ) . $url_link;
		$html          .= '<li class="' . $class . '"><a href="' . $next_page_link . '"><span class="dashicons dashicons-arrow-right-alt2"></span></a></li>';
		$html          .= '<li class="' . $class . '"><a href="' . $last_page_link . '"><span class="connects-icon-fast-forward"></span></a></li>';

		$html .= '</ul>';

		return $html;
	}
}
