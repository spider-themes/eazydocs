/**
 * Toast notification context and hook.
 *
 * Provides a React-friendly way to show save indicators
 * and other toast notifications without direct DOM manipulation.
 *
 * @package EazyDocs
 * @since   2.9.0
 */
import { createContext, useContext, useState, useCallback, useRef } from '@wordpress/element';
import { createPortal } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import type { ToastData } from '../types';

interface ToastContextValue {
	showToast: ( message: string, type?: 'success' | 'error' ) => void;
}

const ToastContext = createContext< ToastContextValue >( {
	showToast: () => {},
} );

/**
 * Hook to access the toast notification system.
 *
 * @return {ToastContextValue} Toast context value.
 */
export const useToast = (): ToastContextValue => useContext( ToastContext );

let toastCounter = 0;

/**
 * ToastProvider component – wraps children with toast context.
 */
export const ToastProvider: React.FC< { children: React.ReactNode } > = ( { children } ) => {
	const [ toasts, setToasts ] = useState< ToastData[] >( [] );
	const timersRef = useRef< Map< string, ReturnType< typeof setTimeout > > >( new Map() );

	const showToast = useCallback( ( message: string, type: 'success' | 'error' = 'success' ): void => {
		const id = `toast-${ ++toastCounter }`;
		setToasts( ( prev ) => [ ...prev, { id, message, type } ] );

		const timer = setTimeout( () => {
			setToasts( ( prev ) => prev.filter( ( t ) => t.id !== id ) );
			timersRef.current.delete( id );
		}, 2300 );

		timersRef.current.set( id, timer );
	}, [] );

	return (
		<ToastContext.Provider value={ { showToast } }>
			{ children }
			{ createPortal(
				<div className="ezd-toast-container" aria-live="polite">
					{ toasts.map( ( toast ) => (
						<div
							key={ toast.id }
							className={ `ezd-save-indicator is-visible${ toast.type === 'error' ? ' is-error' : '' }` }
							role="status"
						>
							<span
								className={ `dashicons ${ toast.type === 'success' ? 'dashicons-yes-alt' : 'dashicons-warning' }` }
							></span>
							{ toast.message }
						</div>
					) ) }
				</div>,
				document.body
			) }
		</ToastContext.Provider>
	);
};
