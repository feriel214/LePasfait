<?php
/**
 * Theme panel's footer tith common links and info
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.6.48
 */
?>
<div class="trx_addons_theme_panel_footer">
	<?php
	$theme_info = trx_addons_get_theme_info();
	if (count($theme_info['theme_actions']) > 0) {
		?>
		<div class="trx_addons_theme_panel_links">
			<?php
			foreach ($theme_info['theme_actions'] as $action=>$item) {
				if ( empty( $item['button'] ) ) {
					continue;
				}
				?><div class="trx_addons_classic_block"><div class="trx_addons_classic_block_inner">
					<?php
					if (!empty($item['image'])) {
						?><img src="<?php echo esc_attr($item['image']); ?>" class="trx_addons_classic_block_image"><?php
					}
					?>
					<h2 class="trx_addons_classic_block_title"><?php
						if (!empty($item['icon'])) {
							?><i class="<?php echo esc_attr($item['icon']); ?>"></i><?php
						}
						echo esc_html($item['title']);
					?></h2>
					<div class="trx_addons_classic_block_description">
						<?php echo esc_html($item['description']); ?>
					</div>
					<a href="<?php echo esc_url( $item['link'] ); ?>" class="trx_addons_classic_block_link button button-primary"<?php
						if (strpos($item['link'], home_url()) === false) {
							echo ' target="_blank"';
						}
					?>>
						<?php echo esc_html($item['button']); ?>
					</a>
				</div></div><?php
			}
		?>
		</div>
		<?php
	}
	?>
</div>
