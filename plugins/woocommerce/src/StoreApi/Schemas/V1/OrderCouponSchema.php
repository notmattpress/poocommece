<?php
namespace Automattic\PooCommerce\StoreApi\Schemas\V1;

/**
 * OrderCouponSchema class.
 */
class OrderCouponSchema extends AbstractSchema {
	/**
	 * The schema item name.
	 *
	 * @var string
	 */
	protected $title = 'order_coupon';

	/**
	 * The schema item identifier.
	 *
	 * @var string
	 */
	const IDENTIFIER = 'order-coupon';

	/**
	 * Cart schema properties.
	 *
	 * @return array
	 */
	public function get_properties() {
		return [
			'code'          => [
				'description' => __( 'The coupons unique code.', 'poocommerce' ),
				'type'        => 'string',
				'context'     => [ 'view', 'edit' ],
				'readonly'    => true,
			],
			'discount_type' => [
				'description' => __( 'The discount type for the coupon (e.g. percentage or fixed amount)', 'poocommerce' ),
				'type'        => 'string',
				'context'     => [ 'view', 'edit' ],
				'readonly'    => true,
			],
			'totals'        => [
				'description' => __( 'Total amounts provided using the smallest unit of the currency.', 'poocommerce' ),
				'type'        => 'object',
				'context'     => [ 'view', 'edit' ],
				'readonly'    => true,
				'properties'  => array_merge(
					$this->get_store_currency_properties(),
					[
						'total_discount'     => [
							'description' => __( 'Total discount applied by this coupon.', 'poocommerce' ),
							'type'        => 'string',
							'context'     => [ 'view', 'edit' ],
							'readonly'    => true,
						],
						'total_discount_tax' => [
							'description' => __( 'Total tax removed due to discount applied by this coupon.', 'poocommerce' ),
							'type'        => 'string',
							'context'     => [ 'view', 'edit' ],
							'readonly'    => true,
						],
					]
				),
			],
		];
	}

	/**
	 * Convert an order coupon to an object suitable for the response.
	 *
	 * @param \WC_Order_Item_Coupon $coupon Order coupon object.
	 * @return array
	 */
	public function get_item_response( $coupon ) {
		$coupon_object = new \WC_Coupon( $coupon->get_code() );
		return [
			'code'          => $coupon->get_code(),
			'discount_type' => $coupon_object ? $coupon_object->get_discount_type() : '',
			'totals'        => (object) $this->prepare_currency_response(
				[
					'total_discount'     => $this->prepare_money_response( $coupon->get_discount(), wc_get_price_decimals() ),
					'total_discount_tax' => $this->prepare_money_response( $coupon->get_discount_tax(), wc_get_price_decimals(), PHP_ROUND_HALF_DOWN ),
				]
			),
		];
	}
}
