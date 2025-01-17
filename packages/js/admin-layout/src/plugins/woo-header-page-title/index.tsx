/**
 * External dependencies
 */
import React from 'react';
import { Slot, Fill } from '@wordpress/components';
import { createElement, Fragment } from '@wordpress/element';

export const WC_HEADER_PAGE_TITLE_SLOT_NAME = 'poocommerce_header_page_title';

/**
 * Create a Fill for extensions to add custom page titles.
 *
 * @slotFill WooHeaderPageTitle
 * @scope poocommerce-admin
 * @example
 * const MyPageTitle = () => (
 * 	<WooHeaderPageTitle>My page title</WooHeaderPageTitle>
 * );
 *
 * registerPlugin( 'my-page-title', {
 * 	render: MyPageTitle,
 * 	scope: 'poocommerce-admin',
 * } );
 * @param {Object} param0
 * @param {Array}  param0.children - Node children.
 */
export const WooHeaderPageTitle: React.FC< {
	children?: React.ReactNode;
} > & {
	Slot: React.FC< React.ComponentProps< typeof Slot > >;
} = ( { children } ) => {
	return <Fill name={ WC_HEADER_PAGE_TITLE_SLOT_NAME }>{ children }</Fill>;
};

WooHeaderPageTitle.Slot = ( { fillProps } ) => (
	<Slot name={ WC_HEADER_PAGE_TITLE_SLOT_NAME } fillProps={ fillProps }>
		{ ( fills ) => {
			// @ts-expect-error -- TODO: react-18-upgrade - examine why the type is inferred to be ReactNode but the code seems to think it's an array
			return <>{ [ ...fills ].pop() }</>;
		} }
	</Slot>
);
