<?php
namespace FileBird;

defined( 'ABSPATH' ) || exit;
/**
 * I18n Logic
 */
class I18n {
	protected static $instance = null;

	public static function getInstance() {
		if ( null == self::$instance ) {
			self::$instance = new self();
			self::$instance->doHooks();
		}

		return self::$instance;
	}

	private function __construct() {
	}

	private function doHooks() {
		add_action( 'plugins_loaded', array( $this, 'loadPluginTextdomain' ) );
	}

	public static function loadPluginTextdomain() {
		if ( function_exists( 'determine_locale' ) ) {
			$locale = determine_locale();
		} else {
			$locale = is_admin() ? get_user_locale() : get_locale();
		}
		unload_textdomain( 'filebird' );
		load_textdomain( 'filebird', NJFB_PLUGIN_PATH . '/i18n/languages/' . $locale . '.mo' );
		load_plugin_textdomain( 'filebird', false, NJFB_PLUGIN_PATH . '/i18n/languages/' );
	}

	public static function getTranslation() {
		$escapeLink = array(
			'a' => array(
				'href'   => array(),
				'target' => array(),
			),
		);

		$translation = array(
			'noMedia'                         => esc_html__( 'No media files found.', 'filebird' ),
			'new_folder'                      => esc_html__( 'New Folder', 'filebird' ),
			'delete'                          => esc_html__( 'Delete', 'filebird' ),
			'folders'                         => esc_html__( 'Folders', 'filebird' ),
			'ok'                              => esc_html__( 'Ok', 'filebird' ),
			'cancel'                          => esc_html__( 'Cancel', 'filebird' ),
			'cut'                             => esc_html__( 'Cut', 'filebird' ),
			'paste'                           => esc_html__( 'Paste', 'filebird' ),
			'download'                        => esc_html__( 'Download', 'filebird' ),
			'download_pro_version'            => esc_html__( 'Download (Pro version)', 'filebird' ),
			'loading'                         => esc_html__( 'Loading', 'filebird' ),
			'generate_download'               => esc_html__( 'Generating download link...', 'filebird' ),
			'move_done'                       => esc_html__( 'Successfully moved', 'filebird' ),
			'move_error'                      => esc_html__( 'Unsuccessfully moved', 'filebird' ),
			'delete_done'                     => esc_html__( 'Successfully deleted!', 'filebird' ),
			'save'                            => esc_html__( 'Save', 'filebird' ),
			'folder'                          => esc_html__( 'Folder', 'filebird' ),
			'folder_name_placeholder'         => esc_html__( 'Folder name...', 'filebird' ),
			'folders'                         => esc_html__( 'Folders', 'filebird' ),
			'enter_folder_name_placeholder'   => esc_html__( 'Enter folder name...', 'filebird' ),
			'are_you_sure_delete'             => esc_html__( 'Are you sure you want to delete', 'filebird' ),
			'are_you_sure'                    => esc_html__( 'Are you sure?', 'filebird' ),
			'editing_warning'                 => esc_html__( 'You are editing another folder! Please complete the task first!', 'filebird' ),
			'sort_folders'                    => esc_html__( 'Sort Folders', 'filebird' ),
			'delete_folder'                   => esc_html__( 'Delete Folder', 'filebird' ),
			'sort_files'                      => esc_html__( 'Sort Files', 'filebird' ),
			'bulk_select'                     => esc_html__( 'Bulk Select', 'filebird' ),
			'all_files'                       => esc_html__( 'All Files', 'filebird' ),
			'uncategorized'                   => esc_html__( 'Uncategorized', 'filebird' ),
			'previous_folder_selected'        => esc_html__( 'Previous folder selected', 'filebird' ),
			'rename'                          => esc_html__( 'Rename', 'filebird' ),
			'sort_ascending'                  => esc_html__( 'Sort Ascending', 'filebird' ),
			'sort_descending'                 => esc_html__( 'Sort Descending', 'filebird' ),
			'reset'                           => esc_html__( 'Reset', 'filebird' ),
			'by_name'                         => esc_html__( 'By Name', 'filebird' ),
			'name_ascending'                  => esc_html__( 'Name Ascending', 'filebird' ),
			'name_descending'                 => esc_html__( 'Name Descending', 'filebird' ),
			'by_date'                         => esc_html__( 'By Date', 'filebird' ),
			'date_ascending'                  => esc_html__( 'Date Ascending', 'filebird' ),
			'date_descending'                 => esc_html__( 'Date Descending', 'filebird' ),
			'by_modified'                     => esc_html__( 'By Modified', 'filebird' ),
			'modified_ascending'              => esc_html__( 'Modified Ascending', 'filebird' ),
			'modified_descending'             => esc_html__( 'Modified Descending', 'filebird' ),
			'by_author'                       => esc_html__( 'By Author', 'filebird' ),
			'by_file_name'                    => esc_html__( 'By File Name', 'filebird' ),
			'author_ascending'                => esc_html__( 'Author Ascending', 'filebird' ),
			'author_descending'               => esc_html__( 'Author Descending', 'filebird' ),
			'by_title'                        => esc_html__( 'By Title', 'filebird' ),
			'title_ascending'                 => esc_html__( 'Title Ascending', 'filebird' ),
			'title_descending'                => esc_html__( 'Title Descending', 'filebird' ),
			'deactivate'                      => esc_html__( 'Deactivate', 'filebird' ),
			'thank_you_so_much'               => esc_html__( 'Thank you so much!', 'filebird' ),
			'all_files_will_move'             => wp_kses( __( 'Those files will be moved to <strong>Uncategorized</strong> folder.', 'filebird' ), array( 'strong' => array() ) ),
			'skip_and_deactivate'             => sprintf( esc_html__( 'Skip %s Deactivate', 'filebird' ), '&' ),
			'delete_error'                    => sprintf( esc_html__( "Can%st delete!", 'filebird' ), '\'' ),
			'are_you_sure_delete_this_folder' => wp_kses( __( 'Are you sure you want to delete this folder? Those files will be moved to <strong>Uncategorized</strong> folder.', 'filebird' ), array( 'strong' => array() ) ),
			'feedback'                        => array(
				'no_features'            => sprintf( esc_html__( 'It doesn%st have the features I%sm looking for.', 'filebird' ), '\'', '\'' ),
				'not_working'            => esc_html__( 'Not work with my theme or other plugins.', 'filebird' ),
				'found_better_plugin'    => esc_html__( 'Found another plugin that works better.', 'filebird' ),
				'not_know_using'         => sprintf( esc_html__( 'Don%st know how to use it.', 'filebird' ), '\'' ),
				'temporary_deactivation' => esc_html__( 'This is just temporary, I will use it again.', 'filebird' ),
				'other'                  => esc_html__( 'Other', 'filebird' ),
			),
			'which_features'                  => esc_html__( 'Which features please?', 'filebird' ),
			'found_better_plugin_placeholder' => esc_html__( 'Please tell us which one', 'filebird' ),
			'not_know_using_document'         => wp_kses(
				__( 'Please read FileBird documentation <a target="_blank" href="https://ninjateam.gitbook.io/filebird/">here</a> or <a target="_blank" href="https://ninjateam.org/support/">chat with us</a> if you need help', 'filebird' ),
				$escapeLink
			),
			'not_working_support'             => wp_kses( __( 'Please <a target="_blank" href="https://ninjateam.org/support/">ask for support here</a>, we will fix it for you.', 'filebird' ), $escapeLink ),
			'other_placeholder'               => esc_html__( 'Please share your thoughts...', 'filebird' ),
			'quick_feedback'                  => esc_html__( 'Want a better FileBird?', 'filebird' ),
			'deactivate_sadly'                => esc_html__( 'Sorry to see you walk away, please share why you want to deactivate FileBird?', 'filebird' ),
			'folder_limit_reached'            => esc_html__( 'Folder Limit Reached', 'filebird' ),
			'pagebuilder_support'             => esc_html__( 'Including Divi, Fusion, Thrive Architect, WPBakery...', 'filebird' ),
			'upgrade_to_pro'                  => esc_html__( 'Upgrade to FileBird Pro now', 'filebird' ),
			'success'                         => esc_html__( 'Success.', 'filebird' ),
			'filebird_db_updated'             => esc_html__( 'Congratulations. Successfully imported!', 'filebird' ),
			'go_to_media'                     => esc_html__( 'Go To Media', 'filebird' ),
			'update_noti_title'               => esc_html__( 'FileBird 4 Update Required', 'filebird' ),
			'update_noti_desc'                => sprintf(esc_html__( 'You%sre using the new FileBird 4. Please import database to view your folders correctly.', 'filebird' ), '\''),
			'update_noti_btn'                 => esc_html__( 'Import now', 'filebird' ),
			'import_failed'                   => wp_kses( __( 'Import failed. Please try again or <a href="https://ninjateam.org/support" target="_blank">contact our support</a>.', 'filebird' ), $escapeLink ),
			'purchase_code_missing'           => esc_html__( 'Please enter your Purchase Code.', 'filebird' ),
			'envato_token_missing'            => esc_html__( 'Please enter your Personal Access Token or get one.', 'filebird' ),
			'envato_invalid_license'          => esc_html__( 'Can not active your License, please try again.', 'filebird' ),
			'settings'                        => esc_html__( 'Settings', 'filebird' ),
			'fb_settings'                     => esc_html__( 'FileBird Settings', 'filebird' ),
			'select_default_startup_folder'   => esc_html__( 'Select a default startup folder:', 'filebird' ),
			'auto_sort_files_by'              => esc_html__( 'Auto sort files by:', 'filebird' ),
			'default'                         => esc_html__( 'Default', 'filebird' ),
			'set_setting_success'             => esc_html__( 'Settings saved', 'filebird' ),
			'set_setting_fail'                => esc_html__( 'Failed to save settings. Please try again!', 'filebird' ),
			'unlock_new_features_title'       => esc_html__( 'Unlock new features', 'filebird' ),
			'unlock_new_features_desc'        => esc_html__( 'To use FileBird folders with your current page builder/plugin, please upgrade to PRO version.', 'filebird' ),
			'do_more_with_filebird_title'     => esc_html__( 'Do more with FileBird PRO', 'filebird' ),
			'do_more_with_filebird_desc'      => sprintf( esc_html__( 'You%sre using a third party plugin, which is supported in FileBird PRO. Please upgrade to browse files faster and get more done.', 'filebird' ), '\'' ),
			'go_pro'                          => esc_html__( 'Go Pro', 'filebird' ),
			'view_details'                    => esc_html__( 'View details.', 'filebird' ),
			'turn_off_for_7_days'             => esc_html__( 'Turn off for 7 days', 'filebird' ),
			'collapse'                        => esc_html__( 'Collapse', 'filebird' ),
			'expand'                          => esc_html__( 'Expand', 'filebird' ),
			'uploaded'                        => esc_html__( 'Uploaded', 'filebird' ),
			'lessThanAMin'                    => esc_html__( 'Less than a min', 'filebird' ),
			'totalSize'                       => esc_html__( 'Total size', 'filebird' ),
			'move'                            => esc_html__( 'Move', 'filebird' ),
			'item'                            => esc_html__( 'item', 'filebird' ),
			'items'                           => esc_html__( 'items', 'filebird' ),
			'import_folder_to_filebird'       => esc_html__( 'Import folders to FileBird', 'filebird' ),
			'go_to_import'                    => esc_html__( 'Go to import', 'filebird' ),
			'no_thanks'                       => esc_html__( 'No, thanks', 'filebird' ),
			'import_some_folders'             => esc_html__( 'You have some folders created by other media plugins. Would you like to import them?', 'filebird' ),
			'default_tree_view'               => esc_html__( 'Default Tree View', 'filebird' ),
			'flat_tree_view'                  => esc_html__( 'Flat Tree View', 'filebird' ),
		);
		return $translation;
	}
}
