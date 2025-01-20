<?php

declare( strict_types = 1 );

namespace Automattic\PooCommerce\Admin\API\AI;

use Automattic\PooCommerce\StoreApi\Exceptions\RouteException;
use WP_Error;

/**
 * Middleware class.
 *
 * @internal
 */
class Middleware {
	/**
	 * Ensure that the user is allowed to make this request.
	 *
	 * @return boolean|WP_Error
	 * @throws RouteException If the user is not allowed to make this request.
	 */
	public static function is_authorized() {
		try {
			if ( ! current_user_can( 'manage_options' ) ) {
				throw new RouteException( 'poocommerce_rest_invalid_user', __( 'You are not allowed to make this request. Please make sure you are logged in.', 'poocommerce' ), 403 );
			}
		} catch ( RouteException $error ) {
			return new WP_Error(
				$error->getErrorCode(),
				$error->getMessage(),
				array( 'status' => $error->getCode() )
			);
		}

		$allow_ai_connection = get_option( 'poocommerce_blocks_allow_ai_connection' );

		if ( ! $allow_ai_connection ) {
			try {
				throw new RouteException( 'ai_connection_not_allowed', __( 'AI content generation is not allowed on this store. Update your store settings if you wish to enable this feature.', 'poocommerce' ), 403 );
			} catch ( RouteException $error ) {
				return new WP_Error(
					$error->getErrorCode(),
					$error->getMessage(),
					array( 'status' => $error->getCode() )
				);
			}
		}

		return true;
	}
}
