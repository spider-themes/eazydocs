/**
 * ProgressBar component.
 *
 * Renders the positive/negative vote progress bar matching
 * the `ezd_child_docs_progress_bar()` PHP function output.
 *
 * @package EazyDocs
 * @since   2.8.0
 */
import { __, _n, sprintf } from '@wordpress/i18n';

interface ProgressBarProps {
	positive: number;
	negative: number;
}

const ProgressBar: React.FC<ProgressBarProps> = ( { positive, negative } ) => {
	let positiveTitle = '';
	let negativeTitle = '';

	if ( positive ) {
		positiveTitle = sprintf(
			_n( '%d Positive vote, ', '%d Positive votes and ', positive, 'eazydocs' ),
			positive
		);
	} else {
		positiveTitle = __( 'No Positive votes, ', 'eazydocs' );
	}

	if ( negative ) {
		negativeTitle = sprintf(
			_n( '%d Negative vote found.', '%d Negative votes found.', negative, 'eazydocs' ),
			negative
		);
	} else {
		negativeTitle = __( 'No Negative votes.', 'eazydocs' );
	}

	const sumVotes = positive + negative;
	const title = positiveTitle + negativeTitle;

	return (
		<span className="progress-text">
			{ ( positive || negative ) ? (
				<progress
					value={ positive }
					max={ sumVotes }
					title={ title }
					aria-label={ title }
				></progress>
			) : (
				__( 'No rates', 'eazydocs' )
			) }
		</span>
	);
};

export default ProgressBar;
