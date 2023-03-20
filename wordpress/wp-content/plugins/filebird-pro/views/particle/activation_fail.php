<?php
if($filebird_activation_error != '') {
  $filebird_activation_error = apply_filters('filebird_activation_error', $filebird_activation_error);
  $escapeLink = array(
    'a' => array(
      'href'   => array(),
      'target' => array(),
    ),
  );
  if($filebird_activation_error == 'no-purchase') {
    $filebird_activation_error = __('It seems you do not have any valid FileBird license. Please <a href="https://ninjateam.org/support" target="_blank">contact support</a> to get help or <a href="https://1.envato.market/Get-FileBird" target="_blank">purchase a FileBird license</a>', 'filebird');
  } elseif($filebird_activation_error == 'code-is-used') {
    $filebird_activation_error = __('This license was used, please <a href="https://1.envato.market/Get-FileBird" target="_blank">purchase another license</a>', 'filebird');
  }
  ?>
  <div class="notice notice-warning is-dismissible">
    <h3><?php esc_html_e('Oops! Activation failed.', 'filebird'); ?></h3>
    <p>
      <?php echo wp_kses($filebird_activation_error, $escapeLink); ?>
    </p>
  </div>
  <?php
}
?>