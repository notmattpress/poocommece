<?php
/**
 * PooCommerce Settings Page/Tab
 *
 * @package     PooCommerce\Admin
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WC_Settings_Page', false ) ) :

	/**
	 * WC_Settings_Page.
	 */
	abstract class WC_Settings_Page {

		/**
		 * Setting page id.
		 *
		 * @var string
		 */
		protected $id = '';

		/**
		 * Setting page icon.
		 *
		 * @var string
		 */
		public $icon = 'settings';

		/**
		 * Setting page label.
		 *
		 * @var string
		 */
		protected $label = '';

		/**
		 * Setting page is modern.
		 *
		 * @var bool
		 */
		protected $is_modern = false;

		/**
		 * Constructor.
		 */
		public function __construct() {
			add_filter( 'poocommerce_settings_tabs_array', array( $this, 'add_settings_page' ), 20 );
			add_action( 'poocommerce_sections_' . $this->id, array( $this, 'output_sections' ) );
			add_action( 'poocommerce_settings_' . $this->id, array( $this, 'output' ) );
			add_action( 'poocommerce_settings_save_' . $this->id, array( $this, 'save' ) );
			add_action( 'poocommerce_admin_field_add_settings_slot', array( $this, 'add_settings_slot' ) );
		}

		/**
		 * Get settings page ID.
		 *
		 * @since 3.0.0
		 * @return string
		 */
		public function get_id() {
			return $this->id;
		}

		/**
		 * Get settings page label.
		 *
		 * @since 3.0.0
		 * @return string
		 */
		public function get_label() {
			return $this->label;
		}

		/**
		 * Creates the React mount point for settings slot.
		 */
		public function add_settings_slot() {
			?>
			<div id="wc_settings_slotfill"> </div>
			<?php
		}

		/**
		 * Add this page to settings.
		 *
		 * @param array $pages The settings array where we'll add ourselves.
		 *
		 * @return mixed
		 */
		public function add_settings_page( $pages ) {
			$pages[ $this->id ] = $this->label;

			return $pages;
		}

		/**
		 * Get page settings data to populate the settings editor.
		 *
		 * @param array $pages The settings array where we'll add data.
		 *
		 * @return array
		 */
		public function add_settings_page_data( $pages ) {
			$sections      = $this->get_sections();
			$sections_data = array();

			// Loop through each section and get the settings for that section.
			foreach ( $sections as $section_id => $section_label ) {
				$section_settings = count( $sections ) > 1
					? $this->get_settings_for_section( $section_id )
					: $this->get_settings();

				$section_settings_data = array();

				// Loop through each setting in the section and add the value to the settings data.
				foreach ( $section_settings as $section_setting ) {
					if ( isset( $section_setting['id'] ) ) {
						$section_setting['value'] = isset( $section_setting['default'] )
							// Fallback to the default value if it exists.
							? get_option( $section_setting['id'], $section_setting['default'] )
							// Otherwise, fallback to false.
							: get_option( $section_setting['id'] );
					}

					$section_settings_data[] = $section_setting;
				}

				// Replace empty string section ids with 'default'.
				$section_id = '' === $section_id ? 'default' : $section_id;

				$sections_data[ $section_id ] = array(
					'label'    => html_entity_decode( $section_label ),
					'settings' => $section_settings_data,
				);
			}

			$pages[ $this->id ] = array(
				'label'     => html_entity_decode( $this->label ),
				'slug'      => $this->id,
				'icon'      => $this->icon,
				'sections'  => $sections_data,
				'is_modern' => $this->is_modern,
			);

			return $pages;
		}

		/**
		 * Get settings array for the default section.
		 *
		 * External settings classes (registered via 'poocommerce_get_settings_pages' filter)
		 * might have redefined this method as "get_settings($section_id='')", thus we need
		 * to use this method internally instead of 'get_settings_for_section' to register settings
		 * and render settings pages.
		 *
		 * *But* we can't just redefine the method as "get_settings($section_id='')" here, since this
		 * will break on PHP 8 if any external setting class have it as 'get_settings()'.
		 *
		 * Thus we leave the method signature as is and use 'func_get_arg' to get the setting id
		 * if it's supplied, and we use this method internally; but it's deprecated and should
		 * otherwise never be used.
		 *
		 * @deprecated 5.4.0 Use 'get_settings_for_section' (passing an empty string for default section)
		 *
		 * @return array Settings array, each item being an associative array representing a setting.
		 */
		public function get_settings() {
			$section_id = 0 === func_num_args() ? '' : func_get_arg( 0 );
			return $this->get_settings_for_section( $section_id );
		}

		/**
		 * Get settings array.
		 *
		 * The strategy for getting the settings is as follows:
		 *
		 * - If a method named 'get_settings_for_{section_id}_section' exists in the class
		 *   it will be invoked (for the default '' section, the method name is 'get_settings_for_default_section').
		 *   Derived classes can implement these methods as required.
		 *
		 * - Otherwise, 'get_settings_for_section_core' will be invoked. Derived classes can override it
		 *   as an alternative to implementing 'get_settings_for_{section_id}_section' methods.
		 *
		 * @param string $section_id The id of the section to return settings for, an empty string for the default section.
		 *
		 * @return array Settings array, each item being an associative array representing a setting.
		 */
		final public function get_settings_for_section( $section_id ) {
			if ( '' === $section_id ) {
				$method_name = 'get_settings_for_default_section';
			} else {
				$method_name = "get_settings_for_{$section_id}_section";
			}

			if ( method_exists( $this, $method_name ) ) {
				$settings = $this->$method_name();
			} else {
				$settings = $this->get_settings_for_section_core( $section_id );
			}

			return apply_filters( 'poocommerce_get_settings_' . $this->id, $settings, $section_id );
		}

		/**
		 * Get the settings for a given section.
		 * This method is invoked from 'get_settings_for_section' when no 'get_settings_for_{current_section}_section'
		 * method exists in the class.
		 *
		 * When overriding, note that the 'poocommerce_get_settings_' filter must NOT be triggered,
		 * as this is already done by 'get_settings_for_section'.
		 *
		 * @param string $section_id The section name to get the settings for.
		 *
		 * @return array Settings array, each item being an associative array representing a setting.
		 */
		protected function get_settings_for_section_core( $section_id ) {
			return array();
		}

		/**
		 * Get all sections for this page, both the own ones and the ones defined via filters.
		 *
		 * @return array
		 */
		public function get_sections() {
			$sections = $this->get_own_sections();
			/**
			 * Filters the sections for this settings page.
			 *
			 * @since 2.2.0
			 * @param array $sections The sections for this settings page.
			 */
			return (array) apply_filters( 'poocommerce_get_sections_' . $this->id, $sections );
		}

		/**
		 * Get own sections for this page.
		 * Derived classes should override this method if they define sections.
		 * There should always be one default section with an empty string as identifier.
		 *
		 * Example:
		 * return array(
		 *   ''        => __( 'General', 'poocommerce' ),
		 *   'foobars' => __( 'Foos & Bars', 'poocommerce' ),
		 * );
		 *
		 * @return array An associative array where keys are section identifiers and the values are translated section names.
		 */
		protected function get_own_sections() {
			return array( '' => __( 'General', 'poocommerce' ) );
		}

		/**
		 * Output sections.
		 */
		public function output_sections() {
			global $current_section;

			$sections = $this->get_sections();

			if ( empty( $sections ) || 1 === count( $sections ) ) {
				return;
			}

			echo '<ul class="subsubsub">';

			$array_keys = array_keys( $sections );

			foreach ( $sections as $id => $label ) {
				$url       = admin_url( 'admin.php?page=wc-settings&tab=' . $this->id . '&section=' . sanitize_title( $id ) );
				$class     = ( $current_section === $id ? 'current' : '' );
				$separator = ( end( $array_keys ) === $id ? '' : '|' );
				$text      = esc_html( $label );
				echo "<li><a href='$url' class='$class'>$text</a> $separator </li>"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}

			echo '</ul><br class="clear" />';
		}

		/**
		 * Output the HTML for the settings.
		 */
		public function output() {
			global $current_section;

			// We can't use "get_settings_for_section" here
			// for compatibility with derived classes overriding "get_settings".
			$settings = $this->get_settings( $current_section );

			WC_Admin_Settings::output_fields( $settings );
		}

		/**
		 * Save settings and trigger the 'poocommerce_update_options_'.id action.
		 */
		public function save() {
			$this->save_settings_for_current_section();
			$this->do_update_options_action();
		}

		/**
		 * Save settings for current section.
		 */
		protected function save_settings_for_current_section() {
			global $current_section;

			// We can't use "get_settings_for_section" here
			// for compatibility with derived classes overriding "get_settings".
			$settings = $this->get_settings( $current_section );
			WC_Admin_Settings::save_fields( $settings );
		}

		/**
		 * Trigger the 'poocommerce_update_options_'.id action.
		 *
		 * @param string $section_id Section to trigger the action for, or null for current section.
		 */
		protected function do_update_options_action( $section_id = null ) {
			global $current_section;

			if ( is_null( $section_id ) ) {
				$section_id = $current_section;
			}

			if ( $section_id ) {
				do_action( 'poocommerce_update_options_' . $this->id . '_' . $section_id );
			}
		}
	}

endif;
