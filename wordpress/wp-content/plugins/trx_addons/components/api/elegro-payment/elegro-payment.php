<?php

// Check if plugin installed and activated
if ( !function_exists( 'trx_addons_exists_elegro_payment' ) ) {
	function trx_addons_exists_elegro_payment() {
		return class_exists( 'WC_Elegro_Payment' );
	}
}

// Add our ref to the link
if ( !function_exists( 'trx_addons_elegro_payment_add_ref' ) ) {
	add_filter( 'woocommerce_settings_api_form_fields_elegro', 'trx_addons_elegro_payment_add_ref' );
	function trx_addons_elegro_payment_add_ref( $fields ) {
		if ( ! empty( $fields['listen_url']['description'] ) ) {
			$fields['listen_url']['description'] = preg_replace( '/href="([^"]+)"/', 'href="$1?ref=246218d7-a23d-444d-83c5-a884ecfa4ebd"', $fields['listen_url']['description'] );
		}
		return $fields;
	}
}


if ( !function_exists( 'trx_addons_elegro_payment_filter_export_options' ) ) {
    add_filter( 'trx_addons_filter_export_options', 'trx_addons_elegro_payment_filter_export_options' );
    function trx_addons_elegro_payment_filter_export_options( $options ) {
        if (isset($options['woocommerce_elegro_settings'])) {
            unset($options['woocommerce_elegro_settings']);
        }
        return $options;
    }
}
?>