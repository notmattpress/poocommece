/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import clsx from 'clsx';
import {
	InnerBlocks,
	useBlockProps,
	InspectorControls,
} from '@wordpress/block-editor';
import { SidebarLayout } from '@poocommerce/base-components/sidebar-layout';
import { CheckoutProvider, EditorProvider } from '@poocommerce/base-context';
import {
	previewCart,
	previewSavedPaymentMethods,
} from '@poocommerce/resource-previews';
import { PanelBody, ToggleControl, RadioControl } from '@wordpress/components';
import { SlotFillProvider } from '@poocommerce/blocks-checkout';
import type { TemplateArray } from '@wordpress/blocks';
import { useEffect, useRef, useState } from '@wordpress/element';
import { getQueryArg } from '@wordpress/url';
import { dispatch, select as selectData, useSelect } from '@wordpress/data';
import { store as coreStore } from '@wordpress/core-data';
import { defaultFields as defaultFieldsSetting } from '@poocommerce/settings';

/**
 * Internal dependencies
 */
import './inner-blocks';
import './styles/editor.scss';
import {
	addClassToBody,
	BlockSettings,
	useBlockPropsWithLocking,
} from '../cart-checkout-shared';
import '../cart-checkout-shared/sidebar-notices';
import { CheckoutBlockContext, CheckoutBlockControlsContext } from './context';
import type { Attributes } from './types';

// This is adds a class to body to signal if the selected block is locked
addClassToBody();

// Array of allowed block names.
const ALLOWED_BLOCKS: string[] = [
	'poocommerce/checkout-fields-block',
	'poocommerce/checkout-totals-block',
];

export const Edit = ( {
	clientId,
	attributes,
	setAttributes,
}: {
	clientId: string;
	attributes: Attributes;
	setAttributes: ( attributes: Record< string, unknown > ) => undefined;
} ): JSX.Element => {
	const {
		showOrderNotes,
		showPolicyLinks,
		showReturnToCart,
		showRateAfterTaxName,
		cartPageId,
		isPreview = false,
		showFormStepNumbers = false,
	} = attributes;

	const defaultFields = useSelect( ( select ) => {
		const settings = select(
			coreStore as unknown as string
		).getEditedEntityRecord( 'root', 'site' ) as Record< string, string >;

		const fieldsWithDefaults = {
			phone: 'optional',
			company: 'hidden',
			address_2: 'optional',
		} as const;

		return {
			...defaultFieldsSetting,
			...Object.fromEntries(
				Object.entries( fieldsWithDefaults ).map(
					( [ field, defaultValue ] ) => {
						const value =
							settings[
								`poocommerce_checkout_${ field }_field`
							] || defaultValue;
						return [
							field,
							{
								...defaultFieldsSetting[ field ],
								required: value === 'required',
								hidden: value === 'hidden',
							},
						];
					}
				)
			),
		};
	} );

	// These state objects are in place so the required toggles remember their last value after being hidden and shown.
	const [ lastPhoneRequired, setLastPhoneRequired ] = useState(
		defaultFields.phone.required
	);
	const [ lastCompanyRequired, setLastCompanyRequired ] = useState(
		defaultFields.company.required
	);
	const [ lastAddress2Required, setLastAddress2Required ] = useState(
		defaultFields.address_2.required
	);

	useEffect( () => {
		if ( ! defaultFields.phone.hidden ) {
			setLastPhoneRequired( defaultFields.phone.required );
		}
		if ( ! defaultFields.company.hidden ) {
			setLastCompanyRequired( defaultFields.company.required );
		}
		if ( ! defaultFields.address_2.hidden ) {
			setLastAddress2Required( defaultFields.address_2.required );
		}
	}, [
		defaultFields.phone.hidden,
		defaultFields.company.hidden,
		defaultFields.address_2.hidden,
		defaultFields.phone.required,
		defaultFields.company.required,
		defaultFields.address_2.required,
	] );

	const setFieldEntity = ( field: string, value: string ) => {
		if (
			[ 'phone', 'company', 'address_2' ].includes( field ) &&
			[ 'optional', 'required', 'hidden' ].includes( value )
		) {
			dispatch( coreStore as unknown as string ).editEntityRecord(
				'root',
				'site',
				undefined,
				{
					[ `poocommerce_checkout_${ field }_field` ]: value,
				}
			);
		}
	};

	// This focuses on the block when a certain query param is found. This is used on the link from the task list.
	const focus = useRef( getQueryArg( window.location.href, 'focus' ) );

	useEffect( () => {
		if (
			focus.current === 'checkout' &&
			! selectData( 'core/block-editor' ).hasSelectedBlock()
		) {
			dispatch( 'core/block-editor' ).selectBlock( clientId );
			dispatch( 'core/interface' ).enableComplementaryArea(
				'core/edit-site',
				'edit-site/block-inspector'
			);
		}
	}, [ clientId ] );

	const defaultTemplate = [
		[ 'poocommerce/checkout-totals-block', {}, [] ],
		[ 'poocommerce/checkout-fields-block', {}, [] ],
	] as TemplateArray;

	const requiredOptions = [
		{
			label: __( 'Optional', 'poocommerce' ),
			value: 'false',
		},
		{
			label: __( 'Required', 'poocommerce' ),
			value: 'true',
		},
	];

	const addressFieldControls = (): JSX.Element => (
		<InspectorControls>
			<PanelBody title={ __( 'Form Step Options', 'poocommerce' ) }>
				<ToggleControl
					label={ __( 'Show form step numbers', 'poocommerce' ) }
					checked={ showFormStepNumbers }
					onChange={ () =>
						setAttributes( {
							showFormStepNumbers: ! showFormStepNumbers,
						} )
					}
				/>
			</PanelBody>
			<PanelBody title={ __( 'Address Fields', 'poocommerce' ) }>
				<p className="wc-block-checkout__controls-text">
					{ __(
						'Show or hide fields in the checkout address forms.',
						'poocommerce'
					) }
				</p>
				<ToggleControl
					label={ __( 'Company', 'poocommerce' ) }
					checked={ ! defaultFields.company.hidden }
					onChange={ () => {
						if ( defaultFields.company.hidden ) {
							setFieldEntity(
								'company',
								lastCompanyRequired ? 'required' : 'optional'
							);
						} else {
							setFieldEntity( 'company', 'hidden' );
						}
					} }
				/>
				{ ! defaultFields.company.hidden && (
					<RadioControl
						selected={
							defaultFields.company.required ? 'true' : 'false'
						}
						options={ requiredOptions }
						onChange={ ( value: string ) => {
							setFieldEntity(
								'company',
								value === 'true' ? 'required' : 'optional'
							);
						} }
						className="components-base-control--nested wc-block-components-require-company-field"
					/>
				) }
				<ToggleControl
					label={ __( 'Address line 2', 'poocommerce' ) }
					checked={ ! defaultFields.address_2.hidden }
					onChange={ () => {
						if ( defaultFields.address_2.hidden ) {
							setFieldEntity(
								'address_2',
								lastAddress2Required ? 'required' : 'optional'
							);
						} else {
							setFieldEntity( 'address_2', 'hidden' );
						}
					} }
				/>
				{ ! defaultFields.address_2.hidden && (
					<RadioControl
						selected={
							defaultFields.address_2.required ? 'true' : 'false'
						}
						options={ requiredOptions }
						onChange={ ( value: string ) => {
							setFieldEntity(
								'address_2',
								value === 'true' ? 'required' : 'optional'
							);
						} }
						className="components-base-control--nested wc-block-components-require-address_2-field"
					/>
				) }
				<ToggleControl
					label={ __( 'Phone', 'poocommerce' ) }
					checked={ ! defaultFields.phone.hidden }
					onChange={ () => {
						if ( defaultFields.phone.hidden ) {
							setFieldEntity(
								'phone',
								lastPhoneRequired ? 'required' : 'optional'
							);
						} else {
							setFieldEntity( 'phone', 'hidden' );
						}
					} }
				/>
				{ ! defaultFields.phone.hidden && (
					<RadioControl
						selected={
							defaultFields.phone.required ? 'true' : 'false'
						}
						options={ requiredOptions }
						onChange={ ( value: string ) => {
							setFieldEntity(
								'phone',
								value === 'true' ? 'required' : 'optional'
							);
						} }
						className="components-base-control--nested wc-block-components-require-phone-field"
					/>
				) }
			</PanelBody>
		</InspectorControls>
	);
	const blockProps = useBlockPropsWithLocking();
	return (
		<div { ...blockProps }>
			<InspectorControls>
				<BlockSettings
					attributes={ attributes }
					setAttributes={ setAttributes }
				/>
			</InspectorControls>
			<EditorProvider
				isPreview={ !! isPreview }
				previewData={ {
					previewCart,
					previewSavedPaymentMethods,
					defaultFields,
				} }
			>
				<SlotFillProvider>
					<CheckoutProvider>
						<SidebarLayout
							className={ clsx( 'wc-block-checkout', {
								'has-dark-controls': attributes.hasDarkControls,
							} ) }
						>
							<CheckoutBlockControlsContext.Provider
								value={ { addressFieldControls } }
							>
								<CheckoutBlockContext.Provider
									value={ {
										showOrderNotes,
										showPolicyLinks,
										showReturnToCart,
										cartPageId,
										showRateAfterTaxName,
										showFormStepNumbers,
									} }
								>
									<InnerBlocks
										allowedBlocks={ ALLOWED_BLOCKS }
										template={ defaultTemplate }
										templateLock="insert"
									/>
								</CheckoutBlockContext.Provider>
							</CheckoutBlockControlsContext.Provider>
						</SidebarLayout>
					</CheckoutProvider>
				</SlotFillProvider>
			</EditorProvider>
		</div>
	);
};

export const Save = (): JSX.Element => {
	return (
		<div
			{ ...useBlockProps.save( {
				className: 'wc-block-checkout is-loading',
			} ) }
		>
			<InnerBlocks.Content />
		</div>
	);
};
