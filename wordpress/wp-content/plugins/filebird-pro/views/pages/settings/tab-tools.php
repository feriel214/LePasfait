<div id="fbv-tools-setting">
<table class="form-table">
	<tbody>
		<tr>
			<th scope="row">
			<label><?php esc_html_e( 'Import from old version', 'filebird' ); ?></label>
			</th>
			<td>
			<button type="button" class="button button-primary njt_fbv_import_from_old_now njt-button-loading"><?php esc_html_e( 'Update now', 'filebird' ); ?></button>
			<p class="description"><?php esc_html_e( 'By running this action, all folders created in version 3.9 & earlier installs will be imported.', 'filebird' ); ?></p>
			</td>
		</tr>
        <tr>
            <th scope="row">
                <label for="">
                <?php echo esc_html__( 'REST API key', 'filebird' ); ?>
                </label>
            </th>
            <td>
                <?php
                $key     = get_option( 'fbv_rest_api_key', '' );
                $classes = array( 'regular-text' );
                if ( strlen( $key ) == 0 ) {
                    $classes[] = 'hidden';
                }
                $classes = array_map( 'esc_attr', $classes );
                ?>
                <input type="text" id="fbv_rest_api_key" class="<?php echo implode( ' ', $classes ); ?>" value="<?php echo esc_attr( $key ); ?>" onclick="this.select()" />
                <button type="button" class="button button-primary fbv_generate_api_key_now njt-button-loading"><?php esc_html_e( 'Generate' ); ?></button>
			    <p class="description">
                    <?php echo wp_kses(__( 'Please see FileBird API for developers <a target="_blank" href="https://ninjateam.gitbook.io/filebird/api">here</a>.', 'filebird' ), array(
                        'a' => array(
				            'href'   => array(),
				            'target' => array(),
			            ))
                    ); ?>
                </p>
            </td>
	    </tr>
        <tr>
			<th colspan="2">
			  <div class="fbv-text-divider">
                  <span><?php echo esc_html__("Danger Zone", "filebird") ?></span>
              </div>
			</th>
        </tr>
        <tr>
            <th scope="row">
                <label><?php esc_html_e( 'Clear all data', 'filebird' ); ?></label>
            </th>
            <td>
                <button type="button" class="button njt_fbv_clear_all_data njt-button-loading"><?php esc_html_e( 'Clear', 'filebird' ); ?></button>
                <p class="description"><?php esc_html_e( 'This action will delete all FileBird data, FileBird settings and bring you back to WordPress default media library.', 'filebird' ); ?></p>
            </td>
        </tr>
	</tbody>
</table>
</div>