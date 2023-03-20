<?php if ( ( $countEnhancedFolder + $countWpmlfFolder + $countWpmfFolder + $countRealMediaFolder + $countHappyFiles + $countPremioFolder ) > 0 ) : ?>
<h2><?php esc_html_e( 'Import', 'filebird' ); ?></h2>
<div id="fbv-import-setting">
<p>
    <?php esc_html_e( 'Import categories/folders from other plugins. We import virtual folders, your website will be safe, do not worry ;)', 'filebird' ); ?>
</p>
<table class="form-table">
    <tbody>
        <tr class="<?php echo ($countEnhancedFolder <= 3 ? 'hidden' : ''); ?>">
            <th scope="row">
                <label for="">
                    <?php echo esc_html__( 'Enhanced Media Library plugin by wpUXsolutions', 'filebird' ); ?>
                </label>
            </th>
            <td>
                <?php if ( $countEnhancedFolder > 0 ) : ?>
                <button class="button button-primary button-large njt-fb-import njt-button-loading" data-site="enhanced"
                    type="button"
                    data-count="<?php esc_attr_e($countEnhancedFolder); ?>"><?php esc_html_e( 'Import Now', 'filebird' ); ?></button>
                <?php endif; ?>
                <p class="description">
                    <?php
					$str = __( 'We found you have <strong>(%1$s)</strong> categories you created from <strong>Enhanced Media Library</strong> plugin.', 'filebird' );
					if ( $countEnhancedFolder > 0 ) {
						$str .= __( ' Would you like to import to <strong>FileBird</strong>?', 'filebird' );
					}
					echo wp_kses( sprintf( $str, $countEnhancedFolder ), array('strong' => array()) );
					?>
                </p>
            </td>
        </tr>
        <tr class="<?php echo ($countWpmlfFolder <= 3 ? 'hidden' : ''); ?>">
            <th scope="row">
                <label for="">
                    <?php echo esc_html__( 'WordPress Media Library Folders by Max Foundry', 'filebird' ); ?>
                </label>
            </th>
            <td>
                <?php if ( $countWpmlfFolder > 0 ) : ?>
                <button class="button button-primary button-large njt-fb-import njt-button-loading" data-site="wpmlf"
                    type="button"
                    data-count="<?php echo esc_attr_e($countWpmlfFolder); ?>"><?php esc_html_e( 'Import Now', 'filebird' ); ?></button>
                <?php endif; ?>
                <p class="description">
                    <?php
					$str = __( 'We found you have <strong>(%1$s)</strong> categories you created from <strong>WordPress Media Library Folders</strong> plugin.', 'filebird' );
					if ( $countWpmlfFolder > 0 ) {
						$str .= __( ' Would you like to import to <strong>FileBird</strong>?', 'filebird' );
					}
					echo wp_kses( sprintf( $str, $countWpmlfFolder ), array('strong' => array()) );
					?>
                </p>
            </td>
        </tr>
        <tr class="<?php echo ($countWpmfFolder <= 3 ? 'hidden' : ''); ?>">
            <th scope="row">
                <label for="">
                    <?php echo esc_html__( 'WP Media folder by Joomunited', 'filebird' ); ?>
                </label>
            </th>
            <td>
                <?php if ( $countWpmfFolder > 0 ) : ?>
                <button class="button button-primary button-large njt-fb-import njt-button-loading" data-site="wpmf"
                    type="button"
                    data-count="<?php echo esc_attr_e($countWpmfFolder); ?>"><?php esc_html_e( 'Import Now', 'filebird' ); ?></button>
                <?php endif; ?>
                <p class="description">
                    <?php
					$str = __( 'We found you have <strong>(%1$s)</strong> categories you created from <strong>WP Media folder</strong> plugin.', 'filebird' );
					if ( $countWpmfFolder > 0 ) {
						$str .= __( ' Would you like to import to <strong>FileBird</strong>?', 'filebird' );
					}
					echo wp_kses( sprintf( $str, $countWpmfFolder ), array('strong' => array()) );
					?>
                </p>
            </td>
        </tr>
        <tr class="<?php echo ($countRealMediaFolder <= 3 ? 'hidden' : ''); ?>">
            <th scope="row">
                <label for="">
                    <?php echo esc_html__( 'WP Real Media Library by devowl.io GmbH', 'filebird' ); ?>
                </label>
            </th>
            <td>
                <?php if ( $countRealMediaFolder > 0 ) : ?>
                <button class="button button-primary button-large njt-fb-import njt-button-loading"
                    data-site="realmedia" type="button"
                    data-count="<?php esc_attr_e($countRealMediaFolder); ?>"><?php esc_html_e( 'Import Now', 'filebird' ); ?></button>
                <?php endif; ?>
                <p class="description">
                    <?php
					$str = __( 'We found you have <strong>(%1$s)</strong> categories you created from <strong>WP Real Media Library</strong> plugin.', 'filebird' );
					if ( $countRealMediaFolder > 0 ) {
						$str .= __( ' Would you like to import to <strong>FileBird</strong>?', 'filebird' );
					}
					echo wp_kses( sprintf( $str, $countRealMediaFolder ), array('strong' => array()) );
					?>
                </p>
            </td>
        </tr>
        <tr class="<?php echo ($countHappyFiles <= 3 ? 'hidden' : ''); ?>">
            <th scope="row">
                <label for="">
                    <?php echo esc_html__( 'HappyFiles by Codeer', 'filebird' ); ?>
                </label>
            </th>
            <td>
                <?php if ( $countHappyFiles > 0 ) : ?>
                <button class="button button-primary button-large njt-fb-import njt-button-loading"
                    data-site="happyfiles" type="button"
                    data-count="<?php esc_attr_e($countHappyFiles); ?>"><?php esc_html_e( 'Import Now', 'filebird' ); ?></button>
                <?php endif; ?>
                <p class="description">
                    <?php
					$str = __( 'We found you have <strong>(%1$s)</strong> categories you created from <strong>HappyFiles</strong> plugin.', 'filebird' );
					if ( $countHappyFiles > 0 ) {
						$str .= __( ' Would you like to import to <strong>FileBird</strong>?', 'filebird' );
					}
					echo wp_kses( sprintf( $str, $countHappyFiles ), array('strong' => array()) );
					?>
                </p>
            </td>
        </tr>
        <tr class="<?php echo ($countPremioFolder <= 3 ? 'hidden' : ''); ?>">
            <th scope="row">
                <label for="">
                    <?php echo esc_html__( 'Folders by Premio', 'filebird' ); ?>
                </label>
            </th>
            <td>
                <?php if ( $countPremioFolder > 0 ) : ?>
                <button class="button button-primary button-large njt-fb-import njt-button-loading" data-site="premio"
                    type="button"
                    data-count="<?php esc_attr_e($countPremioFolder); ?>"><?php esc_html_e( 'Import Now', 'filebird' ); ?></button>
                <?php endif; ?>
                <p class="description">
                    <?php
					$str = __( 'We found you have <strong>(%1$s)</strong> categories you created from <strong>Folders</strong> plugin.', 'filebird' );
					if ( $countPremioFolder > 0 ) {
						$str .= __( ' Would you like to import to <strong>FileBird</strong>?', 'filebird' );
					}
					echo wp_kses( sprintf( $str, $countPremioFolder ), array('strong' => array()) );
					?>
                </p>
            </td>
        </tr>
    </tbody>
</table>
<div class="fbv-row-breakline">
    <span class="fbv-breakline"></span>
</div>
<?php endif ?>
<h2><?php esc_html_e( 'Export', 'filebird' ); ?></h2>
<table class="form-table">
    <tbody>
        <tr>
            <th scope="row">
                <label for="">
                    <?php echo esc_html__( 'Export CSV', 'filebird' ); ?>
                </label>
            </th>
            <td>
                <div class="flex-item-center">
                    <button class="button button-primary button-large njt-fb-csv-export njt-button-loading"
                        type="button">
                        <?php esc_html_e( 'Export Now', 'filebird' ); ?>
                    </button>
                    <a id="njt-fb-download-csv" href="javascript:;" class="hidden">Download File</a>
                </div>
                <p class="description">
                    <?php echo esc_html__( 'The current folder structure will be exported.', 'filebird' ); ?>
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="">
                    <?php echo esc_html__( 'Import CSV', 'filebird' ); ?>
                </label>
            </th>
            <td>
                <div class="flex-item-center">
                    <input type="file" accept=".csv" id="njt-fb-upload-csv" name="csv_file">
                    <button class="button button-large njt-fb-csv-import hidden njt-button-loading" type="button">
                        <?php esc_html_e( 'Import Now', 'filebird' ); ?>
                    </button>
                </div>
                <p class="description">
                    <?php echo esc_html__( 'Choose FileBird CSV file to import.', 'filebird' ); ?><br />
                    <?php echo esc_html__( '(Please check to make sure that there is no duplicated name. The current folder structure is preserved.)', 'filebird' ); ?><br />
                </p>
            </td>
        </tr>
    </tbody>
</table>
</div>