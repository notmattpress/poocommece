<?php
/**
 * The template for displaying product search form
 *
 * This template can be overridden by copying it to yourtheme/poocommerce/product-searchform.php.
 *
 * HOWEVER, on occasion PooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://poocommerce.com/document/template-structure/
 * @package PooCommerce\Templates
 * @version 7.0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<form role="search" method="get" class="poocommerce-product-search" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="screen-reader-text" for="poocommerce-product-search-field-<?php echo isset( $index ) ? absint( $index ) : 0; ?>"><?php esc_html_e( 'Search for:', 'poocommerce' ); ?></label>
	<input type="search" id="poocommerce-product-search-field-<?php echo isset( $index ) ? absint( $index ) : 0; ?>" class="search-field" placeholder="<?php echo esc_attr__( 'Search products&hellip;', 'poocommerce' ); ?>" value="<?php echo get_search_query(); ?>" name="s" />
	<button type="submit" value="<?php echo esc_attr_x( 'Search', 'submit button', 'poocommerce' ); ?>" class="<?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ); ?>"><?php echo esc_html_x( 'Search', 'submit button', 'poocommerce' ); ?></button>
	<input type="hidden" name="post_type" value="product" />
</form>
