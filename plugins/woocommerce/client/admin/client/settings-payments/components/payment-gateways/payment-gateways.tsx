/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import { apiFetch } from '@wordpress/data-controls';
import {
	PaymentProvider,
	PAYMENT_SETTINGS_STORE_NAME,
	WC_ADMIN_NAMESPACE,
} from '@poocommerce/data';
import { useDispatch } from '@wordpress/data';
import { useMemo } from '@wordpress/element';
import { decodeEntities } from '@wordpress/html-entities';
/**
 * Internal dependencies
 */
import { CountrySelector } from '~/settings-payments/components/country-selector';
import { ListPlaceholder } from '~/settings-payments/components/list-placeholder';
import { PaymentGatewayList } from '~/settings-payments/components/payment-gateway-list';
import './payment-gateways.scss';

interface PaymentGatewaysProps {
	providers: PaymentProvider[];
	installedPluginSlugs: string[];
	installingPlugin: string | null;
	setupPlugin: ( id: string, slug: string ) => void;
	updateOrdering: ( providers: PaymentProvider[] ) => void;
	isFetching: boolean;
	businessRegistrationCountry: string | null;
	setBusinessRegistrationCountry: ( country: string ) => void;
}

export const PaymentGateways = ( {
	providers,
	installedPluginSlugs,
	installingPlugin,
	setupPlugin,
	updateOrdering,
	isFetching,
	businessRegistrationCountry,
	setBusinessRegistrationCountry,
}: PaymentGatewaysProps ) => {
	const { invalidateResolution } = useDispatch( PAYMENT_SETTINGS_STORE_NAME );

	const countryOptions = useMemo( () => {
		return Object.entries( window.wcSettings.countries || [] )
			.map( ( [ key, name ] ) => ( {
				key,
				name: decodeEntities( name ),
				types: [],
			} ) )
			.sort( ( a, b ) => a.name.localeCompare( b.name ) );
	}, [] );

	return (
		<div className="settings-payment-gateways">
			<div className="settings-payment-gateways__header">
				<div className="settings-payment-gateways__header-title">
					{ __( 'Payment providers', 'poocommerce' ) }
				</div>
				<div className="settings-payment-gateways__header-select-container">
					<CountrySelector
						className="poocommerce-select-control__country"
						label={ __( 'Business location :', 'poocommerce' ) }
						placeholder={ '' }
						value={
							countryOptions.find(
								( country ) =>
									country.key === businessRegistrationCountry
							) ?? { key: 'US', name: 'United States (US)' }
						}
						options={ countryOptions }
						onChange={ ( value: string ) => {
							setBusinessRegistrationCountry( value );
							invalidateResolution( 'getPaymentProviders', [
								value,
							] );
							apiFetch( {
								path:
									WC_ADMIN_NAMESPACE +
									'/settings/payments/country',
								method: 'POST',
								data: { location: value },
							} );
						} }
					/>
				</div>
			</div>
			{ isFetching ? (
				<ListPlaceholder rows={ 5 } />
			) : (
				<PaymentGatewayList
					providers={ providers }
					installedPluginSlugs={ installedPluginSlugs }
					installingPlugin={ installingPlugin }
					setupPlugin={ setupPlugin }
					updateOrdering={ updateOrdering }
				/>
			) }
		</div>
	);
};
