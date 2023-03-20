<?php

namespace FileBird\Controller;

use FileBird\Controller\Convert as ConvertController;
use FileBird\Classes\Convert as ConvertModel;
use FileBird\Controller\Exclude;

use FileBird\Model\Folder as FolderModel;
use FileBird\Classes\Helpers as Helpers;
use FileBird\Classes\Tree;

use FileBird\I18n as I18n;

defined('ABSPATH') || exit;

/**
 * Folder Controller
 */

class Folder extends Controller
{

  protected static $instance = null;

  public static function getInstance()
  {
    if (null == self::$instance) {
      self::$instance = new self;
      self::$instance->doHooks();
    }
    return self::$instance;
  }

  public function __construct()
  {
  }

  private function doHooks()
  {
    Exclude::getInstance();

		add_filter( 'media_library_infinite_scrolling', '__return_true' );
    
    add_action('admin_enqueue_scripts', array($this, 'enqueueAdminScripts'));

    add_action('rest_api_init', array($this, 'registerRestFields'));

    add_action('add_attachment', array($this, 'addAttachment'));
    add_action('delete_attachment', array($this, 'deleteAttachment'));

    add_filter('ajax_query_attachments_args', array($this, 'ajaxQueryAttachmentsArgs'), 20);
    add_filter('mla_media_modal_query_final_terms', array($this, 'ajaxQueryAttachmentsArgs'), 20);
    
    add_filter('restrict_manage_posts', array($this, 'restrictManagePosts'));
    add_filter('posts_clauses', array($this, 'postsClauses'), 10, 2);
    add_action('pre-upload-ui', array($this, 'actionPluploadUi'));
    add_action('wp_ajax_fbv_first_folder_notice', array($this, 'ajax_first_folder_notice'));
    add_action('wp_ajax_fbv_download_folder', array($this, 'ajaxDownloadFolder'));
    add_action('admin_notices', array($this, 'adminNotices'));
    add_action('attachment_fields_to_edit', array($this, 'attachment_fields_to_edit'), 10, 2);
    add_filter('attachment_fields_to_save', array($this, 'attachment_fields_to_save'), 10, 2);
    // MailPoet plugin support
    add_filter('mailpoet_conflict_resolver_whitelist_script', array($this, 'mailpoet_conflict_resolver_whitelist_script'), 10, 1);
    add_filter('mailpoet_conflict_resolver_whitelist_style', array($this, 'mailpoet_conflict_resolver_whitelist_style'), 10, 1);
  }

  public function mailpoet_conflict_resolver_whitelist_script($scripts){
    $scripts[] = 'filebird';
    $scripts[] = 'filebird-pro';
    return $scripts;
  }

  public function mailpoet_conflict_resolver_whitelist_style($styles){
    $styles[] = 'filebird';
    $styles[] = 'filebird-pro';
    return $styles;
  }

  public function adminNotices()
  {
    global $pagenow;
    //welcome to new filebird message
    $notShownInPages = array('upload.php');
    $optionFirstFolder = get_option('fbv_first_folder_notice');
    if (
      (int)FolderModel::countFolder() == 0
      && !in_array($pagenow, $notShownInPages)
      && ($optionFirstFolder === false || time() >= (int)$optionFirstFolder)
    ) {
?>
      <div class="notice notice-info is-dismissible" id="filebird-empty-folder-notice">
        <p>
          <?php esc_html_e('Create your first folder for media library now.', 'filebird') ?>
          <a href="<?php echo esc_url(admin_url('/upload.php')) ?>">
            <strong><?php esc_html_e('Get Started', 'filebird') ?></strong>
          </a>
        </p>
      </div>
      <?php
    }
    //import from old folders message
    $is_converted = get_option('fbv_old_data_updated_to_v4', '0');
    if ($is_converted !== '1') {
      $old_folder_count = count(ConvertController::getOldFolers());
      $style = '';
      if ($pagenow === 'upload.php') $style = 'display: none';
      if ($pagenow === 'options-general.php') {
        if (
          isset($_GET['page']) && isset($_GET['tab']) &&
          sanitize_text_field($_GET['page']) === 'filebird-settings' &&
          sanitize_text_field($_GET['tab']) === 'update-db'
        ) $style = 'display: none';
      }
      if ($old_folder_count > 0 && !isset($_GET['autorun'])) {
      ?>
        <div style="<?php echo esc_attr($style) ?>" class="notice notice-warning is-dismissible njt-fb-update-db-noti" id="njt-fb-update-db-noti">
          <div class="njt-fb-update-db-noti-item">
            <h3><?php esc_html_e('FileBird 4 Update Required', 'filebird'); ?></h3>
          </div>
          <div class="njt-fb-update-db-noti-item">
            <p>
              <?php esc_html_e('You are using the new FileBird 4. Please update database to view your folders correctly.', 'filebird'); ?>
            </p>
          </div>
          <div class="njt-fb-update-db-noti-item">
            <p>
              <a class="button button-primary" href="<?php echo esc_url(add_query_arg(array('page' => 'filebird-settings', 'tab' => 'tools', 'autorun' => 'true'), admin_url('/options-general.php'))); ?>">
                <strong><?php esc_html_e('Update now', 'filebird') ?></strong>
              </a>
            </p>
          </div>
        </div>
<?php
      }
    }
  }

  public function ajax_first_folder_notice()
  {
    check_ajax_referer('fbv_nonce', 'nonce', true);
    update_option('fbv_first_folder_notice', time() + 30 * 60 * 60 * 24); //After 3 months show
    wp_send_json_success();
  }

  public function registerRestFields()
  {
    register_rest_route(
      NJFB_REST_URL,
      'get-folders',
      array(
        'methods' => 'GET',
        'callback' => array($this, 'ajaxGetFolder'),
        'permission_callback' => array($this, 'resPermissionsCheck'),
      )
    );
    register_rest_route(
      NJFB_REST_URL,
      'gutenberg-get-folders',
      array(
        'methods' => 'GET',
        'callback' => array($this, 'ajaxGutenbergGetFolder'),
        'permission_callback' => array($this, 'resPermissionsCheck'),
      )
    );

    register_rest_route(
      NJFB_REST_URL,
      'new-folder',
      array(
        'methods' => 'POST',
        'callback' => array($this, 'ajaxNewFolder'),
        'permission_callback' => array($this, 'resPermissionsCheck'),
      )
    );
    register_rest_route(
      NJFB_REST_URL,
      'update-folder',
      array(
        'methods' => 'POST',
        'callback' => array($this, 'ajaxUpdateFolder'),
        'permission_callback' => array($this, 'resPermissionsCheck'),
      )
    );
    register_rest_route(
      NJFB_REST_URL,
      'update-folder-ord',
      array(
        'methods' => 'POST',
        'callback' => array($this, 'ajaxUpdateFolderOrd'),
        'permission_callback' => array($this, 'resPermissionsCheck'),
      )
    );
    register_rest_route(
      NJFB_REST_URL,
      'delete-folder',
      array(
        'methods' => 'POST',
        'callback' => array($this, 'ajaxDeleteFolder'),
        'permission_callback' => array($this, 'resPermissionsCheck'),
      )
    );
    register_rest_route(
      NJFB_REST_URL,
      'set-folder-attachments',
      array(
        'methods' => 'POST',
        'callback' => array($this, 'ajaxSetFolder'),
        'permission_callback' => array($this, 'resPermissionsCheck'),
      )
    );
    register_rest_route(
      NJFB_REST_URL,
      'update-tree',
      array(
        'methods' => 'POST',
        'callback' => array($this, 'ajaxUpdateTree'),
        'permission_callback' => array($this, 'resPermissionsCheck'),
      )
    );
    register_rest_route(
      NJFB_REST_URL,
      'get-relations',
      array(
        'methods' => 'POST',
        'callback' => array($this, 'ajaxGetRelations'),
        'permission_callback' => array($this, 'resPermissionsCheck'),
      )
    );
    register_rest_route(
      NJFB_REST_URL,
      'set-settings',
      array(
        'methods' => 'POST',
        'callback' => array($this, 'ajaxSetSettings'),
        'permission_callback' => array($this, 'resPermissionsCheck'),
      )
    );

    register_rest_route(
      NJFB_REST_URL,
      'export-csv',
      array(
        'methods' => 'GET',
        'callback' => array($this, 'exportCSV'),
        'permission_callback' => array($this, 'resPermissionsCheck'),
      )
    );

    register_rest_route(
      NJFB_REST_URL,
      'import-csv',
      array(
        'methods' => 'POST',
        'callback' => array($this, 'importCSV'),
        'permission_callback' => array($this, 'resPermissionsCheck'),
      )
    );
  }
  public function resPermissionsCheck()
  {
    return current_user_can('upload_files');
  }

  public function enqueueAdminScripts($screenId)
  {
    if (function_exists('get_current_screen')) {
      if ($screenId == "upload.php") {
        wp_register_script('jquery-resizable', NJFB_PLUGIN_URL . 'assets/js/jquery-resizable.min.js');
        wp_enqueue_script('jquery-resizable');
      }
    }

    if ($screenId !== 'pagebuilders') {
      wp_enqueue_script('fbv-import', NJFB_PLUGIN_URL . 'assets/js/import.js', array('jquery'), NJFB_VERSION, false);
      wp_enqueue_script('fbv-active', NJFB_PLUGIN_URL . 'assets/js/active.js', array('jquery'), NJFB_VERSION, false);
      wp_enqueue_style('fbv-active', NJFB_PLUGIN_URL . 'assets/css/active.css', array(), NJFB_VERSION);
    }

    if ($screenId === 'settings_page_filebird-settings') {
      wp_enqueue_script('toastr', NJFB_PLUGIN_URL . 'assets/js/toastr/toastr.min.js', array(), '2.1.3', false);
      wp_enqueue_style('toastr', NJFB_PLUGIN_URL . 'assets/js/toastr/toastr.min.css', array(), '2.1.3');
    }

    wp_enqueue_script('jquery-ui-draggable');
    wp_enqueue_script('jquery-ui-droppable');

    if ( wp_is_mobile() ) {
      wp_enqueue_script('jquery-touch-punch-fixed', NJFB_PLUGIN_URL . 'assets/js/jquery.ui.touch-punch.js', array('jquery-ui-widget', 'jquery-ui-mouse'), NJFB_VERSION, false);
    }

    wp_enqueue_script('fbv-folder', NJFB_PLUGIN_URL . 'assets/dist/js/app.js', array(), NJFB_VERSION, false);
    wp_enqueue_script('fbv-lib', NJFB_PLUGIN_URL . 'assets/js/jstree/jstree.min.js', array(), NJFB_VERSION, false);

    wp_enqueue_style('fbv-folder', NJFB_PLUGIN_URL . 'assets/dist/css/app.css', array(), NJFB_VERSION);
    wp_style_add_data('fbv-folder', 'rtl', 'replace');
    wp_localize_script('fbv-folder', 'fbv_data', apply_filters('fbv_data', array(
      'nonce' => wp_create_nonce('fbv_nonce'),
      'rest_nonce' => wp_create_nonce('wp_rest'),
      'nonce_error' => esc_html__('Your request cannot be processed.', 'filebird'),
      'current_folder' => ((isset($_GET['fbv'])) ? (int)sanitize_text_field($_GET['fbv']) : -1), //-1: all files. 0: uncategorized
      'default_folder' => Helpers::getDefaultSelectedFolder(),
      'default_sort_files' => Helpers::getDefaultSortFiles(),
      'folders' => FolderModel::allFolders('id as term_id, name as term_name', array('term_id', 'term_name')),
      'relations' => FolderModel::getRelations(),
      'is_upload_screen' => "upload.php" === $screenId ? '1' : '0',
      'i18n' => i18n::getTranslation(),
      'media_mode' => get_user_option('media_library_mode', get_current_user_id()),
      'json_url' => apply_filters('filebird_json_url', rtrim(rest_url(NJFB_REST_URL), "/")),
      'media_url' => admin_url('upload.php'),
      'auto_import_url' => esc_url(add_query_arg(array('page' => 'filebird-settings', 'tab' => 'tools', 'autorun' => 'true'), admin_url('/options-general.php'))),
      'pll_lang'=> apply_filters('fbv_pll_lang',''),
      'is_new_user' => get_option('fbv_is_new_user', false),
      'sort_folder' => get_option('njt_fb_sort_folder', 'reset'),
      'import_other_plugins' => ConvertModel::getInstance()->get_plugin3rd_folders_to_import(),
      'import_other_plugins_url' => esc_url(
        add_query_arg(
          array(
            'page' => 'filebird-settings',
            'tab'  => 'import',
          ),
          admin_url( 'options-general.php' )
        )
      )
    )));
  }

  public function restrictManagePosts()
  {
    $screen = get_current_screen();
    if ($screen->id == "upload") {
      $fbv = ((isset($_GET['fbv'])) ? (int)sanitize_text_field($_GET['fbv']) : -1);
      $folders = FolderModel::allFolders();

      $all = new \stdClass();
      $all->id = -1;
      $all->name = esc_html__('All Folders', 'filebird');

      $uncategorized = new \stdClass();
      $uncategorized->id = 0;
      $uncategorized->name = esc_html__('Uncategorized', 'filebird');

      array_unshift($folders, $all, $uncategorized);
      echo '<select name="fbv" id="filter-by-fbv" class="fbv-filter attachment-filters fbv">';
      foreach ($folders as $k => $folder) {
        echo sprintf('<option value="%1$d" %3$s>%2$s</option>', $folder->id, $folder->name, selected($folder->id, $fbv, false));
      }
      echo '</select>';
    }
  }
  public function postsClauses($clauses, $query)
  {
    global $wpdb;
    if ($query->get("post_type") !== "attachment") {
      return $clauses;
    }

    if (Helpers::isListMode() && !isset($_GET['fbv'])) {
      return $clauses;
    }

    $fbvPropery = $query->get('fbv');
    if ( isset($_GET['fbv']) || $fbvPropery !== '') {
      $fbv = isset($_GET['fbv']) ? (int)sanitize_text_field($_GET['fbv']) : (int)$fbvPropery;
      $table_name = $wpdb->prefix . 'fbv_attachment_folder';
      
      if ($fbv === -1) {
        return $clauses;
      } else if ($fbv === 0) {
        $clauses = FolderModel::getRelationsWithFolderUser($clauses);
      } else {
        $clauses['join'] .= $wpdb->prepare(" LEFT JOIN {$table_name} AS fbva ON fbva.attachment_id = {$wpdb->posts}.ID AND fbva.folder_id = %d ", $fbv);
        $clauses['where'] .= " AND fbva.folder_id IS NOT NULL";
      }
    }
    return $clauses;
  }
  public function addAttachment($post_id)
  {
    $fbv = ((isset($_REQUEST['fbv'])) ? sanitize_text_field($_REQUEST['fbv']) : '');
    if ($fbv != '') {
      if (is_numeric($fbv)) {
        $parent = $fbv;
      } else {
        $fbv = explode('/', ltrim(rtrim($fbv, '/'), '/'));
        $parent = (int)$fbv[0];
        if ($parent < 0) $parent = 0; //important
        unset($fbv[0]);
        foreach ($fbv as $k => $v) {
          $parent = FolderModel::newOrGet($v, $parent);
        }
      }
      FolderModel::setFoldersForPosts($post_id, $parent);
    }
  }
  public function deleteAttachment($post_id)
  {
    FolderModel::deleteFoldersOfPost($post_id);
  }

  public function ajaxQueryAttachmentsArgs($query)
  {
    if (isset($_REQUEST['query']['fbv'])) {
      $fbv = $_REQUEST['query']['fbv'];
      if (is_array($fbv)) {
        $fbv = array_map('intval', $fbv);
      } else {
        $fbv = intval($fbv);
      }
      $query['fbv'] = $fbv;
    }
    return $query;
  }
  public function ajaxGetFolder()
  {
    $order_by = null;
    $sort_option = 'reset';
    
    $lang = null;
    $icl_lang = isset($_GET['icl_lang']) ? sanitize_text_field($_GET['icl_lang']) : null;
    $pll_lang = isset($_GET['pll_lang']) ? sanitize_text_field($_GET['pll_lang']) : null;

    if (!is_null($icl_lang)) $lang = $icl_lang;
    if (!is_null($pll_lang)) $lang = $pll_lang;

    if (isset($_GET['sort']) && \in_array(sanitize_text_field($_GET['sort']), array('name_asc', 'name_desc', 'reset'))) {
      if (sanitize_text_field($_GET['sort']) == 'name_asc') {
        $order_by = 'CAST(name as unsigned), name ASC';
        $sort_option = sanitize_text_field($_GET['sort']);
      } elseif (sanitize_text_field($_GET['sort']) == 'name_desc') {
        $order_by = 'CAST(name as unsigned) DESC, name DESC';
        $sort_option = sanitize_text_field($_GET['sort']);
      }
      update_option('njt_fb_sort_folder', $sort_option);
    } else {
      $njt_fb_sort_folder = get_option('njt_fb_sort_folder', 'reset');
      if ($njt_fb_sort_folder == 'reset') {
        $order_by = null;
      } elseif ($njt_fb_sort_folder == 'name_asc') {
        $order_by = 'name asc';
      } elseif ($njt_fb_sort_folder == 'name_desc') {
        $order_by = 'name desc';
      }
    }

    $tree = Tree::getFolders($order_by, false);

    wp_send_json_success(array(
      'tree' => $tree,
      'folder_count' => array(
        'total' => Tree::getCount(-1, $lang),
        'folders' => Tree::getAllFoldersAndCount($lang)
      )
    ));
  }
  public function ajaxGutenbergGetFolder()
  {
    $_folders = Tree::getFolders(null, true, 0, true);
    $folders = array(
      array(
        'value' => 0,
        'label' => esc_html__('Please choose folder', 'filebird'),
        'disabled' => true
      )
    );
    foreach ($_folders as $k => $v) {
      $folders[] = array(
        'value' => $v['id'],
        'label' => $v['text']
      );
    }

    wp_send_json_success($folders);
  }
  public function ajaxNewFolder($request)
  {
    $name = $request->get_param('name');
    $parent = $request->get_param('parent');
    $name = isset($name) ? sanitize_text_field(wp_unslash($name)) : '';
    $parent = isset($parent) ? sanitize_text_field($parent) : '';
    $id = null;
    if ($name != '' && $parent != '') {
      $insert = FolderModel::newOrGet($name, $parent, false);
      if ($insert !== false) {
        wp_send_json_success(array('id' => $insert));
      } else {
        wp_send_json_error(array('mess' => esc_html__('A folder with this name already exists. Please choose another one.', 'filebird')));
      }
    } else {
      wp_send_json_error(array(
        'mess' => esc_html__('Validation failed', 'filebird')
      ));
    }
  }
  public function ajaxUpdateFolder($request)
  {
    $id = $request->get_param('id');
    $parent = $request->get_param('parent');
    $name = $request->get_param('name');

    $id = isset($id) ? sanitize_text_field($id) : '';
    $parent = isset($parent) ? intval(sanitize_text_field($parent)) : '';
    $name = isset($name) ? sanitize_text_field(wp_unslash($name)) : '';
    if (is_numeric($id) && is_numeric($parent) && $name != '') {
      $update = FolderModel::updateFolderName($name, $parent, $id);
      if ($update === true) {
        wp_send_json_success();
      } else {
        wp_send_json_error(array('mess' => esc_html__('A folder with this name already exists. Please choose another one.', 'filebird')));
      }
    }
    wp_send_json_error();
  }
  public function ajaxUpdateFolderOrd($request)
  {
    $id = $request->get_param('id');
    $parent = $request->get_param('parent');
    $ord = $request->get_param('ord');

    $id = isset($id) ? sanitize_text_field($id) : '';
    $parent = isset($parent) ? sanitize_text_field(wp_unslash($parent)) : '';
    $ord = isset($ord) ? sanitize_text_field(wp_unslash($ord)) : '';
    if (is_numeric($id) && is_numeric($parent) && is_numeric($ord)) {
      FolderModel::updateOrdAndParent($id, $ord, $parent);
      wp_send_json_success();
    }
    wp_send_json_error();
  }
  public function ajaxDeleteFolder($request)
  {
    $ids = $request->get_param('ids');
    $ids = isset($ids) ? Helpers::sanitize_array($ids) : '';
    if ($ids != '') {
      if (!is_array($ids)) $ids = array($ids);
      $ids = array_map('intval', $ids);

      foreach ($ids as $k => $v) {
        if ($v > 0) FolderModel::deleteFolderAndItsChildren($v);
      }
      wp_send_json_success();
    }
    wp_send_json_error(array(
      'mess' => esc_html__('Cannot delete folder, please try again later', 'filebird')
    ));
  }
  public function ajaxSetFolder($request)
  {
    $ids = $request->get_param('ids');
    $folder = $request->get_param('folder');

    $ids = isset($ids) ? Helpers::sanitize_array($ids) : '';
    $folder = isset($folder) ? sanitize_text_field($folder) : '';
    if ($ids != '' && is_array($ids) && is_numeric($folder)) {
      FolderModel::setFoldersForPosts($ids, $folder);
      wp_send_json_success();
    }
    wp_send_json_error(array(
      'mess' => esc_html__('Validation failed', 'filebird')
    ));
  }
  public function ajaxUpdateTree($request)
  {
    $tree = $request->get_param('tree');

    $tree = isset($tree) ? sanitize_text_field($tree) : '';
    if ($tree != '') {
      $tree = preg_replace('#[^0-9,()]#', '', $tree);
      FolderModel::rawInsert('(id, ord, parent) VALUES ' . $tree . ' ON DUPLICATE KEY UPDATE ord=VALUES(ord),parent=VALUES(parent)');
      wp_send_json_success(array(
        'mess' => esc_html__('Folder tree has been updated.', 'filebird')
      ));
    }
    wp_send_json_error(array(
      'mess' => esc_html__('Validation failed', 'filebird')
    ));
  }
  public function ajaxGetRelations()
  {
    wp_send_json_success(array(
      'relations' => FolderModel::getRelations()
    ));
  }
  public function ajaxSetSettings($request)
  {
    $folder_id = $request->get_param('folder_id');
    $sortSetting = $request->get_param('sortSetting');

    $folder_id = isset($folder_id) ? intval($folder_id) : -1;
    $sortSetting = isset($sortSetting) ? sanitize_text_field($sortSetting) : 'default';
    Helpers::setDefaultSelectedFolder($folder_id);
    Helpers::setDefaultSortFiles($sortSetting);
    wp_send_json_success();
  }
  public function ajaxDownloadFolder()
  {
    check_ajax_referer('fbv_nonce', 'nonce', true);
    try {
      $root_folder = NJFB_UPLOAD_DIR;
      $upload_folder = WP_CONTENT_DIR . DIRECTORY_SEPARATOR . $root_folder . DIRECTORY_SEPARATOR;

      @mkdir($upload_folder, 755);

      $folder_id = ((isset($_GET['folder_id'])) ? intval($_GET['folder_id']) : 0);
      if ($folder_id > 0) {
        $folder = FolderModel::getChildrenOfFolder($folder_id, 0);

        $tmp_dir = get_temp_dir();

        $zip = new \ZipArchive;

        $zipname = sanitize_title($folder->name) . '-' . uniqid() . '-' . time() . '.zip';
        $zip->open($upload_folder . $zipname, \ZipArchive::CREATE);
        $attachment_ids = Helpers::getAttachmentIdsByFolderId($folder_id);
        foreach ($attachment_ids as $k => $id) {
          $file = get_attached_file($id);
          if ($file) {
            $zip->addFile($file, \basename($file));
          }
        }

        self::addFolderToZip($zip, $folder->children, '');
        $zip->close();
        
        wp_send_json_success(array(
          'link' => content_url() . '/' . $root_folder . '/' . $zipname
        ));
      }
    } catch (\Exception $ex) {
      wp_send_json_error(array(
        'errorCode'    => $ex->getCode(),
        'errorMessage' => $ex->getMessage()
      ));
    } catch (\Error $ex) {
      wp_send_json_error(array(
        'errorCode'    => $ex->getCode(),
        'errorMessage' => $ex->getMessage()
      ));
    }
  }

  public function attachment_fields_to_edit($form_fields, $post){
    $screen = null;
    if (function_exists('get_current_screen'))
    {
      $screen = get_current_screen();

      if( ! is_null($screen) && 'attachment' === $screen->id )
        return $form_fields;
    }

    $fbv_folder = FolderModel::getFolderFromPostId($post->ID);
    $fbv_folder = count($fbv_folder) > 0 ? $fbv_folder[0] : (object) array(
      'folder_id' => 0,
      'name' => esc_html__( 'Uncategorized', 'filebird' ),
    );
    $form_fields['fbv'] = array(
      'html'  => "<div class='fbv-attachment-edit-wrapper' data-folder-id='{$fbv_folder->folder_id}' data-attachment-id='{$post->ID}'><input readonly type='text' value='{$fbv_folder->name}'/></div>",
      'label' => esc_html__( 'FileBird folder:',  'filebird'),
      'helps' => esc_html__( "Click on the button to move this file to another folder", "filebird" ),
      'input' => 'html'
    );
   
    return $form_fields;
  }

  public function attachment_fields_to_save($post, $attachment){
    if (isset($attachment['fbv'])){
      FolderModel::setFoldersForPosts($post['ID'], $attachment['fbv']);
    }
    return $post;
  }

  private static function addFolderToZip(&$zip, $children, $parent_dir = '')
  {
    foreach ($children as $k => $v) {
      $folder_name = $v->name;
      $folder_id = $v->id;

      $folder_name = sanitize_title($folder_name);

      $attachment_ids = Helpers::getAttachmentIdsByFolderId($folder_id);
      $empty_dir = $parent_dir != '' ? $parent_dir . '/' . $folder_name : $folder_name;
      $zip->addEmptyDir($empty_dir);

      foreach ($attachment_ids as $k => $id) {
        $file = get_attached_file($id);
        if ($file) {
          $zip->addFile($file, $empty_dir . '/' . \basename($file));
        }
      }
      if (\is_array($v->children)) {
        self::addFolderToZip($zip, $v->children, $empty_dir);
      }
    }
  }
  public function actionPluploadUi()
  {
    global $pagenow;
    $folders = FolderModel::allFolders();
    $default = array(
      array(
        'title' => esc_html__('Uncategorized', 'filebird'),
        'value' => '0'
      )
    );
    $data = array(
      'tree' => $this->getFlatTree($folders, 0, $default)
    );
    $this->loadView('particle/folder_dropdown', $data);
  }
  private function _buildQuery($tree, $parent)
  {
    $results = array();
    $ord = 0;
    foreach ($tree as $k => $v) {
      $results[] = sprintf('(%1$d, %2$d, %3$d)', $v['id'], $ord, $parent);
      if (isset($v['children']) && is_array($v['children']) && count($v['children']) > 0) {
        $children = $this->_buildQuery($v['children'], $v['id']);
        foreach ($children as $k2 => $v2) {
          $results[] = $v2;
        }
      }
      $ord++;
    }
    return $results;
  }

  public function getFlatTree($data, $parent = 0, $default = null, $level = 0)
  {
    $tree = is_null($default) ? array() : $default;
    foreach ($data as $k => $v) {
      if ($v->parent == $parent) {
        $node = array(
          'title' => str_repeat('-', $level) .  $v->name,
          'value' => $v->id,
        );
        $tree[] = $node;
        $children = $this->getFlatTree($data, $v->id, null, $level + 1);
        foreach ($children as $k2 => $child) {
          $tree[] = $child;
        }
      }
    }
    return $tree;
  }

  public function exportCSV(){
    global $wpdb;

    $query = "SELECT * FROM {$wpdb->prefix}fbv";

    $folders = $wpdb->get_results($query, ARRAY_A);

    return new \WP_REST_Response( array( 'folders' => $folders ) );
  }

  public function restoreFolderStructure($folders){
    global $wpdb;
    $currentUserId = get_current_user_id();
    try {
      foreach ( $folders as $k => $folder ) {
        $new_parent = $folder['parent'];
        if ( intval($new_parent) > 0 ) {
          $new_parent = get_option( 'njt_new_term_id_' . $new_parent );
        }
        $data = array(
          'name' => sanitize_text_field($folder['name']),
          'parent' => intval($new_parent),
          'type' => 0,
          'ord' => intval($folder['ord']),
          'created_by' => get_option('njt_fbv_folder_per_user', '0') === '1' ? $currentUserId : 0
        );
  
        $table = "{$wpdb->prefix}fbv";
        $inserted = $wpdb->insert($table, $data);
        update_option( 'njt_new_term_id_' . $folder['id'], $wpdb->insert_id );
      }
      return true;
    } catch (\Throwable $th) {
      return false;
    }
  }

  public function importCSV(\WP_REST_Request $request){
    $params  = $request->get_file_params();
		$handle  = \fopen( $params['file']['tmp_name'], 'r' );
		$data    = array();
		$columns = array();
		if ( false !== $handle ) {
			$count = 1;
			while ( 1 ) {
				$row = fgetcsv( $handle, 0 );
				if ( 1 === $count ) {
					$columns = $row;
					$count++;
					continue;
				}
				if ( false === $row ) {
					break;
				}
				foreach ( $columns as $key => $col ) {
					$tmp[ $col ] = $row[ $key ];
				}
				$data[] = $tmp;
			}
		}
		\fclose( $handle );

    $check = array_diff($columns, array(
      "id",
      "name",
      "parent",
      "type",
      "ord",
      "created_by",
    ));

    if (count($check) > 0) {
		  return new \WP_REST_Response( array( 
        'success' => false, 
        'message' => esc_html__("The uploaded file was not generated by FileBird. Please check again.", 'filebird')
      ) );
    }

		$result = $this->restoreFolderStructure( $data );

		return new \WP_REST_Response( array( 'success' => $result ) );
  }
}
