/**
 * External dependencies
 */

import { decodeEntities } from '@wordpress/html-entities';
import { Button } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { WooPaymentMethodsLogos } from '@poocommerce/onboarding';
import { PaymentProvider } from '@poocommerce/data';

/**
 * Internal dependencies
 */
import sanitizeHTML from '~/lib/sanitize-html';
import { EllipsisMenuWrapper as EllipsisMenu } from '~/settings-payments/components/ellipsis-menu-content';
import { isWooPayments } from '~/settings-payments/utils';
import { DefaultDragHandle } from '~/settings-payments/components/sortable';
import { StatusBadge } from '~/settings-payments/components/status-badge';

type PaymentExtensionSuggestionListItemProps = {
	extension: PaymentProvider;
	installingPlugin: string | null;
	setupPlugin: ( id: string, slug: string ) => void;
	pluginInstalled: boolean;
};

export const PaymentExtensionSuggestionListItem = ( {
	extension,
	installingPlugin,
	setupPlugin,
	pluginInstalled,
	...props
}: PaymentExtensionSuggestionListItemProps ) => {
	return (
		<div
			id={ extension.id }
			className={ `transitions-disabled poocommerce-list__item poocommerce-list__item-enter-done` }
			{ ...props }
		>
			<div className="poocommerce-list__item-inner">
				<div className="poocommerce-list__item-before">
					<DefaultDragHandle />
					<img
						src={ extension.icon }
						alt={ extension.title + ' logo' }
					/>
				</div>
				<div className="poocommerce-list__item-text">
					<span className="poocommerce-list__item-title">
						{ extension.title }{ ' ' }
						{ isWooPayments( extension.id ) && (
							<StatusBadge status="recommended" />
						) }
					</span>
					<span
						className="poocommerce-list__item-content"
						dangerouslySetInnerHTML={ sanitizeHTML(
							decodeEntities( extension.description )
						) }
					/>
					{ isWooPayments( extension.id ) && (
						<WooPaymentMethodsLogos
							maxElements={ 10 }
							isWooPayEligible={ true }
						/>
					) }
				</div>
				<div className="poocommerce-list__item-after">
					<div className="poocommerce-list__item-after__actions">
						<Button
							variant="primary"
							onClick={ () =>
								setupPlugin(
									extension.id,
									extension.plugin.slug
								)
							}
							isBusy={ installingPlugin === extension.id }
							disabled={ !! installingPlugin }
						>
							{ pluginInstalled
								? __( 'Enable', 'poocommerce' )
								: __( 'Install', 'poocommerce' ) }
						</Button>

						<EllipsisMenu
							label={ __(
								'Payment Provider Options',
								'poocommerce'
							) }
							provider={ extension }
						/>
					</div>
				</div>
			</div>
		</div>
	);
};
