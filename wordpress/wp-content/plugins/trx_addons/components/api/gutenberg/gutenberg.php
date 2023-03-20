<?php
/**
 * Plugin support: Gutenberg
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.0.49
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	die( '-1' );
}

// Check if plugin 'Gutenberg' is installed and activated
// Attention! This function is used in many files and was moved to the api.php
/*
if ( !function_exists( 'trx_addons_exists_gutenberg' ) ) {
	function trx_addons_exists_gutenberg() {
		return function_exists( 'register_block_type' );
	}
}
*/
	
// Merge specific styles into single stylesheet
if ( !function_exists( 'trx_addons_gutenberg_merge_styles' ) ) {
	add_filter("trx_addons_filter_merge_styles", 'trx_addons_gutenberg_merge_styles');
	function trx_addons_gutenberg_merge_styles($list) {
		if (trx_addons_exists_gutenberg()) {
			//$list[] = TRX_ADDONS_PLUGIN_API . 'gutenberg/_gutenberg.scss';
		}
		return $list;
	}
}


// Merge shortcode's specific styles to the single stylesheet (responsive)
if ( !function_exists( 'trx_addons_gutenberg_merge_styles_responsive' ) ) {
	add_filter("trx_addons_filter_merge_styles_responsive", 'trx_addons_gutenberg_merge_styles_responsive');
	function trx_addons_gutenberg_merge_styles_responsive($list) {
		if (trx_addons_exists_gutenberg()) {
			//$list[] = TRX_ADDONS_PLUGIN_API . 'gutenberg/_gutenberg.responsive.scss';
		}
		return $list;
	}
}


// Load required styles and scripts for Backend Editor mode
if ( !function_exists( 'trx_addons_gutenberg_editor_load_scripts' ) ) {
	add_action("enqueue_block_editor_assets", 'trx_addons_gutenberg_editor_load_scripts');
	function trx_addons_gutenberg_editor_load_scripts() {
		trx_addons_load_scripts_admin(true);
		trx_addons_localize_scripts_admin();
		wp_enqueue_style( 'trx_addons', trx_addons_get_file_url(TRX_ADDONS_PLUGIN_API . 'gutenberg/gutenberg-preview.css'), array(), null );
		if (trx_addons_get_setting('allow_gutenberg_blocks')) {
			wp_enqueue_style( 'trx_addons-gutenberg-blocks-editor', trx_addons_get_file_url(TRX_ADDONS_PLUGIN_API . 'gutenberg/blocks/dist/blocks.editor.build.css'), array(), null );
			wp_enqueue_script( 'trx_addons-gutenberg-blocks', trx_addons_get_file_url(TRX_ADDONS_PLUGIN_API . 'gutenberg/blocks/dist/blocks.build.js'), array('jquery'), null, true );
		}
		do_action('trx_addons_action_pagebuilder_admin_scripts');
	}
}

// Load required scripts for both: Backend + Frontend mode
if ( !function_exists( 'trx_addons_gutenberg_preview_load_scripts' ) ) {
	add_action("enqueue_block_assets", 'trx_addons_gutenberg_preview_load_scripts');
	function trx_addons_gutenberg_preview_load_scripts() {
		if (trx_addons_get_setting('allow_gutenberg_blocks')) {
			wp_enqueue_style(  'trx_addons-gutenberg-blocks', trx_addons_get_file_url(TRX_ADDONS_PLUGIN_API . 'gutenberg/blocks/dist/blocks.style.build.css'), array(), null );
		}
		do_action('trx_addons_action_pagebuilder_preview_scripts');
	}
}

// Add shortcode's specific vars to the JS storage
if ( !function_exists( 'trx_addons_gutenberg_localize_script' ) ) {
	add_filter("trx_addons_filter_localize_script", 'trx_addons_gutenberg_localize_script');
	function trx_addons_gutenberg_localize_script($vars) {
		return $vars;
	}
}


// Save CSS with custom colors and fonts to the gutenberg-editor-style.css
if ( ! function_exists( 'trx_addons_gutenberg_save_css' ) ) {
	add_action( 'basekit_action_save_options', 'trx_addons_gutenberg_save_css', 30 );
	add_action( 'trx_addons_action_save_options', 'trx_addons_gutenberg_save_css', 30 );
	function trx_addons_gutenberg_save_css() {

		$msg = '/* ' . esc_html__( "ATTENTION! This file was generated automatically! Don't change it!!!", 'trx_addons' )
				. "\n----------------------------------------------------------------------- */\n";

		// Get main styles
		$css = trx_addons_fgc( trx_addons_get_file_dir( 'css/trx_addons.css' ) );

		// Add context class to each selector
		$css = trx_addons_css_add_context($css, '.edit-post-visual-editor');

		// Save styles to the file
		trx_addons_fpc( trx_addons_get_file_dir( TRX_ADDONS_PLUGIN_API . 'gutenberg/gutenberg-preview.css' ), $msg . $css );
	}
}


//------------------------------------------------------------
//-- Compatibility Gutenberg and other PageBuilders
//-------------------------------------------------------------

// Prevent simultaneous editing of posts for Gutenberg and other PageBuilders (VC, Elementor)
if ( ! function_exists( 'trx_addons_gutenberg_disable_cpt' ) ) {
	add_action( 'current_screen', 'trx_addons_gutenberg_disable_cpt' );
	function trx_addons_gutenberg_disable_cpt() {
		$safe_pb = trx_addons_get_setting( 'gutenberg_safe_mode' );
		if ( !empty($safe_pb) && trx_addons_exists_gutenberg() ) {
			$current_post_type = get_current_screen()->post_type;
			$disable = false;
			if ( !$disable && in_array('elementor', $safe_pb) && trx_addons_exists_elementor() ) {
				$post_types = get_post_types_by_support( 'elementor' );
				$disable = is_array($post_types) && in_array($current_post_type, $post_types);
			}
			if ( !$disable && in_array('vc', $safe_pb) && trx_addons_exists_vc() ) {
				$post_types = function_exists('vc_editor_post_types') ? vc_editor_post_types() : array();
				$disable = is_array($post_types) && in_array($current_post_type, $post_types);
			}
			if ( $disable ) {
				remove_filter( 'replace_editor', 'gutenberg_init' );
				remove_action( 'load-post.php', 'gutenberg_intercept_edit_post' );
				remove_action( 'load-post-new.php', 'gutenberg_intercept_post_new' );
				remove_action( 'admin_init', 'gutenberg_add_edit_link_filters' );
				remove_filter( 'admin_url', 'gutenberg_modify_add_new_button_url' );
				remove_action( 'admin_print_scripts-edit.php', 'gutenberg_replace_default_add_new_button' );
				remove_action( 'admin_enqueue_scripts', 'gutenberg_editor_scripts_and_styles' );
				remove_filter( 'screen_options_show_screen', '__return_false' );
			}
		}
	}
}
