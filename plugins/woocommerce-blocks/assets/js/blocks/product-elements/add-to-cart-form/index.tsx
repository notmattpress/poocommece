/**
 * External dependencies
 */
import { registerBlockSingleProductTemplate } from '@poocommerce/atomic-utils';
import { Icon, button } from '@wordpress/icons';

/**
 * Internal dependencies
 */
import metadata from './block.json';
import AddToCartFormEdit from './edit';
import './style.scss';
import './editor.scss';
import '../../../base/components/quantity-selector/style.scss';

const blockSettings = {
	edit: AddToCartFormEdit,
	icon: {
		src: (
			<Icon
				icon={ button }
				className="wc-block-editor-components-block-icon"
			/>
		),
	},
	ancestor: [ 'poocommerce/single-product' ],
	save() {
		return null;
	},
};

registerBlockSingleProductTemplate( {
	blockName: metadata.name,
	blockMetadata: metadata,
	blockSettings,
	isAvailableOnPostEditor: true,
} );
