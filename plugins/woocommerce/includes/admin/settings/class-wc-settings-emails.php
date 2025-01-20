<?php
/**
 * PooCommerce Email Settings
 *
 * @package PooCommerce\Admin
 * @version 2.1.0
 */

use Automattic\PooCommerce\Internal\Admin\EmailPreview\EmailPreview;
use Automattic\PooCommerce\Internal\BrandingController;
use Automattic\PooCommerce\Utilities\FeaturesUtil;

defined( 'ABSPATH' ) || exit;

if ( class_exists( 'WC_Settings_Emails', false ) ) {
	return new WC_Settings_Emails();
}

/**
 * WC_Settings_Emails.
 */
class WC_Settings_Emails extends WC_Settings_Page {

	/**
	 * Array of font families supported in email templates
	 *
	 * @var string[]
	 */
	public static $font = array(
		'Arial'           => "Arial, 'Helvetica Neue', Helvetica, sans-serif",
		'Comic Sans MS'   => "'Comic Sans MS', 'Marker Felt-Thin', Arial, sans-serif",
		'Courier New'     => "'Courier New', Courier, 'Lucida Sans Typewriter', 'Lucida Typewriter', monospace",
		'Georgia'         => "Georgia, Times, 'Times New Roman', serif",
		'Helvetica'       => "'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif",
		'Lucida'          => "'Lucida Sans Unicode', 'Lucida Grande', sans-serif",
		'Tahoma'          => 'Tahoma, Verdana, Segoe, sans-serif',
		'Times New Roman' => "'Times New Roman', Times, Baskerville, Georgia, serif",
		'Trebuchet MS'    => "'Trebuchet MS', 'Lucida Grande', 'Lucida Sans Unicode', 'Lucida Sans', Tahoma, sans-serif",
		'Verdana'         => 'Verdana, Geneva, sans-serif',
	);

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id    = 'email';
		$this->label = __( 'Emails', 'poocommerce' );

		add_action( 'poocommerce_admin_field_email_notification', array( $this, 'email_notification_setting' ) );
		add_action( 'poocommerce_admin_field_email_preview', array( $this, 'email_preview' ) );
		add_action( 'poocommerce_admin_field_email_image_url', array( $this, 'email_image_url' ) );
		add_action( 'poocommerce_admin_field_email_font_family', array( $this, 'email_font_family' ) );
		add_action( 'poocommerce_admin_field_email_color_palette', array( $this, 'email_color_palette' ) );
		if ( FeaturesUtil::feature_is_enabled( 'email_improvements' ) ) {
			add_action( 'poocommerce_email_settings_after', array( $this, 'email_preview_single' ) );
			add_filter( 'poocommerce_admin_settings_sanitize_option_poocommerce_email_header_image', array( $this, 'sanitize_email_header_image' ), 10, 3 );
		}
		parent::__construct();
	}

	/**
	 * Setting page icon.
	 *
	 * @var string
	 */
	public $icon = 'atSymbol';

	/**
	 * Get own sections.
	 *
	 * @return array
	 */
	protected function get_own_sections() {
		return array(
			'' => __( 'Email options', 'poocommerce' ),
		);
	}

	/**
	 * Get settings array.
	 *
	 * @return array
	 */
	protected function get_settings_for_default_section() {
		$desc_help_text = sprintf(
		/* translators: %1$s: Link to WP Mail Logging plugin, %2$s: Link to Email FAQ support page. */
			__( 'To ensure your store&rsquo;s notifications arrive in your and your customers&rsquo; inboxes, we recommend connecting your email address to your domain and setting up a dedicated SMTP server. If something doesn&rsquo;t seem to be sending correctly, install the <a href="%1$s">WP Mail Logging Plugin</a> or check the <a href="%2$s">Email FAQ page</a>.', 'poocommerce' ),
			'https://wordpress.org/plugins/wp-mail-logging/',
			'https://poocommerce.com/document/email-faq'
		);

		/* translators: %s: Nonced email preview link */
		$email_template_description = sprintf( __( 'This section lets you customize the PooCommerce emails. <a href="%s" target="_blank">Click here to preview your email template</a>.', 'poocommerce' ), wp_nonce_url( admin_url( '?preview_poocommerce_mail=true' ), 'preview-mail' ) );
		$logo_image                 = array(
			'title'       => __( 'Header image', 'poocommerce' ),
			'desc'        => __( 'Paste the URL of an image you want to show in the email header. Upload images using the media uploader (Media > Add New).', 'poocommerce' ),
			'id'          => 'poocommerce_email_header_image',
			'type'        => 'text',
			'css'         => 'min-width:400px;',
			'placeholder' => __( 'N/A', 'poocommerce' ),
			'default'     => '',
			'autoload'    => false,
			'desc_tip'    => true,
		);
		$header_alignment           = null;
		$font_family                = null;

		/* translators: %s: Available placeholders for use */
		$footer_text_description = __( 'The text to appear in the footer of all PooCommerce emails.', 'poocommerce' ) . ' ' . sprintf( __( 'Available placeholders: %s', 'poocommerce' ), '{site_title} {site_url}' );
		$footer_text_default     = '{site_title} &mdash; Built with {PooCommerce}';

		// These defaults should be chosen by the same logic as the other color option properties.
		list(
			'base_color_default' => $base_color_default,
			'bg_color_default' => $bg_color_default,
			'body_bg_color_default' => $body_bg_color_default,
			'body_text_color_default' => $body_text_color_default,
			'footer_text_color_default' => $footer_text_color_default,
		) = $this->get_email_default_colors();

		$base_color_title = __( 'Base color', 'poocommerce' );
		/* translators: %s: default color */
		$base_color_desc = sprintf( __( 'The base color for PooCommerce email templates. Default %s.', 'poocommerce' ), '<code>' . $base_color_default . '</code>' );

		$bg_color_title = __( 'Background color', 'poocommerce' );
		/* translators: %s: default color */
		$bg_color_desc = sprintf( __( 'The background color for PooCommerce email templates. Default %s.', 'poocommerce' ), '<code>' . $bg_color_default . '</code>' );

		$body_bg_color_title = __( 'Body background color', 'poocommerce' );
		/* translators: %s: default color */
		$body_bg_color_desc = sprintf( __( 'The main body background color. Default %s.', 'poocommerce' ), '<code>' . $body_bg_color_default . '</code>' );

		$body_text_color_title = __( 'Body text color', 'poocommerce' );
		/* translators: %s: default color */
		$body_text_color_desc = sprintf( __( 'The main body text color. Default %s.', 'poocommerce' ), '<code>' . $body_text_color_default . '</code>' );

		$footer_text_color_title = __( 'Footer text color', 'poocommerce' );
		/* translators: %s: footer default color */
		$footer_text_color_desc = sprintf( __( 'The footer text color. Default %s.', 'poocommerce' ), '<code>' . $footer_text_color_default . '</code>' );

		$color_palette_section_header = null;
		$color_palette_section_end    = null;

		if ( FeaturesUtil::feature_is_enabled( 'email_improvements' ) ) {
			$email_template_description = __( 'Customize your PooCommerce email template and preview it below.', 'poocommerce' );
			$logo_image                 = array(
				'title'       => __( 'Logo', 'poocommerce' ),
				'desc'        => __( 'Add your logo to each of your PooCommerce emails. If no logo is uploaded, your site title will be used instead.', 'poocommerce' ),
				'id'          => 'poocommerce_email_header_image',
				'type'        => 'email_image_url',
				'css'         => 'min-width:400px;',
				'placeholder' => __( 'N/A', 'poocommerce' ),
				'default'     => '',
				'autoload'    => false,
				'desc_tip'    => true,
			);
			$header_alignment           = array(
				'title'    => __( 'Header alignment', 'poocommerce' ),
				'id'       => 'poocommerce_email_header_alignment',
				'desc_tip' => '',
				'default'  => 'left',
				'type'     => 'select',
				'class'    => 'wc-enhanced-select',
				'options'  => array(
					'left'   => __( 'Left', 'poocommerce' ),
					'center' => __( 'Center', 'poocommerce' ),
					'right'  => __( 'Right', 'poocommerce' ),
				),
			);

			$font_family = array(
				'title'   => __( 'Font family', 'poocommerce' ),
				'id'      => 'poocommerce_email_font_family',
				'default' => 'Helvetica',
				'type'    => 'email_font_family',
			);

			/* translators: %s: Available placeholders for use */
			$footer_text_description = __( 'This text will appear in the footer of all of your PooCommerce emails.', 'poocommerce' ) . ' ' . sprintf( __( 'Available placeholders: %s', 'poocommerce' ), '{site_title} {site_url} {store_address} {store_email}' );
			$footer_text_default     = '{site_title}<br />{store_address}';

			$base_color_title = __( 'Accent', 'poocommerce' );
			/* translators: %s: default color */
			$base_color_desc = sprintf( __( 'Customize the color of your buttons and links. Default %s.', 'poocommerce' ), '<code>' . $base_color_default . '</code>' );

			$bg_color_title = __( 'Email background', 'poocommerce' );
			/* translators: %s: default color */
			$bg_color_desc = sprintf( __( 'Select a color for the background of your emails. Default %s.', 'poocommerce' ), '<code>' . $bg_color_default . '</code>' );

			$body_bg_color_title = __( 'Content background', 'poocommerce' );
			/* translators: %s: default color */
			$body_bg_color_desc = sprintf( __( 'Choose a background color for the content area of your emails. Default %s.', 'poocommerce' ), '<code>' . $body_bg_color_default . '</code>' );

			$body_text_color_title = __( 'Heading & text', 'poocommerce' );
			/* translators: %s: default color */
			$body_text_color_desc = sprintf( __( 'Set the color of your headings and text. Default %s.', 'poocommerce' ), '<code>' . $body_text_color_default . '</code>' );

			$footer_text_color_title = __( 'Secondary text', 'poocommerce' );
			/* translators: %s: footer default color */
			$footer_text_color_desc = sprintf( __( 'Choose a color for your secondary text, such as your footer content. Default %s.', 'poocommerce' ), '<code>' . $footer_text_color_default . '</code>' );

			$color_palette_section_header = array(
				'title' => __( 'Color palette', 'poocommerce' ),
				'type'  => 'email_color_palette',
				'id'    => 'email_color_palette',
			);

			$color_palette_section_end = array(
				'type' => 'sectionend',
				'id'   => 'email_template_options',
			);
		}

		// Reorder email color settings based on the email_improvements feature flag.

		$base_color_setting = array(
			'title'    => $base_color_title,
			'desc'     => $base_color_desc,
			'id'       => 'poocommerce_email_base_color',
			'type'     => 'color',
			'css'      => 'width:6em;',
			'default'  => $base_color_default,
			'autoload' => false,
			'desc_tip' => true,
		);

		$bg_color_setting = array(
			'title'    => $bg_color_title,
			'desc'     => $bg_color_desc,
			'id'       => 'poocommerce_email_background_color',
			'type'     => 'color',
			'css'      => 'width:6em;',
			'default'  => $bg_color_default,
			'autoload' => false,
			'desc_tip' => true,
		);

		$body_bg_color_setting = array(
			'title'    => $body_bg_color_title,
			'desc'     => $body_bg_color_desc,
			'id'       => 'poocommerce_email_body_background_color',
			'type'     => 'color',
			'css'      => 'width:6em;',
			'default'  => $body_bg_color_default,
			'autoload' => false,
			'desc_tip' => true,
		);

		$body_text_color_setting = array(
			'title'    => $body_text_color_title,
			'desc'     => $body_text_color_desc,
			'id'       => 'poocommerce_email_text_color',
			'type'     => 'color',
			'css'      => 'width:6em;',
			'default'  => $body_text_color_default,
			'autoload' => false,
			'desc_tip' => true,
		);

		$footer_text_color_setting = array(
			'title'    => $footer_text_color_title,
			'desc'     => $footer_text_color_desc,
			'id'       => 'poocommerce_email_footer_text_color',
			'type'     => 'color',
			'css'      => 'width:6em;',
			'default'  => $footer_text_color_default,
			'autoload' => false,
			'desc_tip' => true,
		);

		$reorder_colors = FeaturesUtil::feature_is_enabled( 'email_improvements' );

		$base_color_setting_in_template_opts        = $reorder_colors ? null : $base_color_setting;
		$bg_color_setting_in_template_opts          = $reorder_colors ? null : $bg_color_setting;
		$body_bg_color_setting_in_template_opts     = $reorder_colors ? null : $body_bg_color_setting;
		$body_text_color_setting_in_template_opts   = $reorder_colors ? null : $body_text_color_setting;
		$footer_text_color_setting_in_template_opts = $reorder_colors ? null : $footer_text_color_setting;

		$base_color_setting_in_palette        = $reorder_colors ? $base_color_setting : null;
		$bg_color_setting_in_palette          = $reorder_colors ? $bg_color_setting : null;
		$body_bg_color_setting_in_palette     = $reorder_colors ? $body_bg_color_setting : null;
		$body_text_color_setting_in_palette   = $reorder_colors ? $body_text_color_setting : null;
		$footer_text_color_setting_in_palette = $reorder_colors ? $footer_text_color_setting : null;

		$settings =
			array(
				array(
					'title' => __( 'Email notifications', 'poocommerce' ),
					/* translators: %s: help description with link to WP Mail logging and support page. */
					'desc'  => sprintf( __( 'Email notifications sent from PooCommerce are listed below. Click on an email to configure it.<br>%s', 'poocommerce' ), $desc_help_text ),
					'type'  => 'title',
					'id'    => 'email_notification_settings',
				),

				array( 'type' => 'email_notification' ),

				array(
					'type' => 'sectionend',
					'id'   => 'email_notification_settings',
				),

				array(
					'type' => 'sectionend',
					'id'   => 'email_recipient_options',
				),

				array(
					'title' => __( 'Email sender options', 'poocommerce' ),
					'type'  => 'title',
					'desc'  => __( "Set the name and email address you'd like your outgoing emails to use.", 'poocommerce' ),
					'id'    => 'email_options',
				),

				array(
					'title'    => __( '"From" name', 'poocommerce' ),
					'desc'     => '',
					'id'       => 'poocommerce_email_from_name',
					'type'     => 'text',
					'css'      => 'min-width:400px;',
					'default'  => esc_attr( get_bloginfo( 'name', 'display' ) ),
					'autoload' => false,
					'desc_tip' => true,
				),

				array(
					'title'             => __( '"From" address', 'poocommerce' ),
					'desc'              => '',
					'id'                => 'poocommerce_email_from_address',
					'type'              => 'email',
					'custom_attributes' => array(
						'multiple' => 'multiple',
					),
					'css'               => 'min-width:400px;',
					'default'           => get_option( 'admin_email' ),
					'autoload'          => false,
					'desc_tip'          => true,
				),

				array(
					'type' => 'sectionend',
					'id'   => 'email_options',
				),

				array(
					'title' => __( 'Email template', 'poocommerce' ),
					'type'  => 'title',
					'desc'  => $email_template_description,
					'id'    => 'email_template_options',
				),

				$logo_image,

				$header_alignment,

				$font_family,

				$base_color_setting_in_template_opts,

				$bg_color_setting_in_template_opts,

				$body_bg_color_setting_in_template_opts,

				$body_text_color_setting_in_template_opts,

				array(
					'title'       => __( 'Footer text', 'poocommerce' ),
					'desc'        => $footer_text_description,
					'id'          => 'poocommerce_email_footer_text',
					'css'         => 'width:400px; height: 75px;',
					'placeholder' => __( 'N/A', 'poocommerce' ),
					'type'        => 'textarea',
					'default'     => $footer_text_default,
					'autoload'    => false,
					'desc_tip'    => true,
				),

				$footer_text_color_setting_in_template_opts,

				array(
					'type' => 'sectionend',
					'id'   => 'email_template_options',
				),

				$color_palette_section_header,

				$base_color_setting_in_palette,

				$bg_color_setting_in_palette,

				$body_bg_color_setting_in_palette,

				$body_text_color_setting_in_palette,

				$footer_text_color_setting_in_palette,

				$color_palette_section_end,

				array( 'type' => 'email_preview' ),

				array(
					'title' => __( 'Store management insights', 'poocommerce' ),
					'type'  => 'title',
					'id'    => 'email_merchant_notes',
				),

				array(
					'title'         => __( 'Enable email insights', 'poocommerce' ),
					'desc'          => __( 'Receive email notifications with additional guidance to complete the basic store setup and helpful insights', 'poocommerce' ),
					'id'            => 'poocommerce_merchant_email_notifications',
					'type'          => 'checkbox',
					'checkboxgroup' => 'start',
					'default'       => 'no',
					'autoload'      => false,
				),

				array(
					'type' => 'sectionend',
					'id'   => 'email_merchant_notes',
				),
			);

		// Remove empty elements that depend on the email_improvements feature flag.
		$settings = array_filter( $settings );

		return apply_filters( 'poocommerce_email_settings', $settings );
	}

	/**
	 * Get default colors for emails.
	 */
	private function get_email_default_colors() {
		$base_color_default        = BrandingController::get_default_email_base_color();
		$bg_color_default          = '#f7f7f7';
		$body_bg_color_default     = '#ffffff';
		$body_text_color_default   = '#3c3c3c';
		$footer_text_color_default = '#3c3c3c';

		if ( FeaturesUtil::feature_is_enabled( 'email_improvements' ) ) {
			$base_color_default        = '#8526ff';
			$bg_color_default          = '#ffffff';
			$body_bg_color_default     = '#ffffff';
			$body_text_color_default   = '#1e1e1e';
			$footer_text_color_default = '#787c82';

			if ( wc_current_theme_is_fse_theme() && function_exists( 'wp_get_global_styles' ) ) {
				$global_styles             = wp_get_global_styles( array(), array( 'transforms' => array( 'resolve-variables' ) ) );
				$base_color_global         = isset( $global_styles['elements']['button']['color']['text'] )
					? sanitize_hex_color( $global_styles['elements']['button']['color']['text'] ) : '';
				$bg_color_global           = isset( $global_styles['color']['background'] )
					? sanitize_hex_color( $global_styles['color']['background'] ) : '';
				$body_bg_color_global      = isset( $global_styles['color']['background'] )
					? sanitize_hex_color( $global_styles['color']['background'] ) : '';
				$body_text_color_global    = isset( $global_styles['color']['text'] )
					? sanitize_hex_color( $global_styles['color']['text'] ) : '';
				$footer_text_color_global  = isset( $global_styles['elements']['caption']['color']['text'] )
					? sanitize_hex_color( $global_styles['elements']['caption']['color']['text'] ) : '';
				$base_color_default        = $base_color_global ? $base_color_global : $base_color_default;
				$bg_color_default          = $bg_color_global ? $bg_color_global : $bg_color_default;
				$body_bg_color_default     = $body_bg_color_global ? $body_bg_color_global : $body_bg_color_default;
				$body_text_color_default   = $body_text_color_global ? $body_text_color_global : $body_text_color_default;
				$footer_text_color_default = $footer_text_color_global ? $footer_text_color_global : $footer_text_color_default;
			}
		}

		return compact(
			'base_color_default',
			'bg_color_default',
			'body_bg_color_default',
			'body_text_color_default',
			'footer_text_color_default',
		);
	}

	/**
	 * Get custom fonts for emails.
	 */
	public function get_custom_fonts() {
		$custom_fonts = array();
		if ( wc_current_theme_is_fse_theme() && class_exists( 'WP_Font_Face_Resolver' ) ) {
			$theme_fonts = WP_Font_Face_Resolver::get_fonts_from_theme_json();
			if ( count( $theme_fonts ) > 0 ) {
				foreach ( $theme_fonts as $font ) {
					if ( ! empty( $font[0]['font-family'] ) ) {
						$custom_fonts[ $font[0]['font-family'] ] = $font[0]['font-family'];
					}
				}
			}
		}
		ksort( $custom_fonts );

		return $custom_fonts;
	}

	/**
	 * Output the settings.
	 */
	public function output() {
		global $current_section;

		// Define emails that can be customised here.
		$mailer          = WC()->mailer();
		$email_templates = $mailer->get_emails();

		if ( $current_section ) {
			foreach ( $email_templates as $email_key => $email ) {
				if ( strtolower( $email_key ) === $current_section ) {
					$this->run_email_admin_options( $email );
					break;
				}
			}
		}

		parent::output();
	}

	/**
	 * Run the 'admin_options' method on a given email.
	 * This method exists to easy unit testing.
	 *
	 * @param object $email The email object to run the method on.
	 */
	protected function run_email_admin_options( $email ) {
		$email->admin_options();
	}

	/**
	 * Save settings.
	 */
	public function save() {
		global $current_section;

		if ( ! $current_section ) {
			$this->save_settings_for_current_section();
			$this->do_update_options_action();
		} else {
			$wc_emails = WC_Emails::instance();

			if ( in_array( $current_section, array_map( 'sanitize_title', array_keys( $wc_emails->get_emails() ) ), true ) ) {
				foreach ( $wc_emails->get_emails() as $email_id => $email ) {
					if ( sanitize_title( $email_id ) === $current_section ) {
						$this->do_update_options_action( $email->id );
					}
				}
			} else {
				$this->save_settings_for_current_section();
				$this->do_update_options_action();
			}
		}
	}

	/**
	 * Output email notification settings.
	 */
	public function email_notification_setting() {
		// Define emails that can be customised here.
		$mailer          = WC()->mailer();
		$email_templates = $mailer->get_emails();

		?>
		<tr valign="top">
		<td class="wc_emails_wrapper" colspan="2">
			<table class="wc_emails widefat" cellspacing="0">
				<thead>
					<tr>
						<?php
						$columns = apply_filters(
							'poocommerce_email_setting_columns',
							array(
								'status'     => '',
								'name'       => __( 'Email', 'poocommerce' ),
								'email_type' => __( 'Content type', 'poocommerce' ),
								'recipient'  => __( 'Recipient(s)', 'poocommerce' ),
								'actions'    => '',
							)
						);
						foreach ( $columns as $key => $column ) {
							echo '<th class="wc-email-settings-table-' . esc_attr( $key ) . '">' . esc_html( $column ) . '</th>';
						}
						?>
						</tr>
					</thead>
					<tbody>
						<?php
						foreach ( $email_templates as $email_key => $email ) {
							echo '<tr>';

							foreach ( $columns as $key => $column ) {

								switch ( $key ) {
									case 'name':
										echo '<td class="wc-email-settings-table-' . esc_attr( $key ) . '">
										<a href="' . esc_url( admin_url( 'admin.php?page=wc-settings&tab=email&section=' . strtolower( $email_key ) ) ) . '">' . esc_html( $email->get_title() ) . '</a>
										' . wc_help_tip( $email->get_description() ) . '
									</td>';
										break;
									case 'recipient':
										echo '<td class="wc-email-settings-table-' . esc_attr( $key ) . '">
										' . esc_html( $email->is_customer_email() ? __( 'Customer', 'poocommerce' ) : $email->get_recipient() ) . '
									</td>';
										break;
									case 'status':
										echo '<td class="wc-email-settings-table-' . esc_attr( $key ) . '">';

										if ( $email->is_manual() ) {
											echo '<span class="status-manual tips" data-tip="' . esc_attr__( 'Manually sent', 'poocommerce' ) . '">' . esc_html__( 'Manual', 'poocommerce' ) . '</span>';
										} elseif ( $email->is_enabled() ) {
											echo '<span class="status-enabled tips" data-tip="' . esc_attr__( 'Enabled', 'poocommerce' ) . '">' . esc_html__( 'Yes', 'poocommerce' ) . '</span>';
										} else {
											echo '<span class="status-disabled tips" data-tip="' . esc_attr__( 'Disabled', 'poocommerce' ) . '">-</span>';
										}

										echo '</td>';
										break;
									case 'email_type':
										echo '<td class="wc-email-settings-table-' . esc_attr( $key ) . '">
										' . esc_html( $email->get_content_type() ) . '
									</td>';
										break;
									case 'actions':
										echo '<td class="wc-email-settings-table-' . esc_attr( $key ) . '">
										<a class="button alignright" href="' . esc_url( admin_url( 'admin.php?page=wc-settings&tab=email&section=' . strtolower( $email_key ) ) ) . '">' . esc_html__( 'Manage', 'poocommerce' ) . '</a>
									</td>';
										break;
									default:
										do_action( 'poocommerce_email_setting_column_' . $key, $email );
										break;
								}
							}

							echo '</tr>';
						}
						?>
					</tbody>
				</table>
			</td>
		</tr>
		<?php
	}

	/**
	 * Creates the React mount point for the email preview.
	 */
	public function email_preview() {
		$this->delete_transient_email_settings( null );
		$emails      = WC()->mailer()->get_emails();
		$email_types = array();
		foreach ( $emails as $type => $email ) {
			$email_types[] = array(
				'label' => $email->get_title(),
				'value' => $type,
			);
		}
		?>
		<div
			id="wc_settings_email_preview_slotfill"
			data-preview-url="<?php echo esc_url( wp_nonce_url( admin_url( '?preview_poocommerce_mail=true' ), 'preview-mail' ) ); ?>"
			data-email-types="<?php echo esc_attr( wp_json_encode( $email_types ) ); ?>"
			data-email-settings-ids="<?php echo esc_attr( wp_json_encode( EmailPreview::get_email_style_settings_ids() ) ); ?>"
		></div>
		<?php
	}

	/**
	 * Creates the React mount point for the single email preview.
	 *
	 * @param object $email The email object to run the method on.
	 */
	public function email_preview_single( $email ) {
		$this->delete_transient_email_settings( $email->id );
		// Email types array should have a single entry for current email.
		$email_types = array(
			array(
				'label' => $email->get_title(),
				'value' => get_class( $email ),
			),
		);
		?>
		<h2><?php echo esc_html( __( 'Email preview', 'poocommerce' ) ); ?></h2>

		<p><?php echo esc_html( __( 'Preview your email template. You can also test on different devices and send yourself a test email.', 'poocommerce' ) ); ?></p>
		<div>
			<div
				id="wc_settings_email_preview_slotfill"
				data-preview-url="<?php echo esc_url( wp_nonce_url( admin_url( '?preview_poocommerce_mail=true' ), 'preview-mail' ) ); ?>"
				data-email-types="<?php echo esc_attr( wp_json_encode( $email_types ) ); ?>"
				data-email-settings-ids="<?php echo esc_attr( wp_json_encode( EmailPreview::get_email_content_settings_ids( $email->id ) ) ); ?>"
			></div>
			<input type="hidden" id="poocommerce_email_from_name" value="<?php echo esc_attr( get_option( 'poocommerce_email_from_name' ) ); ?>" />
			<input type="hidden" id="poocommerce_email_from_address" value="<?php echo esc_attr( get_option( 'poocommerce_email_from_address' ) ); ?>" />
		</div>
		<?php
	}

	/**
	 * Deletes transient with email settings used for live preview. This is to
	 * prevent conflicts where the preview would show values from previous session.
	 *
	 * @param string|null $email_id Email ID.
	 */
	private function delete_transient_email_settings( ?string $email_id ) {
		$setting_ids = array_merge(
			EmailPreview::get_email_style_settings_ids(),
			EmailPreview::get_email_content_settings_ids( $email_id ),
		);
		foreach ( $setting_ids as $id ) {
			delete_transient( $id );
		}
	}

	/**
	 * Creates the React mount point for the email image url.
	 *
	 * @param array $value Field value array.
	 */
	public function email_image_url( $value ) {
		$option_value = $value['value'];
		if ( ! isset( $value['field_name'] ) ) {
			$value['field_name'] = $value['id'];
		}
		?>
		<tr class="<?php echo esc_attr( $value['row_class'] ); ?>">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?> <?php echo wc_help_tip( $value['desc'] ); // WPCS: XSS ok. ?></label>
			</th>
			<td class="forminp forminp-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?>">
				<input
					name="<?php echo esc_attr( $value['field_name'] ); ?>"
					id="<?php echo esc_attr( $value['id'] ); ?>"
					type="hidden"
					value="<?php echo esc_attr( $option_value ); ?>"
				/>
				<div
					id="wc_settings_email_image_url_slotfill"
					data-id="<?php echo esc_attr( $value['id'] ); ?>"
					data-image-url="<?php echo esc_attr( $option_value ); ?>"
				></div>
			</td>
		</tr>
		<?php
	}

	/**
	 * Sanitize email image URL.
	 *
	 * @param  string $value     Option value.
	 * @param  array  $option    Option name.
	 * @param  string $raw_value Raw value.
	 * @return string
	 */
	public function sanitize_email_header_image( $value, $option, $raw_value ) {
		return sanitize_url( $raw_value );
	}

	/**
	 * Creates the email font family field with custom font family applied to each option.
	 *
	 * @param array $value Field value array.
	 */
	public function email_font_family( $value ) {
		$option_value = $value['value'];
		$custom_fonts = $this->get_custom_fonts();

		?>
		<tr class="<?php echo esc_attr( $value['row_class'] ); ?>">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?></label>
			</th>
			<td class="forminp forminp-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?>">
			<script type="text/javascript">
				function renderWithFont( node ) {
					if ( ! node.element || ! node.element.value ) return node.text;
					var $wrapper = jQuery( '<span></span>' );
					$wrapper.css( {'font-family': node.element.dataset['font-family'] || node.element.value} );
					$wrapper.text( node.text );
					return $wrapper;
				}
				function fontsSelect( selector ) {
					jQuery( selector ).selectWoo( {
						minimumResultsForSearch: Infinity,
						templateResult: renderWithFont
					} );
				}
				jQuery( document.body )
					.on( 'wc-enhanced-select-init', function() {
						fontsSelect( '#<?php echo esc_js( $value['id'] ); ?>' );
					} );
				</script>
				<select
					name="<?php echo esc_attr( $value['field_name'] ); ?>"
					id="<?php echo esc_attr( $value['id'] ); ?>"
					>
					<optgroup label="<?php echo esc_attr__( 'Standard fonts', 'poocommerce' ); ?>">
						<?php
						foreach ( self::$font as $key => $font_family ) {
							?>
							<option
								value="<?php echo esc_attr( $key ); ?>"
								data-font-family="<?php echo esc_attr( $font_family ); ?>"
								<?php selected( $option_value, (string) $key ); ?>
							><?php echo esc_html( $key ); ?></option>
							<?php
						}
						?>
					</optgroup>
					<?php if ( $custom_fonts ) : ?>
						<optgroup label="<?php echo esc_attr__( 'Custom fonts', 'poocommerce' ); ?>">
							<?php
							foreach ( $custom_fonts as $key => $val ) {
								?>
							<option
								value="<?php echo esc_attr( $key ); ?>"
								<?php selected( $option_value, (string) $key ); ?>
							><?php echo esc_html( $val ); ?></option>
								<?php
							}
							?>
						</optgroup>
					<?php endif; ?>
				</select>
			</td>
		</tr>
		<?php
	}

	/**
	 * Creates the React mount point for the email color palette title.
	 *
	 * @param array $value Field value array.
	 */
	public function email_color_palette( $value ) {
		$default_colors = $this->get_email_default_colors();

		?>
		<hr class="wc-settings-email-color-palette-separator" />
		<h2 class="wc-settings-email-color-palette-title"><?php echo esc_html( $value['title'] ); ?></h2>
		<div
			class="wc-settings-email-color-palette-buttons"
			id="wc_settings_email_color_palette_slotfill"
			data-default-colors="<?php echo esc_attr( wp_json_encode( $default_colors ) ); ?>"
			<?php echo wp_theme_has_theme_json() ? 'data-has-theme-json' : ''; ?>
		></div>
		<table class="form-table">
		<?php
	}
}

return new WC_Settings_Emails();
