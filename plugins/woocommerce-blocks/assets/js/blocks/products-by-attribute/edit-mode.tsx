/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import { Button, Placeholder } from '@wordpress/components';
import { category, Icon } from '@wordpress/icons';
import ProductAttributeTermControl from '@poocommerce/editor-components/product-attribute-term-control';

/**
 * Internal dependencies
 */
import { Props } from './types';

export interface EditModeProps extends Props {
	isEditing: boolean;
	setIsEditing: ( isEditing: boolean ) => void;
}

export const ProductsByAttributeEditMode = (
	props: EditModeProps
): JSX.Element => {
	const {
		attributes: blockAttributes,
		setAttributes,
		setIsEditing,
		isEditing,
		debouncedSpeak,
	} = props;

	const onDone = () => {
		setIsEditing( ! isEditing );
		debouncedSpeak(
			__( 'Showing Products by Attribute block preview.', 'poocommerce' )
		);
	};

	return (
		<Placeholder
			icon={ <Icon icon={ category } /> }
			label={ __( 'Products by Attribute', 'poocommerce' ) }
			className="wc-block-products-grid wc-block-products-by-attribute"
		>
			{ __(
				'Display a grid of products from your selected attributes.',
				'poocommerce'
			) }
			<div className="wc-block-products-by-attribute__selection">
				<ProductAttributeTermControl
					selected={ blockAttributes.attributes }
					onChange={ ( value = [] ) => {
						const result = value.map(
							( { id, value: attributeSlug } ) => ( {
								id,
								attr_slug: attributeSlug,
							} )
						);
						setAttributes( { attributes: result } );
					} }
					operator={ blockAttributes.attrOperator }
					onOperatorChange={ ( value = 'any' ) =>
						setAttributes( { attrOperator: value } )
					}
				/>
				<Button variant="primary" onClick={ onDone }>
					{ __( 'Done', 'poocommerce' ) }
				</Button>
			</div>
		</Placeholder>
	);
};
