/**
 * External dependencies
 */
import { createElement } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
/* eslint-disable @poocommerce/dependency-group */
// @ts-ignore No types for this exist yet.
import { privateApis as routerPrivateApis } from '@wordpress/router';
// @ts-ignore No types for this exist yet.
import { unlock } from '@wordpress/edit-site/build-module/lock-unlock';
/* eslint-enable @poocommerce/dependency-group */

/**
 * Internal dependencies
 */
import { isGutenbergVersionAtLeast } from './utils';
import { Layout } from './layout';
import { useActiveRoute } from './route';

const { RouterProvider } = unlock( routerPrivateApis );

const SettingsLayout = () => {
	const { route, settingsPage, tabs, activeSection } = useActiveRoute();

	return (
		<Layout
			route={ route }
			settingsPage={ settingsPage }
			tabs={ tabs }
			activeSection={ activeSection }
		/>
	);
};

export const SettingsEditor = () => {
	const isRequiredGutenbergVersion = isGutenbergVersionAtLeast( 19.0 );

	if ( ! isRequiredGutenbergVersion ) {
		return (
			//  Temporary during development.
			<div style={ { margin: 'auto' } }>
				{ __(
					'Please enable Gutenberg version 19.0 or higher for this feature',
					'poocommerce'
				) }
			</div>
		);
	}

	return (
		<RouterProvider>
			<SettingsLayout />
		</RouterProvider>
	);
};

export * from './components';
export * from './legacy';
