<?php

/**
 * The settings of the plugin.
 *
 * @link       https://www.3cx.com
 * @since      10.0.0
 *
 * @package    wplc_Plugin
 * @subpackage wplc_Plugin/admin
 */

/**
 * Class WordPress_Plugin_Template_Settings
 *
 */
class wplc_Admin_Settings
{

  /**
   * The ID of this plugin.
   *
   * @since    10.0.0
   * @access   private
   * @var      string    $plugin_name    The ID of this plugin.
   */
  private $plugin_name;

  /**
   * The version of this plugin.
   *
   * @since    10.0.0
   * @access   private
   * @var      string    $version    The current version of this plugin.
   */
  private $version;

  /**
   * Initialize the class and set its properties.
   *
   * @since    10.0.0
   * @param      string    $plugin_name       The name of this plugin.
   * @param      string    $version    The version of this plugin.
   */
  public function __construct($plugin_name, $version)
  {

    $this->plugin_name = $plugin_name;
    $this->version = $version;
  }

  /**
   * This function introduces the theme options into the 'Appearance' menu and into a top-level
   * '3CX Live Chat' menu.
   */
  public function setup_plugin_options_menu()
  {

    //Add the menu to the Plugins set of menu items
    add_menu_page(
      '3CX Live Chat',
      '3CX Live Chat',
      'manage_options',
      'wplc_options',
      array($this, 'render_settings_page_content'),
      'dashicons-format-chat'
    );
  }

  public function read_config()
  {
    return $this->get_display_options();
  }

  /**
   * Provides default values for the Display Options.
   *
   * @return array
   */
  private function get_display_options()
  {
    $defaults = array(
      'callus_url' => '',
      'show_all_pages' => '',
      'include_pages' => '',
      'powered_by' => ''
    );
    $temp_options = get_option('wplc_display_options');
    // force defaults when there is no value
    foreach ($defaults as $k => $v) {
      if (!isset($temp_options[$k])) {
        $temp_options[$k] = $v;
      }
    }
    $options = array();
    // remove values without a default
    foreach ($temp_options as $k => $v) {
      if (isset($defaults[$k])) {
        $options[$k] = $v;
      }
    }
    return $options;
  }

  /**
   * Renders settings page
   */
  public function render_settings_page_content($active_tab = '')
  {
?>
    <!-- Create a header in the default WordPress 'wrap' container -->
    <div class="wrap">

      <h2><?php _e('3CX Live Chat Options', 'wp-live-chat-support'); ?></h2>
      <?php $active_tab = 'display_options'; settings_errors(); ?>

      <form method="post" action="options.php">
        <?php

        if ($active_tab == 'display_options') {

          settings_fields('wplc_display_options');
          do_settings_sections('wplc_display_options');
        }

        submit_button();

        ?>
      </form>

    </div><!-- /.wrap -->
<?php
  }


  /**
   * This function provides a simple description for the General Options page.
   *
   * It's called from the 'wplc_initialize_theme_options' function by being passed as a parameter
   * in the add_settings_section function.
   */
  public function general_options_callback()
  {
  } // end general_options_callback

  /**
   * Initializes the theme's display options page by registering the Sections,
   * Fields, and Settings.
   *
   * This function is registered with the 'admin_init' hook.
   */
  public function initialize_display_options()
  {

    add_settings_section(
      'general_settings_section',                  // ID used to identify this section and with which to register options
      '',            // Title to be displayed on the administration page
      array($this, 'general_options_callback'),      // Callback used to render the description of the section
      'wplc_display_options'                    // Page on which to add this section of options
    );

    // Next, we'll introduce the fields for toggling the visibility of content elements.

    add_settings_field(
      'pbx_config',
      __('Get 3CX', 'wp-live-chat-support'),
      array($this, 'pbx_config_callback'),
      'wplc_display_options',
      'general_settings_section',
      array()
    );
    
    add_settings_field(
      'callus_url',
      __('3CX Talk URL', 'wp-live-chat-support'),
      array($this, 'toggle_callus_url_callback'),
      'wplc_display_options',
      'general_settings_section',
      array()
    );

    add_settings_field(
      'show_all_pages',                    
      __('Enable in all pages', 'wp-live-chat-support'), 
      array($this, 'toggle_show_all_pages_callback'), 
      'wplc_display_options',       
      'general_settings_section',   
      array(                        
      )
    );

    add_settings_field(
      'pages_include',
      __('Just these', 'wp-live-chat-support'),
      array($this, 'toggle_include_pages_callback'),
      'wplc_display_options',
      'general_settings_section',
      array(
      )
    );

    add_settings_field(
      'powered_by',                   
      __('Show "Powered By 3CX"', 'wp-live-chat-support'), 
      array($this, 'toggle_powered_by_callback'), 
      'wplc_display_options',     
      'general_settings_section', 
      array(                      
      )
    );    

    // Finally, we register the fields with WordPress
    register_setting(
      'wplc_display_options',
      'wplc_display_options',
      array($this, 'validate_options')
    );
  } // end initialize_display_options

  /**
   * This function renders the interface elements for toggling the visibility of the header element.
   *
   * It accepts an array or arguments and expects the first element in the array to be the description
   * to be displayed next to the checkbox.
   */

  public function pbx_config_callback($args){
    $html ='<p class="description">'.__('In order to activate live chat you need to setup 3CX to answer live chats from your 3CX web portal and apps.','wp-live-chat-support').'</p>';
    $html.='<ol><li>'.sprintf(__('Get your free %s', 'wp-live-chat-support'), '<a target="_blank" href="https://www.3cx.com/startup/">'.__('3CX StartUP account','wp-live-chat-support').'</a>').' (if you already have 3CX you can skip this step).</li>';
    $html.='<li>'.__('Follow the steps in the wizard.','wp-live-chat-support').'</li>';
    $html.='<li>'.__('Take note of the 3CX Talk URL that you will be given during the live chat step.','wp-live-chat-support').'</li>';
    $html.='<li>'.sprintf(__('Read our %s.','wp-live-chat-support'),'<a target="_blank" href="https://www.3cx.com/docs/startup">'.__('Getting Started Guide', 'wp-live-chat-support').'</a>').'</li></ol>';
    echo $html;
  } // end pbx_config_callback

  public function toggle_callus_url_callback($args)
  {
    $options = $this->read_config();
    $html = '<input type="text" id="callus_url" style="width:600px" name="wplc_display_options[callus_url]" placeholder="'.htmlspecialchars(sprintf(__('Example: %s', 'wp-live-chat-support'),'https://your-pbx.3cx.eu:5001/LiveChat12345')).'" value="' . $options['callus_url'] . '" />';
    $html.='<p class="description">'.__('Enter the URL given to you during the live chat configuration step in the wizard.','wp-live-chat-support').'</p>';
    echo $html;
  } // end toggle_callus_url_callback

  public function toggle_show_all_pages_callback($args)
  {
    $options = $this->read_config();
    $html = '<input type="checkbox" id="show_all_pages" name="wplc_display_options[show_all_pages]" value="1" ' . checked(1, isset($options['show_all_pages']) ? $options['show_all_pages'] : 0, false) . '/>';
    echo $html;
  } // end toggle_show_all_pages_callback

  public function toggle_include_pages_callback($args)
  {
    $options = $this->read_config();
    $pages = get_pages();
    $pagenums = !empty($options['include_pages']) ? $options['include_pages'] : array();
    $pagelist = array();
    $homepageId = get_option('page_on_front');
    $pagelist[1] = 'Home';
    if (!empty($homepageId)) {
      $pagelist[$homepageId] = 'Home';
    }
    foreach ($pages as $page) {
      $pagelist[$page->ID] = $page->post_title;
    }
    $html = '<div class="wplc_pages_scrollbox' . ($options['show_all_pages'] ? ' wplc_box_disabled' : '') . '">';
    foreach ($pagelist as $k => $v) {
      $html .= '<p><input type="checkbox" id="display_page_' . $k . '" name="wplc_display_options[include_pages][' . $k . ']" value="1" ' . checked(1, isset($pagenums[$k]) ? 1 : 0, false) . '/>';
      $html .= htmlspecialchars($v) . '</p>';
    }
    $html .= '</div>';
    echo $html;
  } // end toggle_include_pages_callback

  public function toggle_powered_by_callback($args)
  {
    $options = $this->read_config();
    $html = '<input type="checkbox" id="powered_by" name="wplc_display_options[powered_by]" value="1" ' . checked(1, isset($options['powered_by']) ? $options['powered_by'] : 0, false) . '/>';
    echo $html;
  } // end toggle_powered_by_callback  

  public function validate_options($input)
  {
    $output = array();
    $output['show_all_pages'] = 0;
    if (isset($input['show_all_pages'])) {
      $output['show_all_pages'] = intval($input['show_all_pages']);
    }
    $output['powered_by'] = 0;
    if (isset($input['powered_by'])) {
      $output['powered_by'] = intval($input['powered_by']);
    }
    $output['callus_url'] = '';
    if (isset($input['callus_url'])) {
      $output['callus_url'] = strip_tags(stripslashes($input['callus_url']));
      $url=parse_url($output['callus_url']);
      $path=preg_replace("/[^A-Za-z0-9 ]/", '', reset(explode('/',substr($url['path'],1).'/')));
      if ($path=='' || substr($output['callus_url'],-1,1)=='/' || substr($output['callus_url'],-1,1)=='?'|| substr($output['callus_url'],0,8)!='https://' || !filter_var($output['callus_url'], FILTER_VALIDATE_URL,  FILTER_FLAG_PATH_REQUIRED)) {
        add_settings_error('wplc_display_options', 'callus_url', sprintf(__('Invalid 3CX Talk URL: %s', 'wp-live-chat-support'), htmlspecialchars($input['callus_url'])), 'error');
        $output['callus_url'] = '';
      } else {
        $output['callus_url']='https://'.$url['host'].((isset($url['port']) && $url['port']!=443) ? ':'.$url['port'] : '').'/'.$path;
      }
    }
    $output['include_pages'] = array();
    if (is_array($input['include_pages'])) {
      foreach ($input['include_pages'] as $k => $v) {
        $output['include_pages'][intval($k)] = intval($v);
      }
    }
    return apply_filters('validate_options', $output, $input);
  } // end validate_options

}
