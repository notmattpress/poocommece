/**
 * External dependencies
 */
import clsx from 'clsx';
import { useBlockProps } from '@wordpress/block-editor';
import { useCheckoutAddress } from '@poocommerce/base-context/hooks';
import { innerBlockAreas } from '@poocommerce/blocks-checkout';

/**
 * Internal dependencies
 */
import {
	FormStepBlock,
	AdditionalFields,
	AdditionalFieldsContent,
} from '../../form-step';
import { useCheckoutBlockControlsContext } from '../../context';
import Block from './block';

export const Edit = ( {
	attributes,
	setAttributes,
}: {
	attributes: {
		title: string;
		description: string;
		showStepNumber: boolean;
		className: string;
	};
	setAttributes: ( attributes: Record< string, unknown > ) => void;
} ): JSX.Element | null => {
	const { addressFieldControls: Controls } =
		useCheckoutBlockControlsContext();
	const { showShippingFields } = useCheckoutAddress();

	if ( ! showShippingFields ) {
		return null;
	}

	return (
		<FormStepBlock
			setAttributes={ setAttributes }
			attributes={ attributes }
			className={ clsx(
				'wc-block-checkout__shipping-fields',
				attributes?.className
			) }
		>
			<Controls />
			<Block />
			<AdditionalFields block={ innerBlockAreas.SHIPPING_ADDRESS } />
		</FormStepBlock>
	);
};

export const Save = (): JSX.Element => {
	return (
		<div { ...useBlockProps.save() }>
			<AdditionalFieldsContent />
		</div>
	);
};
