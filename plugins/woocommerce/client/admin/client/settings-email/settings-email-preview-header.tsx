/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import apiFetch from '@wordpress/api-fetch';
import { useEffect, useState } from 'react';

/**
 * Internal dependencies
 */
import avatarIcon from './icon-avatar.svg';

type EmailPreviewHeaderProps = {
	emailType: string;
};

type EmailPreviewSubjectResponse = {
	subject: string;
};

export const EmailPreviewHeader: React.FC< EmailPreviewHeaderProps > = ( {
	emailType,
} ) => {
	const [ fromName, setFromName ] = useState( '' );
	const [ fromAddress, setFromAddress ] = useState( '' );
	const [ subject, setSubject ] = useState( '' );

	useEffect( () => {
		const fromNameEl = document.getElementById(
			'poocommerce_email_from_name'
		) as HTMLInputElement;
		const fromAddressEl = document.getElementById(
			'poocommerce_email_from_address'
		) as HTMLInputElement;

		if ( ! fromNameEl || ! fromAddressEl ) {
			return;
		}

		// Set initial values
		setFromName( fromNameEl.value || '' );
		setFromAddress( fromAddressEl.value || '' );

		const handleFromNameChange = ( event: Event ) => {
			const target = event.target as HTMLInputElement;
			setFromName( target.value || '' );
		};
		const handleFromAddressChange = ( event: Event ) => {
			const target = event.target as HTMLInputElement;
			setFromAddress( target.value || '' );
		};

		fromNameEl.addEventListener( 'change', handleFromNameChange );
		fromAddressEl.addEventListener( 'change', handleFromAddressChange );

		return () => {
			fromNameEl.removeEventListener( 'change', handleFromNameChange );
			fromAddressEl.removeEventListener(
				'change',
				handleFromAddressChange
			);
		};
	}, [] );

	useEffect( () => {
		const fetchSubject = async () => {
			try {
				const response: EmailPreviewSubjectResponse = await apiFetch( {
					path: `wc-admin-email/settings/email/preview-subject?type=${ emailType }`,
				} );
				setSubject( response.subject );
			} catch ( e ) {
				setSubject( '' );
			}
		};
		fetchSubject();
	}, [ emailType ] );

	return (
		<div className="wc-settings-email-preview-header">
			<h3 className="wc-settings-email-preview-header-subject">
				{ subject }
			</h3>
			<div className="wc-settings-email-preview-header-data">
				<div className="wc-settings-email-preview-header-icon">
					<img
						src={ avatarIcon }
						alt={ __( 'Avatar icon', 'poocommerce' ) }
					/>
				</div>
				<div className="wc-settings-email-preview-header-sender">
					{ fromName }
					<span>&lt;{ fromAddress }&gt;</span>
				</div>
			</div>
		</div>
	);
};
