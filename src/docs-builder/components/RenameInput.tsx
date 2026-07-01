/**
 * RenameInput – inline text field for renaming a doc in place.
 *
 * Shared by the parent nav and section cards so inline renaming behaves
 * identically everywhere: Enter or blur commits, Escape cancels, and an
 * empty/unchanged value is treated as a cancel. All events are stopped
 * from bubbling so the surrounding tab-switch / collapse handlers stay put.
 *
 * @package EazyDocs
 * @since   2.12.2
 */
import { useState, useRef, useEffect } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

interface RenameInputProps {
	/** Current title to seed the field with. */
	initialTitle: string;
	/** Called with the trimmed new title when it changed. */
	onCommit: ( title: string ) => void;
	/** Called when the edit is cancelled or left unchanged. */
	onCancel: () => void;
	/** Optional extra class for sizing to match the surrounding title. */
	className?: string;
}

const RenameInput: React.FC< RenameInputProps > = ( { initialTitle, onCommit, onCancel, className } ) => {
	const [ value, setValue ] = useState( initialTitle );
	const inputRef = useRef< HTMLInputElement >( null );
	// Guard so a commit-on-blur doesn't double-fire after Enter/Escape.
	const settled = useRef( false );

	useEffect( () => {
		const input = inputRef.current;
		if ( input ) {
			input.focus();
			input.select();
		}
	}, [] );

	const commit = (): void => {
		if ( settled.current ) {
			return;
		}
		settled.current = true;

		const next = value.trim();
		if ( ! next || next === initialTitle.trim() ) {
			onCancel();
			return;
		}
		onCommit( next );
	};

	const cancel = (): void => {
		if ( settled.current ) {
			return;
		}
		settled.current = true;
		onCancel();
	};

	return (
		<span className="ezd-rename" onClick={ ( e ) => e.stopPropagation() }>
			<input
				ref={ inputRef }
				type="text"
				className={ `ezd-rename-input${ className ? ` ${ className }` : '' }` }
				value={ value }
				aria-label={ __( 'Rename document', 'eazydocs' ) }
				onChange={ ( e ) => setValue( e.target.value ) }
				onKeyDown={ ( e ) => {
					e.stopPropagation();
					if ( 'Enter' === e.key ) {
						e.preventDefault();
						commit();
					} else if ( 'Escape' === e.key ) {
						e.preventDefault();
						cancel();
					}
				} }
				onBlur={ commit }
			/>
			<button
				type="button"
				className="ezd-rename-confirm"
				aria-label={ __( 'Save name', 'eazydocs' ) }
				title={ __( 'Save', 'eazydocs' ) }
				// Prevent the input blur (which would commit first) so this
				// click is the single source of the commit.
				onMouseDown={ ( e ) => e.preventDefault() }
				onClick={ ( e ) => {
					e.preventDefault();
					e.stopPropagation();
					commit();
				} }
			>
				<span className="dashicons dashicons-yes" aria-hidden="true"></span>
			</button>
		</span>
	);
};

export default RenameInput;
