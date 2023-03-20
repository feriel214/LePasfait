<table class="form-table">
  <tr>
    <th scope="row">
      <label for="njt_fbv_folder_per_user"><?php esc_html_e('Each user has his own folders?', 'filebird'); ?></label>
    </th>
    <td>
      <label class="njt-switch">
        <input type="checkbox" name="njt_fbv_folder_per_user" class="njt-submittable" id="njt_fbv_folder_per_user"  value="1" <?php checked(get_option('njt_fbv_folder_per_user'), '1'); ?> />
        <span class="slider round">
          <span class="njt-switch-cursor"></span>
        </span>
      </label>
    </td>
  </tr>
</table>