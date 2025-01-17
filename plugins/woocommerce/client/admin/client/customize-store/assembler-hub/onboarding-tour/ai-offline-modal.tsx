/**
 * External dependencies
 */
import { Button, Modal } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

type AiOfflineModalProps = {
	skipTour: () => void;
	takeTour: () => void;
	shouldTourBeShown: boolean;
};

export const AiOfflineModal = ( {
	skipTour,
	takeTour,
	shouldTourBeShown,
}: AiOfflineModalProps ) => {
	return (
		<Modal
			className="poocommerce-customize-store__onboarding-welcome-modal"
			title={ __( 'Welcome to your store!', 'poocommerce' ) }
			onRequestClose={ skipTour }
			shouldCloseOnClickOutside={ false }
		>
			<span className="poocommerce-customize-store__title">
				{ __(
					'Our AI tool had a few issues generating your content.',
					'poocommerce'
				) }
			</span>
			<p>
				{ __(
					"But don't let that stop you! Start customizing the look and feel of your store by adding your logo and selecting your colors and layout. ",
					'poocommerce'
				) }
				{ shouldTourBeShown &&
					__(
						"Take a quick tour to discover what's possible.",
						'poocommerce'
					) }
			</p>
			{ shouldTourBeShown && (
				<div className="poocommerce-customize-store__design-change-warning-modal-footer">
					<Button onClick={ skipTour } variant="link">
						{ __( 'Skip', 'poocommerce' ) }
					</Button>
					<Button onClick={ takeTour } variant="primary">
						{ __( 'Take a tour', 'poocommerce' ) }
					</Button>
				</div>
			) }
		</Modal>
	);
};
