<?php
/**
 * Helper Admin Notice - Woo Updater Plugin is not Installed.
 *
 * @package PooCommerce\Views
 */

defined( 'ABSPATH' ) || exit;
?>
<div id="message" class="error poocommerce-message">
	<a class="poocommerce-message-close notice-dismiss" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'wc-hide-notice', 'woo_updater_not_installed' ), 'poocommerce_hide_notices_nonce', '_wc_notice_nonce' ) ); ?>"><?php esc_html_e( 'Dismiss', 'poocommerce' ); ?></a>
	<p>
	<?php
		echo wp_kses_post(
			sprintf(
				/* translators: 1: Woo Update Manager plugin install URL 2: Woo Update Manager plugin download URL */
				__(
					'Please <a href="%1$s">Install the PooCommerce.com Update Manager</a> to continue receiving the updates and streamlined support included in your PooCommerce.com subscriptions. Alternatively, you can <a href="%2$s">download</a> and install it manually.',
					'poocommerce'
				),
				esc_url( WC_Woo_Update_Manager_Plugin::generate_install_url() ),
				esc_url( WC_Woo_Update_Manager_Plugin::WOO_UPDATE_MANAGER_DOWNLOAD_URL )
			)
		);
		?>
	</p>
</div>
