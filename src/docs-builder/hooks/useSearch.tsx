/**
 * Custom hook for search functionality in the docs builder.
 *
 * Replaces direct DOM manipulation (document.querySelectorAll,
 * element.style.display) with React state-based filtering.
 *
 * @package EazyDocs
 * @since   2.9.0
 */
import { createContext, useContext, useState, useCallback, useMemo } from '@wordpress/element';

interface SearchContextValue {
	searchValue: string;
	setSearchValue: ( value: string ) => void;
}

const SearchContext = createContext< SearchContextValue >( {
	searchValue: '',
	setSearchValue: () => {},
} );

/**
 * Hook to access the search context.
 *
 * @return {SearchContextValue} Search context value.
 */
export const useSearch = (): SearchContextValue => useContext( SearchContext );

/**
 * SearchProvider component – wraps children with search context.
 */
export const SearchProvider: React.FC< { children: React.ReactNode } > = ( { children } ) => {
	const [ searchValue, setSearchValue ] = useState< string >( '' );

	const contextValue = useMemo(
		() => ( { searchValue, setSearchValue } ),
		[ searchValue ]
	);

	return (
		<SearchContext.Provider value={ contextValue }>
			{ children }
		</SearchContext.Provider>
	);
};
