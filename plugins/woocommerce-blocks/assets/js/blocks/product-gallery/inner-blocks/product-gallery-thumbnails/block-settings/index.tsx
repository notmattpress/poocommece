/**
 * External dependencies
 */
import { store as blockEditorStore } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';
import { Icon } from '@wordpress/icons';
import {
	thumbnailsPositionLeft,
	thumbnailsPositionBottom,
	thumbnailsPositionRight,
} from '@poocommerce/icons';
import { useDispatch } from '@wordpress/data';
import {
	RangeControl,
	// eslint-disable-next-line @typescript-eslint/ban-ts-comment
	// @ts-ignore - Ignoring because `__experimentalToggleGroupControlOption` is not yet in the type definitions.
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis
	__experimentalToggleGroupControlOption as ToggleGroupControlOption,
	// eslint-disable-next-line @typescript-eslint/ban-ts-comment
	// @ts-ignore - Ignoring because `__experimentalToggleGroupControl` is not yet in the type definitions.
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis
	__experimentalToggleGroupControl as ToggleGroupControl,
} from '@wordpress/components';

/**
 * Internal dependencies
 */
import { ThumbnailsPosition } from '../constants';
import type { ProductGalleryThumbnailsSettingsProps } from '../../../types';

const positionHelp: Record< ThumbnailsPosition, string > = {
	[ ThumbnailsPosition.OFF ]: __(
		'No thumbnails will be displayed.',
		'poocommerce'
	),
	[ ThumbnailsPosition.LEFT ]: __(
		'A strip of small images will appear to the left of the main gallery image.',
		'poocommerce'
	),
	[ ThumbnailsPosition.BOTTOM ]: __(
		'A strip of small images will appear below the main gallery image.',
		'poocommerce'
	),
	[ ThumbnailsPosition.RIGHT ]: __(
		'A strip of small images will appear to the right of the main gallery image.',
		'poocommerce'
	),
};

export const ProductGalleryThumbnailsBlockSettings = ( {
	context,
}: ProductGalleryThumbnailsSettingsProps ) => {
	const maxNumberOfThumbnails = 8;
	const minNumberOfThumbnails = 3;
	const { productGalleryClientId } = context;
	// @ts-expect-error @wordpress/block-editor/store types not provided
	const { updateBlockAttributes } = useDispatch( blockEditorStore );

	return (
		<>
			<ToggleGroupControl
				className="wc-block-editor-product-gallery-thumbnails__position-toggle"
				isBlock
				label={ __( 'Thumbnails', 'poocommerce' ) }
				value={ context.thumbnailsPosition }
				help={
					positionHelp[
						context.thumbnailsPosition as ThumbnailsPosition
					]
				}
				onChange={ ( value: string ) =>
					updateBlockAttributes( productGalleryClientId, {
						thumbnailsPosition: value,
					} )
				}
			>
				<ToggleGroupControlOption
					value={ ThumbnailsPosition.OFF }
					label={ __( 'Off', 'poocommerce' ) }
				/>
				<ToggleGroupControlOption
					value={ ThumbnailsPosition.LEFT }
					label={
						<Icon size={ 32 } icon={ thumbnailsPositionLeft } />
					}
				/>
				<ToggleGroupControlOption
					value={ ThumbnailsPosition.BOTTOM }
					label={
						<Icon size={ 32 } icon={ thumbnailsPositionBottom } />
					}
				/>
				<ToggleGroupControlOption
					value={ ThumbnailsPosition.RIGHT }
					label={
						<Icon size={ 32 } icon={ thumbnailsPositionRight } />
					}
				/>
			</ToggleGroupControl>
			{ context.thumbnailsPosition !== ThumbnailsPosition.OFF && (
				<RangeControl
					label={ __( 'Number of Thumbnails', 'poocommerce' ) }
					value={ context.thumbnailsNumberOfThumbnails }
					onChange={ ( value: number ) =>
						updateBlockAttributes( productGalleryClientId, {
							thumbnailsNumberOfThumbnails: Math.round( value ),
						} )
					}
					help={ __(
						'Choose how many thumbnails (3-8) will display. If more images exist, a “View all” button will display.',
						'poocommerce'
					) }
					max={ maxNumberOfThumbnails }
					min={ minNumberOfThumbnails }
					step={ 1 }
				/>
			) }
		</>
	);
};
