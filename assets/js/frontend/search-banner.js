/**
 * EazyDocs — Search Banner (AJAX live search)
 *
 * Single source of truth for every search-banner context:
 *   - Built-in banner on the doc single page (templates/search-banner.php)
 *   - Gutenberg search-banner block (includes/block-templates/search-banner.php)
 *   - Elementor search widget (includes/Elementor/Search/ezd-search.php)
 *
 * Behaviour is driven entirely by markup / data-attributes so the same file
 * serves all three. Optional elements (post-type filter, result tabs) simply
 * no-op when they are not present.
 *
 *   #ezd_searchInput          search input (optional data-post-type)
 *   #ezd-search-results       results container (data-noresult*, data-show-tabs)
 *   .header_search_keyword a  popular keyword chips
 *   .ezd-type-filter*         post-type filter (Elementor widget only)
 *
 * Requires: jQuery and the localized `eazydocs_local_object`
 * (ajaxurl, nonce, ezd_search_submit, i18n).
 */
( function ( $ ) {
	'use strict';

	var MIN_CHARS    = 2;      // Minimum keyword length before querying.
	var DEBOUNCE_MS  = 400;    // Live-search debounce.
	var activeRequest = null;  // In-flight jqXHR so stale responses can be aborted.

	function $results() {
		return $( '#ezd-search-results' );
	}

	function selectedType() {
		return $( '#ezd_searchInput' ).data( 'post-type' ) || 'all';
	}

	/**
	 * Announce a message to assistive technology via a polite live region.
	 * The region is created once and visually hidden (no CSS dependency).
	 */
	function announce( message ) {
		var $status = $( '#ezd-search-status' );
		if ( ! $status.length ) {
			$status = $( '<span>', {
				id: 'ezd-search-status',
				'aria-live': 'polite',
				'aria-atomic': 'true'
			} ).css( {
				position: 'absolute',
				width: '1px',
				height: '1px',
				margin: '-1px',
				padding: '0',
				border: '0',
				overflow: 'hidden',
				'white-space': 'nowrap',
				'clip-path': 'inset(50%)'
			} ).appendTo( 'body' );
		}
		$status.text( message );
	}

	// Reset the search UI: close results and drop the focus state.
	function resetSearch() {
		$results().removeClass( 'ajax-search' ).html( '' );
		$( 'body' ).removeClass( 'ezd-search-focused' );
		$( 'form.ezd_search_form' ).css( 'z-index', '' );
	}

	// Friendly "no results" state (optional image + title + subtitle).
	function buildNoResult() {
		var $r      = $results();
		var img     = $r.data( 'noresult-img' );
		var title   = $r.data( 'noresult-title' ) || $r.attr( 'data-noresult' ) || 'No Results Found';
		var sub     = $r.data( 'noresult-sub' );
		var imgHtml = img ? '<div class="ezd-no-results-img"><img src="' + img + '" alt=""></div>' : '';
		var subHtml = sub ? '<p class="ezd-no-results-sub">' + sub + '</p>' : '';
		$r.addClass( 'ajax-search' ).html(
			'<div class="ezd-no-results">' + imgHtml +
			'<h4 class="ezd-no-results-title">' + title + '</h4>' +
			subHtml + '</div>'
		);
		announce( title );
	}

	// Honour the widget's "show result tabs" setting.
	function applyTabVisibility() {
		if ( $results().data( 'show-tabs' ) == '0' ) {
			$results().find( '.ezd-result-tabs' ).hide();
		}
	}

	// When a fixed post type is configured, open its tab by default.
	function activateConfiguredTab() {
		var type = selectedType();
		if ( type !== 'all' ) {
			$results().find( '.ezd-tab[data-tab="' + type + '"]' ).trigger( 'click' );
		}
	}

	function renderResults( data ) {
		if ( $.trim( data ).length > 0 ) {
			$results().addClass( 'ajax-search' ).html( data );
			applyTabVisibility();
			activateConfiguredTab();

			var count = $results().find( '.search-result-item' ).length;
			var tpl   = ( eazydocs_local_object.i18n && eazydocs_local_object.i18n.results_found ) || '%d results found';
			announce( tpl.replace( '%d', count ) );
		} else {
			buildNoResult();
		}
	}

	// Run the AJAX live search.
	function runSearch() {
		var keyword = $( '#ezd_searchInput' ).val() || '';

		if ( $.trim( keyword ).length < MIN_CHARS ) {
			if ( activeRequest ) {
				activeRequest.abort();
				activeRequest = null;
			}
			$results().removeClass( 'ajax-search' ).html( '' );
			return;
		}

		// Abort any earlier request so a slow response can't overwrite a newer one.
		if ( activeRequest ) {
			activeRequest.abort();
		}

		activeRequest = $.ajax( {
			url: eazydocs_local_object.ajaxurl,
			type: 'post',
			data: {
				action: 'eazydocs_search_results',
				keyword: keyword,
				post_type: selectedType(),
				security: eazydocs_local_object.nonce
			},
			beforeSend: function () {
				$( '.spinner-border' ).show();
			}
		} ).done( function ( data ) {
			renderResults( data );
		} ).always( function ( jqXHR, status ) {
			if ( status !== 'abort' ) {
				$( '.spinner-border' ).hide();
				activeRequest = null;
			}
		} );
	}

	// Expose for any inline/legacy callers.
	window.ezSearchResults = runSearch;

	var debouncedSearch = ( function () {
		var timer = 0;
		return function () {
			clearTimeout( timer );
			timer = setTimeout( runSearch, DEBOUNCE_MS );
		};
	} )();

	// --- Keyboard navigation across result items -------------------------------
	function resultLinks() {
		return $results().find( '.search-result-item a.title' );
	}

	function moveSelection( dir ) {
		var $items = resultLinks();
		if ( ! $items.length ) {
			return;
		}
		var idx  = $items.index( document.activeElement );
		var next = idx + dir;
		if ( next < 0 ) {
			$( '#ezd_searchInput' ).trigger( 'focus' );
			return;
		}
		if ( next >= $items.length ) {
			next = $items.length - 1;
		}
		$items.eq( next ).trigger( 'focus' );
	}

	$( function () {

		// Focus state.
		$( document ).on( 'focus', '#ezd_searchInput', function () {
			$( 'body' ).addClass( 'ezd-search-focused' );
			$( this ).closest( 'form.ezd_search_form' ).css( 'z-index', '999' );
		} );

		$( document ).on( 'click', '.focus_overlay', resetSearch );

		// Result type tabs.
		$( document ).on( 'click', '.ezd-tab', function () {
			var tab = $( this ).data( 'tab' );
			$( '.ezd-tab' ).removeClass( 'active' );
			$( this ).addClass( 'active' );
			if ( tab === 'all' ) {
				$( '#ezd-search-results .ezd-result-group' ).show();
			} else {
				$( '#ezd-search-results .ezd-result-group' ).hide();
				$( '#ezd-search-results .ezd-result-group[data-type="' + tab + '"]' ).show();
			}
		} );

		// Post-type filter dropdown (Elementor widget).
		$( document ).on( 'click', '.ezd-type-filter-btn', function ( e ) {
			e.stopPropagation();
			$( this ).next( '.ezd-type-filter-dropdown' ).toggleClass( 'open' );
			$( this ).toggleClass( 'open' );
		} );

		$( document ).on( 'click', '.ezd-type-option', function ( e ) {
			e.preventDefault();
			e.stopPropagation();
			var newType  = $( this ).data( 'type' );
			var newLabel = $( this ).text().trim();
			$( '#ezd_searchInput' ).attr( 'data-post-type', newType ).data( 'post-type', newType );
			$( '.ezd-filter-label' ).text( newLabel );
			$( '.ezd-type-filter-dropdown' ).removeClass( 'open' );
			$( '.ezd-type-filter-btn' ).removeClass( 'open' );
			if ( $.trim( $( '#ezd_searchInput' ).val() ).length ) {
				runSearch();
			}
		} );

		// Dropdown title click: docs → load child docs inline; others → navigate.
		$( document ).on( 'click', '.ezd-title-dropdown li a:not(.ezd-type-option)', function ( e ) {
			e.preventDefault();
			var postId      = $( this ).data( 'id' );
			var postTitle   = $( this ).text().trim();
			var fallbackUrl = $( this ).attr( 'href' );

			$( '.ezd-type-filter-dropdown' ).removeClass( 'open' );
			$( '.ezd-type-filter-btn' ).removeClass( 'open' );

			if ( selectedType() !== 'docs' ) {
				window.location.href = fallbackUrl;
				return;
			}

			$( '#ezd_searchInput' ).val( '' );
			$results().addClass( 'ajax-search' ).html(
				'<div class="ezd-panel-loading"><span class="spinner-border spinner-border-sm" role="status"></span></div>'
			);

			$.ajax( {
				url: eazydocs_local_object.ajaxurl,
				type: 'post',
				data: {
					action: 'eazydocs_child_docs',
					parent_id: postId,
					security: eazydocs_local_object.nonce
				}
			} ).done( function ( response ) {
				if ( response.success && $.trim( response.data.html ).length > 0 ) {
					$results().addClass( 'ajax-search' ).html(
						'<div class="ezd-result-group-label">' + postTitle + '</div>' + response.data.html
					);
				} else {
					buildNoResult();
				}
			} ).fail( buildNoResult );
		} );

		// Close the type dropdown when clicking outside it.
		$( document ).on( 'click', function ( e ) {
			if ( ! $( e.target ).closest( '.ezd-type-filter' ).length ) {
				$( '.ezd-type-filter-dropdown' ).removeClass( 'open' );
				$( '.ezd-type-filter-btn' ).removeClass( 'open' );
			}
		} );

		// Close results when clicking outside the search form.
		$( document ).on( 'mousedown', function ( e ) {
			var $t = $( e.target );
			if (
				! $t.closest( '#ezd-search-results' ).length &&
				! $t.closest( '.header_search_form_info' ).length &&
				! $t.closest( '.header_search_keyword' ).length &&
				! $t.closest( '.ezd_search_keywords' ).length &&
				! $t.closest( '.focus_overlay' ).length
			) {
				resetSearch();
			}
		} );

		// Popular keyword chips. Two markups exist: the Elementor widget renders
		// `.header_search_keyword`, the built-in banner/block renders `.ezd_search_keywords`.
		$( document ).on( 'click', '.header_search_keyword ul li a, .ezd_search_keywords ul li a', function ( e ) {
			e.preventDefault();
			$( '#ezd_searchInput' ).val( $.trim( $( this ).text() ) ).trigger( 'focus' );
			runSearch();
		} );

		// Live search (debounced); ignore navigation keys.
		$( document ).on( 'keyup', '#ezd_searchInput', function ( e ) {
			if ( e.key === 'ArrowDown' || e.key === 'ArrowUp' || e.key === 'Enter' ) {
				return;
			}
			debouncedSearch();
		} );

		// ArrowDown from the input moves into the result list.
		$( document ).on( 'keydown', '#ezd_searchInput', function ( e ) {
			if ( e.key === 'ArrowDown' && resultLinks().length ) {
				e.preventDefault();
				resultLinks().first().trigger( 'focus' );
			}
		} );

		// Arrow navigation between result items.
		$( document ).on( 'keydown', '#ezd-search-results .search-result-item a.title', function ( e ) {
			if ( e.key === 'ArrowDown' ) {
				e.preventDefault();
				moveSelection( 1 );
			} else if ( e.key === 'ArrowUp' ) {
				e.preventDefault();
				moveSelection( -1 );
			}
		} );

		// Escape closes results from anywhere.
		$( document ).on( 'keyup', function ( e ) {
			if ( e.key === 'Escape' ) {
				resetSearch();
			}
		} );

		// Honour the "Enter key search" option.
		$( document ).on( 'keypress', '#ezd_searchInput', function ( e ) {
			if ( eazydocs_local_object.ezd_search_submit != 1 && e.key === 'Enter' ) {
				e.preventDefault();
			}
		} );

		// Submit button (widget): submit when allowed, otherwise block.
		$( document ).on( 'click', 'button.search_submit_btn', function ( e ) {
			if ( eazydocs_local_object.ezd_search_submit != 1 ) {
				e.preventDefault();
				return false;
			}
			$( this ).closest( '.ezd_search_form' ).trigger( 'submit' );
		} );

		// Icon label (built-in banner): submit only when allowed; otherwise let it focus the input.
		$( document ).on( 'click', 'label[for="ezd_searchInput"]', function () {
			if ( eazydocs_local_object.ezd_search_submit == 1 ) {
				$( this ).closest( '.ezd_search_form' ).trigger( 'submit' );
			}
		} );

		// Block empty submits.
		$( document ).on( 'submit', '.ezd_search_form', function ( e ) {
			if ( $.trim( $( this ).find( '#ezd_searchInput' ).val() ) === '' ) {
				e.preventDefault();
				return false;
			}
		} );

		// Native search-field clear (the "x") empties results. Bound directly
		// because the `search` event does not reliably bubble for delegation.
		var inputEl = document.getElementById( 'ezd_searchInput' );
		if ( inputEl ) {
			inputEl.addEventListener( 'search', function () {
				$results().empty().removeClass( 'ajax-search' );
			} );
		}

		// Seamless input↔results join: toggle a class while results are shown.
		var resultsEl = document.getElementById( 'ezd-search-results' );
		if ( resultsEl && window.MutationObserver ) {
			var $form = $( resultsEl ).closest( 'form.ezd_search_form' );
			new MutationObserver( function ( mutations ) {
				mutations.forEach( function ( m ) {
					if ( m.attributeName === 'class' ) {
						$form.toggleClass( 'ezd-results-open', resultsEl.classList.contains( 'ajax-search' ) );
					}
				} );
			} ).observe( resultsEl, { attributes: true } );
		}
	} );

} )( jQuery );
