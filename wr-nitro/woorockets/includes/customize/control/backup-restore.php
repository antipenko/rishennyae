<?php
/**
 * @version    1.0
 * @package    WR_Theme
 * @author     WooRockets Team <support@woorockets.com>
 * @copyright  Copyright (C) 2014 WooRockets.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.woorockets.com
 * Custom functions for WooCommerce.
 */

/**
 * Backup / restore custom control for WordPress Theme Customize.
 *
 * @package  WR_Theme
 * @since    1.0
 */
class WR_Nitro_Customize_Control_Backup_Restore extends WP_Customize_Control {
	public $type = 'backup-restore';

	/**
	 * Enqueue control related scripts/styles.
	 *
	 * @return  void
	 */
	public function enqueue() {
		static $enqueued;

		if ( ! isset( $enqueued ) ) {
			// Enqueue assets to upload file.
			wp_enqueue_script( 'jquery-file-upload', get_template_directory_uri() . '/assets/3rd-party/jquery-file-upload/jquery.uploadfile.min.js' );

			wp_enqueue_script( 'wr-nitro-customize-backup-restore', get_template_directory_uri() . '/assets/woorockets/js/admin/customize/control/backup-restore.js', array(), '1.0.0', true );

			wp_localize_script( 'wr-nitro-customize-backup-restore', 'wr_nitro_customize_backup_restore', array(
				'type'            => $this->type,
				'dismiss'         => esc_html__( 'Dismiss this message.', 'wr-nitro' ),
				'restore_url'     => admin_url( 'admin-ajax.php?action=nitro_restore_settings' ),
				'restore_nonce'   => wp_create_nonce( 'nitro_restore_settings' ),
				'restore_success' => esc_html__(
					'Successfully restored theme options from backup file. Please reload to see restored settings.',
					'wr-nitro'
				),
			) );

			$enqueued = true;
		}
	}

	/**
	 * Render the control's content.
	 *
	 * @return  void
	 */
	public function render_content() {
		if ( $this->label ) {
			?>
			<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<?php
		}

		if ( $this->description ) {
			?>
			<span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
			<?php
		}

		$value = $this->value();
		?>
		<div class="customize-control-content-backup-restore" id="wr-<?php echo esc_attr( $this->type ); ?>-<?php echo esc_attr( $this->id ); ?>">
			<ul>
				<li><a class="nitro-backup-settings button" class="welcome-icon dashicons-backup" href="<?php
					echo add_query_arg(
						'nonce',
						wp_create_nonce( 'nitro_backup_settings' ),
						admin_url( 'admin-ajax.php?action=nitro_backup_settings' )
					);
				?>">
					<?php esc_html_e( 'Backup Theme Options', 'wr-nitro' ); ?>
				</a></li>
				<li><a class="nitro-restore-settings button" class="welcome-icon dashicons-update" href="#">
					<?php esc_html_e( 'Restore Theme Options', 'wr-nitro' ); ?>
				</a></li>
			</ul>
			<div class="nitro-restore-settings-form hidden">
				<div class="nitro-upload-backup">
					<?php esc_html_e( 'Select backup file...', 'wr-nitro' ); ?>
				</div>
				<div class="start-upload hidden">
					<?php esc_html_e( 'Upload and restore', 'wr-nitro' ); ?>
				</div>
			</div>
		</div>
		<?php
	}
}
