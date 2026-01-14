/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./src/tabbed-docs/block.json":
/*!************************************!*\
  !*** ./src/tabbed-docs/block.json ***!
  \************************************/
/***/ ((module) => {

module.exports = /*#__PURE__*/JSON.parse('{"apiVersion":3,"name":"eazydocs/tabbed-docs","version":"0.1.0","title":"Tabbed Docs","category":"eazydocs","icon":"category","description":"Display multiple docs in a tabbed layout with various preset styles.","supports":{"html":false,"anchor":true},"attributes":{"preset":{"type":"string","default":"flat_tabbed"},"show_docs":{"type":"string","default":-1},"sectionsNumber":{"type":"string","default":5},"articlesNumber":{"type":"string","default":-1},"readMoreText":{"type":"string","default":"View All"},"orderBy":{"type":"string","default":"menu_order"},"parent_docs_order":{"type":"string","default":"asc"},"child_docs_order":{"type":"string","default":"desc"},"docs_layout":{"type":"string","default":"grid"},"col":{"type":"number","default":2},"include":{"type":"array","default":[]},"exclude":{"type":"array","default":[]},"isFeaturedImage":{"type":"boolean","default":true},"excerptCharNumber":{"type":"string","default":12},"isPreviewOpen":{"type":"boolean","default":false},"enableHoverAnimation":{"type":"boolean","default":true},"tabStyle":{"type":"string","default":"default"},"showDocCount":{"type":"boolean","default":true},"showTabIcon":{"type":"boolean","default":false},"cardStyle":{"type":"string","default":"elevated"},"animationSpeed":{"type":"string","default":"normal"},"showLastUpdated":{"type":"boolean","default":false},"compactMode":{"type":"boolean","default":false},"primaryColor":{"type":"string","default":""},"cardPadding":{"type":"number","default":32},"cardGap":{"type":"number","default":24},"borderRadius":{"type":"number","default":16},"showTitleLine":{"type":"boolean","default":true},"buttonStyle":{"type":"string","default":"filled"},"iconStyle":{"type":"string","default":"rounded"},"shadowIntensity":{"type":"string","default":"medium"}},"textdomain":"eazydocs","editorScript":"file:./index.js","editorStyle":"file:./index.css"}');

/***/ }),

/***/ "./src/tabbed-docs/edit.js":
/*!*********************************!*\
  !*** ./src/tabbed-docs/edit.js ***!
  \*********************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ Edit)
/* harmony export */ });
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/block-editor */ "@wordpress/block-editor");
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _editor_scss__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./editor.scss */ "./src/tabbed-docs/editor.scss");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! react/jsx-runtime */ "react/jsx-runtime");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__);
/**
 * Edit component for Tabbed Docs block
 * Provides enhanced UI/UX with featured image support in tabs
 * Pro features are locked with "Upgrade to Pro" notices for free users
 * Pro options show in dropdowns with badges and work in demo mode in editor
 *
 * @package EazyDocs
 */







// Import editor styles


/**
 * Check if Pro is active via localized script data
 */

const isProActive = () => {
  return window.eazydocs_local_object?.is_ezd_pro_block === 'yes' || window.ezdBlockData?.is_ezd_pro_block === 'yes';
};

/**
 * Get the dynamic pricing page URL from localized script data
 */
const getPricingUrl = () => {
  return window.eazydocs_local_object?.ezd_pricing_url || window.ezdBlockData?.ezd_pricing_url || '/wp-admin/admin.php?page=eazydocs-pricing';
};

/**
 * Pro style values that require Pro license
 */
const proTabStyles = ['pill', 'boxed', 'underline', 'gradient', 'glass'];
const proCardStyles = ['flat', 'minimal', 'glass', 'gradient-border'];
const proLayoutTypes = ['masonry'];

/**
 * Check if a value is a Pro-only option
 */
const isProOption = (value, proList) => proList.includes(value);

/**
 * Pro Badge Component - Inline for dropdown options
 */
const ProBadge = ({
  inline = false
}) => /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsxs)("span", {
  className: `ezd-pro-badge ${inline ? 'ezd-pro-badge-inline' : ''}`,
  children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)("svg", {
    xmlns: "http://www.w3.org/2000/svg",
    width: "10",
    height: "10",
    viewBox: "0 0 24 24",
    fill: "currentColor",
    children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)("path", {
      d: "M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z"
    })
  }), (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Pro', 'eazydocs')]
});

/**
 * Pro Demo Notice - Shows when a Pro option is selected in demo mode
 */
const ProDemoNotice = () => /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.Notice, {
  status: "warning",
  isDismissible: false,
  className: "ezd-pro-demo-notice",
  children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsxs)("div", {
    className: "ezd-pro-demo-notice-content",
    children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)("strong", {
      children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Pro Feature Preview', 'eazydocs')
    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)("p", {
      children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('This style will not apply on the frontend.', 'eazydocs')
    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsxs)("a", {
      className: "ezd-upgrade-link",
      href: getPricingUrl(),
      children: [(0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Upgrade to Pro', 'eazydocs'), " \u2192"]
    })]
  })
});

/**
 * Pro Watermark - Large centered PRO text overlay with upgrade button
 */
const ProWatermark = () => /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)("div", {
  className: "ezd-pro-watermark",
  children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsxs)("a", {
    className: "ezd-pro-watermark-btn",
    href: getPricingUrl(),
    children: [(0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Upgrade to Pro', 'eazydocs'), " \u2192"]
  })
});

/**
 * Upgrade Notice - For panel descriptions (not overlay)
 */
const UpgradeNotice = ({
  feature
}) => /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsxs)("div", {
  className: "ezd-upgrade-notice",
  children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)("div", {
    className: "ezd-upgrade-icon",
    children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsxs)("svg", {
      xmlns: "http://www.w3.org/2000/svg",
      width: "16",
      height: "16",
      viewBox: "0 0 24 24",
      fill: "none",
      stroke: "currentColor",
      strokeWidth: "2",
      children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)("path", {
        d: "M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)("path", {
        d: "M12 16v-4"
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)("path", {
        d: "M12 8h.01"
      })]
    })
  }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsxs)("div", {
    className: "ezd-upgrade-content",
    children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)("strong", {
      children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Pro Feature', 'eazydocs')
    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)("p", {
      children: feature
    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsxs)("a", {
      className: "ezd-upgrade-link",
      href: getPricingUrl(),
      children: [(0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Upgrade to Pro', 'eazydocs'), " \u2192"]
    })]
  })]
});

/**
 * Locked Control Wrapper - Grayed out with PRO watermark (like screenshot)
 */
const LockedControl = ({
  children,
  feature,
  isPro = false
}) => {
  if (isPro || isProActive()) {
    return children;
  }
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsxs)("div", {
    className: "ezd-locked-control",
    children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)("div", {
      className: "ezd-locked-overlay",
      children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(ProWatermark, {})
    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)("div", {
      className: "ezd-locked-content",
      children: children
    })]
  });
};

/**
 * TabIcon Component - Fetches and displays featured image for a tab
 */
const TabIcon = ({
  mediaId
}) => {
  const media = (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_3__.useSelect)(select => {
    if (!mediaId) return null;
    return select('core').getMedia(mediaId, {
      context: 'view'
    });
  }, [mediaId]);
  if (!media) return null;
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)("span", {
    className: "ezd-tab-icon",
    children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)("img", {
      src: media.source_url,
      alt: media.alt_text || '',
      className: "ezd-tab-icon-img"
    })
  });
};

/**
 * Helper function to extract doc IDs from form token values
 */
const doc_ids = tokens => {
  return tokens.map(token => {
    const match = token.match(/^(\d+)/);
    return match ? parseInt(match[1], 10) : null;
  }).filter(id => id !== null);
};

/**
 * Create dropdown options with Pro badges
 */
const createOptionsWithProBadge = (freeOptions, proOptions, isPro) => {
  const allOptions = [...freeOptions];
  proOptions.forEach(option => {
    allOptions.push({
      ...option,
      label: isPro ? option.label : `${option.label} ⭐ Pro`
    });
  });
  return allOptions;
};

/**
 * Edit component
 */
function Edit({
  attributes,
  setAttributes
}) {
  const {
    preset,
    show_docs,
    sectionsNumber,
    articlesNumber,
    readMoreText,
    orderBy,
    parent_docs_order,
    child_docs_order,
    docs_layout,
    col,
    include,
    exclude,
    isFeaturedImage,
    excerptCharNumber,
    enableHoverAnimation,
    tabStyle,
    showDocCount,
    showTabIcon,
    cardStyle,
    animationSpeed,
    showLastUpdated,
    compactMode,
    // Advanced styling attributes
    primaryColor,
    cardPadding,
    cardGap,
    borderRadius,
    showTitleLine,
    buttonStyle,
    iconStyle,
    shadowIntensity
  } = attributes;

  // Check Pro status
  const isPro = isProActive();

  // State for active tab in preview
  const [activeTab, setActiveTab] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_4__.useState)(null);

  // Check if current selections are Pro features (for demo notice)
  const isUsingProTabStyle = !isPro && isProOption(tabStyle, proTabStyles);
  const isUsingProCardStyle = !isPro && isProOption(cardStyle, proCardStyles);
  const isUsingProLayout = !isPro && docs_layout === 'masonry';
  const isUsingProFeature = isUsingProTabStyle || isUsingProCardStyle || isUsingProLayout;

  // Fetch parent docs (those with parent === 0)
  const parentDocs = (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_3__.useSelect)(select => {
    try {
      const args = {
        per_page: show_docs || -1,
        order: parent_docs_order || 'asc',
        orderby: orderBy === 'rand' ? 'title' : orderBy,
        parent: 0,
        status: ['publish', 'private']
      };
      if (doc_ids(include || []).length > 0) {
        args.include = doc_ids(include);
      }

      // Only apply exclude if Pro is active
      if (isPro && doc_ids(exclude || []).length > 0) {
        args.exclude = doc_ids(exclude);
      }
      return select('core').getEntityRecords('postType', 'docs', args);
    } catch (error) {
      console.error('Error fetching parent docs:', error);
      return [];
    }
  }, [show_docs, orderBy, parent_docs_order, include, exclude, isPro]);

  // Fetch all docs for suggestions and child docs
  const allDocs = (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_3__.useSelect)(select => {
    return select('core').getEntityRecords('postType', 'docs', {
      per_page: -1,
      status: ['publish', 'private'],
      orderby: orderBy === 'rand' ? 'title' : orderBy,
      order: child_docs_order || 'desc'
    });
  }, [orderBy, child_docs_order]);

  // Set initial active tab
  (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_4__.useEffect)(() => {
    if (parentDocs && parentDocs.length > 0 && !activeTab) {
      setActiveTab(parentDocs[0].id);
    }
  }, [parentDocs]);

  // Get child docs for a parent
  const getChildDocs = parentId => {
    if (!allDocs) return [];
    return allDocs.filter(doc => doc.parent === parentId);
  };

  // Get grandchild docs count for article count
  const getArticleCount = parentId => {
    if (!allDocs) return 0;
    const children = allDocs.filter(doc => doc.parent === parentId);
    let count = children.length;
    children.forEach(child => {
      count += allDocs.filter(doc => doc.parent === child.id).length;
    });
    return count;
  };

  // Doc suggestions for include/exclude
  const docSuggestions = allDocs ? allDocs.filter(doc => doc.parent === 0).map(doc => `${doc.id} | ${doc.title.rendered}`) : [];

  // Order options
  const orderOptions = [{
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Ascending', 'eazydocs'),
    value: 'asc'
  }, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Descending', 'eazydocs'),
    value: 'desc'
  }];
  const orderByOptions = [{
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Title', 'eazydocs'),
    value: 'title'
  }, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Post Author', 'eazydocs'),
    value: 'author'
  }, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Date', 'eazydocs'),
    value: 'date'
  }, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Post ID', 'eazydocs'),
    value: 'id'
  }, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Last Modified Date', 'eazydocs'),
    value: 'modified'
  }, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Random', 'eazydocs'),
    value: 'rand'
  }, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Menu Order', 'eazydocs'),
    value: 'menu_order'
  }];

  // Free layout options
  const freeLayoutOptions = [{
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Grid', 'eazydocs'),
    value: 'grid'
  }];

  // Pro layout options
  const proLayoutOptions = [{
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Masonry', 'eazydocs'),
    value: 'masonry'
  }];

  // All layout options with Pro badge
  const layoutOptions = createOptionsWithProBadge(freeLayoutOptions, proLayoutOptions, isPro);

  // Free tab styles (basic options)
  const freeTabStyleOptions = [{
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Default (Underline)', 'eazydocs'),
    value: 'default'
  }, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Rounded Pill', 'eazydocs'),
    value: 'rounded'
  }];

  // Pro tab styles (advanced options)
  const proTabStyleOptionsList = [{
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Pill Style', 'eazydocs'),
    value: 'pill'
  }, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Boxed', 'eazydocs'),
    value: 'boxed'
  }, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Simple Underline', 'eazydocs'),
    value: 'underline'
  }, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Gradient', 'eazydocs'),
    value: 'gradient'
  }, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Glassmorphism', 'eazydocs'),
    value: 'glass'
  }];

  // All tab style options with Pro badges
  const tabStyleOptions = createOptionsWithProBadge(freeTabStyleOptions, proTabStyleOptionsList, isPro);

  // Free card styles
  const freeCardStyleOptions = [{
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Elevated (Shadow)', 'eazydocs'),
    value: 'elevated'
  }, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Bordered', 'eazydocs'),
    value: 'bordered'
  }];

  // Pro card styles
  const proCardStyleOptionsList = [{
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Flat', 'eazydocs'),
    value: 'flat'
  }, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Minimal', 'eazydocs'),
    value: 'minimal'
  }, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Glassmorphism', 'eazydocs'),
    value: 'glass'
  }, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Gradient Border', 'eazydocs'),
    value: 'gradient-border'
  }];

  // All card style options with Pro badges
  const cardStyleOptions = createOptionsWithProBadge(freeCardStyleOptions, proCardStyleOptionsList, isPro);
  const animationSpeedOptions = [{
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Slow', 'eazydocs'),
    value: 'slow'
  }, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Normal', 'eazydocs'),
    value: 'normal'
  }, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Fast', 'eazydocs'),
    value: 'fast'
  }];
  const buttonStyleOptions = [{
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Filled', 'eazydocs'),
    value: 'filled'
  }, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Outlined', 'eazydocs'),
    value: 'outlined'
  }, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Text Only', 'eazydocs'),
    value: 'text'
  }];
  const iconStyleOptions = [{
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Rounded', 'eazydocs'),
    value: 'rounded'
  }, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Circle', 'eazydocs'),
    value: 'circle'
  }, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Square', 'eazydocs'),
    value: 'square'
  }];
  const shadowIntensityOptions = [{
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('None', 'eazydocs'),
    value: 'none'
  }, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Light', 'eazydocs'),
    value: 'light'
  }, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Medium', 'eazydocs'),
    value: 'medium'
  }, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Strong', 'eazydocs'),
    value: 'strong'
  }];

  // Build wrapper classes - Always apply selected styles for editor preview (demo mode)
  const wrapperClasses = ['ezd-tabbed-docs-editor', 'ezd-tabbed-docs', `ezd-tab-style-${tabStyle || 'default'}`, `ezd-card-style-${cardStyle || 'elevated'}`, `ezd-animation-${animationSpeed || 'normal'}`, `ezd-shadow-${shadowIntensity || 'medium'}`, `ezd-button-${buttonStyle || 'filled'}`, `ezd-icon-${iconStyle || 'rounded'}`, enableHoverAnimation ? 'ezd-hover-enabled' : '', compactMode ? 'ezd-compact' : '', !showTitleLine ? 'ezd-no-title-line' : '', isUsingProFeature ? 'ezd-pro-demo-mode' : ''].filter(Boolean).join(' ');

  // Build inline styles for custom properties
  const customStyles = {
    '--ezd-primary': primaryColor || undefined,
    '--ezd-card-padding': cardPadding ? `${cardPadding}px` : undefined,
    '--ezd-grid-gap': cardGap ? `${cardGap}px` : undefined,
    '--ezd-card-radius': borderRadius ? `${borderRadius}px` : undefined
  };
  const layoutClass = docs_layout === 'grid' ? `ezd-grid ezd-column-${col} ezd-topic-list-inner` : `ezd-masonry-wrap ezd-masonry-col-${col} ezd-topic-list-inner`;

  // Loading state
  if (!parentDocs) {
    return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)("div", {
      ...(0,_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1__.useBlockProps)(),
      children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsxs)("div", {
        className: "ezd-tabbed-docs-loading",
        children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.Spinner, {}), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)("p", {
          children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Loading documentation...', 'eazydocs')
        })]
      })
    });
  }

  // Empty state
  if (parentDocs.length === 0) {
    return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)("div", {
      ...(0,_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1__.useBlockProps)(),
      children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsxs)("div", {
        className: "ezd-tabbed-docs-empty",
        children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsxs)("svg", {
          xmlns: "http://www.w3.org/2000/svg",
          width: "48",
          height: "48",
          viewBox: "0 0 24 24",
          fill: "none",
          stroke: "currentColor",
          strokeWidth: "1.5",
          children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)("path", {
            d: "M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"
          }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)("polyline", {
            points: "14 2 14 8 20 8"
          }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)("line", {
            x1: "12",
            y1: "18",
            x2: "12",
            y2: "12"
          }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)("line", {
            x1: "9",
            y1: "15",
            x2: "15",
            y2: "15"
          })]
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)("h4", {
          children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('No Documentation Found', 'eazydocs')
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)("p", {
          children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Create some documentation posts to display them here.', 'eazydocs')
        })]
      })
    });
  }
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsxs)(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.Fragment, {
    children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsxs)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1__.InspectorControls, {
      children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsxs)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.PanelBody, {
        title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Content Settings', 'eazydocs'),
        initialOpen: true,
        children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.__experimentalNumberControl, {
          label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Number of Docs', 'eazydocs'),
          help: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Number of parent docs to show. Use -1 for all.', 'eazydocs'),
          value: show_docs,
          onChange: value => setAttributes({
            show_docs: value
          }),
          min: -1,
          step: 1
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.__experimentalNumberControl, {
          label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Sections per Tab', 'eazydocs'),
          help: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Number of sections per tab. Use -1 for all.', 'eazydocs'),
          value: sectionsNumber,
          onChange: value => setAttributes({
            sectionsNumber: value
          }),
          min: -1,
          step: 1
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(LockedControl, {
          feature: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Control the number of articles displayed per section.', 'eazydocs'),
          isPro: isPro,
          children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.__experimentalNumberControl, {
            label: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsxs)(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.Fragment, {
              children: [(0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Articles per Section', 'eazydocs'), !isPro && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(ProBadge, {
                inline: true
              })]
            }),
            help: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Number of articles per section. Use -1 for all.', 'eazydocs'),
            value: articlesNumber,
            onChange: value => setAttributes({
              articlesNumber: value
            }),
            min: -1,
            step: 1
          })
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.TextControl, {
          label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Read More Text', 'eazydocs'),
          value: readMoreText,
          onChange: value => setAttributes({
            readMoreText: value
          })
        }), exclude.length === 0 && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.FormTokenField, {
          __experimentalAutoSelectFirstMatch: true,
          __experimentalExpandOnFocus: true,
          label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Docs to Show', 'eazydocs'),
          help: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Select specific docs to show. Leave empty for all.', 'eazydocs'),
          suggestions: docSuggestions,
          value: include,
          onChange: value => setAttributes({
            include: value
          })
        }), include.length === 0 && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(LockedControl, {
          feature: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Exclude specific documentation from display.', 'eazydocs'),
          isPro: isPro,
          children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.FormTokenField, {
            __experimentalAutoSelectFirstMatch: true,
            __experimentalExpandOnFocus: true,
            label: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsxs)(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.Fragment, {
              children: [(0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Docs to Exclude', 'eazydocs'), !isPro && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(ProBadge, {
                inline: true
              })]
            }),
            help: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Select specific docs to exclude.', 'eazydocs'),
            suggestions: docSuggestions,
            value: exclude,
            onChange: value => isPro && setAttributes({
              exclude: value
            })
          })
        })]
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsxs)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.PanelBody, {
        title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Ordering', 'eazydocs'),
        initialOpen: false,
        children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.SelectControl, {
          label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Order By', 'eazydocs'),
          value: orderBy,
          options: orderByOptions,
          onChange: value => setAttributes({
            orderBy: value
          }),
          help: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Some options like Random may not preview correctly.', 'eazydocs')
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.SelectControl, {
          label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Parent Docs Order', 'eazydocs'),
          value: parent_docs_order,
          options: orderOptions,
          onChange: value => setAttributes({
            parent_docs_order: value
          })
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.SelectControl, {
          label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Child Docs Order', 'eazydocs'),
          value: child_docs_order,
          options: orderOptions,
          onChange: value => setAttributes({
            child_docs_order: value
          })
        })]
      })]
    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsxs)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1__.InspectorControls, {
      group: "styles",
      children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsxs)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.PanelBody, {
        title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Layout', 'eazydocs'),
        initialOpen: true,
        children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.RangeControl, {
          label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Columns', 'eazydocs'),
          value: col,
          onChange: value => setAttributes({
            col: value
          }),
          min: 1,
          max: 6
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.SelectControl, {
          label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Layout Type', 'eazydocs'),
          value: docs_layout,
          options: layoutOptions,
          onChange: value => setAttributes({
            docs_layout: value
          })
        }), isUsingProLayout && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(ProDemoNotice, {}), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.ToggleControl, {
          label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Compact Mode', 'eazydocs'),
          help: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Reduce padding and spacing for compact display.', 'eazydocs'),
          checked: compactMode,
          onChange: value => setAttributes({
            compactMode: value
          })
        })]
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsxs)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.PanelBody, {
        title: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.Fragment, {
          children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Tab Style', 'eazydocs')
        }),
        initialOpen: false,
        children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.SelectControl, {
          label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Style', 'eazydocs'),
          value: tabStyle,
          options: tabStyleOptions,
          onChange: value => setAttributes({
            tabStyle: value
          })
        }), isUsingProTabStyle && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(ProDemoNotice, {}), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.ToggleControl, {
          label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Show Doc Count', 'eazydocs'),
          help: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Display article count badge on each tab.', 'eazydocs'),
          checked: showDocCount,
          onChange: value => setAttributes({
            showDocCount: value
          })
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(LockedControl, {
          feature: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Display featured images on tabs.', 'eazydocs'),
          isPro: isPro,
          children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.ToggleControl, {
            label: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsxs)(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.Fragment, {
              children: [(0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Show Tab Featured Image', 'eazydocs'), !isPro && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(ProBadge, {
                inline: true
              })]
            }),
            help: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Display featured image/icon alongside tab titles.', 'eazydocs'),
            checked: showTabIcon,
            onChange: value => isPro && setAttributes({
              showTabIcon: value
            })
          })
        })]
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsxs)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.PanelBody, {
        title: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.Fragment, {
          children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Card Style', 'eazydocs')
        }),
        initialOpen: false,
        children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.SelectControl, {
          label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Style', 'eazydocs'),
          value: cardStyle,
          options: cardStyleOptions,
          onChange: value => setAttributes({
            cardStyle: value
          })
        }), isUsingProCardStyle && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(ProDemoNotice, {}), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(LockedControl, {
          feature: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Display featured images on section cards.', 'eazydocs'),
          isPro: isPro,
          children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.ToggleControl, {
            label: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsxs)(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.Fragment, {
              children: [(0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Show Featured Images', 'eazydocs'), !isPro && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(ProBadge, {
                inline: true
              })]
            }),
            help: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Display featured images on section cards.', 'eazydocs'),
            checked: isFeaturedImage,
            onChange: value => isPro && setAttributes({
              isFeaturedImage: value
            })
          })
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.ToggleControl, {
          label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Show Last Updated', 'eazydocs'),
          help: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Display last updated date on articles.', 'eazydocs'),
          checked: showLastUpdated,
          onChange: value => setAttributes({
            showLastUpdated: value
          })
        })]
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsxs)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.PanelBody, {
        title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Animation', 'eazydocs'),
        initialOpen: false,
        children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.ToggleControl, {
          label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Enable Hover Animation', 'eazydocs'),
          help: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)(enableHoverAnimation ? 'Cards will animate on hover' : 'No hover animation', 'eazydocs'),
          checked: enableHoverAnimation,
          onChange: value => setAttributes({
            enableHoverAnimation: value
          })
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.SelectControl, {
          label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Animation Speed', 'eazydocs'),
          value: animationSpeed,
          options: animationSpeedOptions,
          onChange: value => setAttributes({
            animationSpeed: value
          })
        })]
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.PanelBody, {
        title: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsxs)(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.Fragment, {
          children: [(0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Advanced Styling', 'eazydocs'), !isPro && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(ProBadge, {})]
        }),
        initialOpen: false,
        children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsxs)(LockedControl, {
          feature: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Advanced styling options', 'eazydocs'),
          isPro: isPro,
          children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.BaseControl, {
            label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Primary Color', 'eazydocs'),
            help: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Customize the main accent color for tabs and highlights.', 'eazydocs'),
            children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1__.ColorPalette, {
              value: primaryColor,
              onChange: value => isPro && setAttributes({
                primaryColor: value
              }),
              clearable: true
            })
          }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.__experimentalDivider, {}), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.RangeControl, {
            label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Card Padding', 'eazydocs'),
            value: cardPadding,
            onChange: value => isPro && setAttributes({
              cardPadding: value
            }),
            min: 8,
            max: 64,
            step: 4
          }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.RangeControl, {
            label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Card Gap', 'eazydocs'),
            value: cardGap,
            onChange: value => isPro && setAttributes({
              cardGap: value
            }),
            min: 8,
            max: 48,
            step: 4
          }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.RangeControl, {
            label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Border Radius', 'eazydocs'),
            value: borderRadius,
            onChange: value => isPro && setAttributes({
              borderRadius: value
            }),
            min: 0,
            max: 32,
            step: 2
          }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.__experimentalDivider, {}), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.ToggleControl, {
            label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Show Title Line', 'eazydocs'),
            help: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Display gradient line under section titles.', 'eazydocs'),
            checked: showTitleLine,
            onChange: value => isPro && setAttributes({
              showTitleLine: value
            })
          }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.SelectControl, {
            label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Button Style', 'eazydocs'),
            value: buttonStyle,
            options: buttonStyleOptions,
            onChange: value => isPro && setAttributes({
              buttonStyle: value
            })
          }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.SelectControl, {
            label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Tab Icon Style', 'eazydocs'),
            value: iconStyle,
            options: iconStyleOptions,
            onChange: value => isPro && setAttributes({
              iconStyle: value
            }),
            help: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Only visible when tab icons are enabled.', 'eazydocs')
          }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.SelectControl, {
            label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Shadow Intensity', 'eazydocs'),
            value: shadowIntensity,
            options: shadowIntensityOptions,
            onChange: value => isPro && setAttributes({
              shadowIntensity: value
            })
          })]
        })
      })]
    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsxs)("div", {
      ...(0,_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1__.useBlockProps)({
        className: wrapperClasses,
        style: customStyles
      }),
      children: [isUsingProFeature && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsxs)("div", {
        className: "ezd-pro-demo-banner",
        children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)("div", {
          className: "ezd-pro-demo-banner-icon",
          children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)("svg", {
            xmlns: "http://www.w3.org/2000/svg",
            width: "18",
            height: "18",
            viewBox: "0 0 24 24",
            fill: "currentColor",
            children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)("path", {
              d: "M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z"
            })
          })
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsxs)("div", {
          className: "ezd-pro-demo-banner-content",
          children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)("strong", {
            children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Pro Preview Mode', 'eazydocs')
          }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)("span", {
            children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Upgrade to apply this style on the frontend.', 'eazydocs')
          })]
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)("a", {
          className: "ezd-pro-demo-banner-btn",
          href: getPricingUrl(),
          children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Upgrade', 'eazydocs')
        })]
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsxs)("div", {
        className: "ezd-tabs-sliders",
        children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)("span", {
          className: "ezd-scroller-btn ezd-left ezd-inactive",
          children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)("svg", {
            xmlns: "http://www.w3.org/2000/svg",
            width: "20",
            height: "20",
            viewBox: "0 0 24 24",
            fill: "none",
            stroke: "currentColor",
            strokeWidth: "2",
            children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)("polyline", {
              points: "15 18 9 12 15 6"
            })
          })
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)("ul", {
          className: "ezd-tab-menu ezd-slide-nav-tabs",
          children: parentDocs.map(doc => {
            const articleCount = showDocCount ? getArticleCount(doc.id) : 0;
            const featuredMedia = doc.featured_media;
            return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)("li", {
              className: "ezd-nav-item",
              children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsxs)("button", {
                type: "button",
                className: `ezd-nav-link ${activeTab === doc.id ? 'ezd-active' : ''}`,
                onClick: () => setActiveTab(doc.id),
                children: [showTabIcon && featuredMedia > 0 && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(TabIcon, {
                  mediaId: featuredMedia
                }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)("span", {
                  className: "ezd-tab-text",
                  children: doc.title.rendered
                }), showDocCount && articleCount > 0 && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)("span", {
                  className: "ezd-tab-count",
                  children: articleCount
                })]
              })
            }, doc.id);
          })
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)("span", {
          className: "ezd-scroller-btn ezd-right ezd-inactive",
          children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)("svg", {
            xmlns: "http://www.w3.org/2000/svg",
            width: "20",
            height: "20",
            viewBox: "0 0 24 24",
            fill: "none",
            stroke: "currentColor",
            strokeWidth: "2",
            children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)("polyline", {
              points: "9 18 15 12 9 6"
            })
          })
        })]
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)("div", {
        className: "ezd-tab-content",
        children: parentDocs.map(parentDoc => {
          const childDocs = getChildDocs(parentDoc.id).slice(0, sectionsNumber === -1 ? undefined : Number(sectionsNumber));
          return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)("div", {
            className: `ezd-tab-pane ${activeTab === parentDoc.id ? 'ezd-active' : ''}`,
            children: childDocs.length === 0 ? /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsxs)("div", {
              className: "ezd-no-sections",
              children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsxs)("svg", {
                xmlns: "http://www.w3.org/2000/svg",
                width: "48",
                height: "48",
                viewBox: "0 0 24 24",
                fill: "none",
                stroke: "currentColor",
                strokeWidth: "1.5",
                children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)("path", {
                  d: "M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"
                }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)("polyline", {
                  points: "14 2 14 8 20 8"
                })]
              }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)("p", {
                children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('No sections found in this documentation.', 'eazydocs')
              })]
            }) : /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)("div", {
              className: layoutClass,
              children: childDocs.map(section => {
                // Apply articles limit - but always show in editor for demo
                const articlesLimit = isPro ? articlesNumber : -1;
                const articles = allDocs ? allDocs.filter(doc => doc.parent === section.id).slice(0, articlesLimit === -1 ? undefined : Number(articlesLimit)) : [];
                return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)("div", {
                  className: "ezd-section-card",
                  children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsxs)("div", {
                    className: "ezd-doc-tag-item",
                    children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsxs)("div", {
                      className: "ezd-doc-tag-title",
                      children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsxs)("h4", {
                        className: "ezd-item-title",
                        children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)("a", {
                          href: section.link,
                          onClick: e => e.preventDefault(),
                          children: section.title.rendered
                        }), articles.length > 0 && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsxs)("span", {
                          className: "ezd-section-count",
                          children: [articles.length, ' ', articles.length === 1 ? (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('article', 'eazydocs') : (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('articles', 'eazydocs')]
                        })]
                      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)("div", {
                        className: "ezd-line"
                      })]
                    }), articles.length > 0 ? /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)("ul", {
                      className: "ezd-tag-list",
                      children: articles.map(article => /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)("li", {
                        children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsxs)("a", {
                          href: article.link,
                          className: "ezd-item-list-title",
                          onClick: e => e.preventDefault(),
                          children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsxs)("svg", {
                            xmlns: "http://www.w3.org/2000/svg",
                            width: "16",
                            height: "16",
                            viewBox: "0 0 24 24",
                            fill: "none",
                            stroke: "currentColor",
                            strokeWidth: "2",
                            children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)("path", {
                              d: "M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"
                            }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)("polyline", {
                              points: "14 2 14 8 20 8"
                            }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)("line", {
                              x1: "16",
                              y1: "13",
                              x2: "8",
                              y2: "13"
                            }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)("line", {
                              x1: "16",
                              y1: "17",
                              x2: "8",
                              y2: "17"
                            }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)("polyline", {
                              points: "10 9 9 9 8 9"
                            })]
                          }), article.title.rendered]
                        })
                      }, article.id))
                    }) : /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)("p", {
                      className: "ezd-no-articles-msg",
                      children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('No articles in this section yet.', 'eazydocs')
                    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsxs)("a", {
                      href: section.link,
                      className: "ezd-text-btn ezd-dark-btn",
                      onClick: e => e.preventDefault(),
                      children: [readMoreText || (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('View All', 'eazydocs'), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsxs)("svg", {
                        xmlns: "http://www.w3.org/2000/svg",
                        width: "16",
                        height: "16",
                        viewBox: "0 0 24 24",
                        fill: "none",
                        stroke: "currentColor",
                        strokeWidth: "2",
                        children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)("line", {
                          x1: "5",
                          y1: "12",
                          x2: "19",
                          y2: "12"
                        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)("polyline", {
                          points: "12 5 19 12 12 19"
                        })]
                      })]
                    })]
                  })
                }, section.id);
              })
            })
          }, parentDoc.id);
        })
      })]
    })]
  });
}

/***/ }),

/***/ "./src/tabbed-docs/editor.scss":
/*!*************************************!*\
  !*** ./src/tabbed-docs/editor.scss ***!
  \*************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "./src/tabbed-docs/save.js":
/*!*********************************!*\
  !*** ./src/tabbed-docs/save.js ***!
  \*********************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ Save)
/* harmony export */ });
/**
 * Save component for Tabbed Docs block
 * Returns null as rendering is done server-side via PHP
 */
function Save() {
  return null;
}

/***/ }),

/***/ "@wordpress/block-editor":
/*!*************************************!*\
  !*** external ["wp","blockEditor"] ***!
  \*************************************/
/***/ ((module) => {

module.exports = window["wp"]["blockEditor"];

/***/ }),

/***/ "@wordpress/blocks":
/*!********************************!*\
  !*** external ["wp","blocks"] ***!
  \********************************/
/***/ ((module) => {

module.exports = window["wp"]["blocks"];

/***/ }),

/***/ "@wordpress/components":
/*!************************************!*\
  !*** external ["wp","components"] ***!
  \************************************/
/***/ ((module) => {

module.exports = window["wp"]["components"];

/***/ }),

/***/ "@wordpress/data":
/*!******************************!*\
  !*** external ["wp","data"] ***!
  \******************************/
/***/ ((module) => {

module.exports = window["wp"]["data"];

/***/ }),

/***/ "@wordpress/element":
/*!*********************************!*\
  !*** external ["wp","element"] ***!
  \*********************************/
/***/ ((module) => {

module.exports = window["wp"]["element"];

/***/ }),

/***/ "@wordpress/i18n":
/*!******************************!*\
  !*** external ["wp","i18n"] ***!
  \******************************/
/***/ ((module) => {

module.exports = window["wp"]["i18n"];

/***/ }),

/***/ "react/jsx-runtime":
/*!**********************************!*\
  !*** external "ReactJSXRuntime" ***!
  \**********************************/
/***/ ((module) => {

module.exports = window["ReactJSXRuntime"];

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	(() => {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = (module) => {
/******/ 			var getter = module && module.__esModule ?
/******/ 				() => (module['default']) :
/******/ 				() => (module);
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry needs to be wrapped in an IIFE because it needs to be isolated against other modules in the chunk.
(() => {
/*!**********************************!*\
  !*** ./src/tabbed-docs/index.js ***!
  \**********************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/blocks */ "@wordpress/blocks");
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _block_json__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./block.json */ "./src/tabbed-docs/block.json");
/* harmony import */ var _edit__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./edit */ "./src/tabbed-docs/edit.js");
/* harmony import */ var _save__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./save */ "./src/tabbed-docs/save.js");



/**
 * Internal dependencies
 */



/**
 * Block Registration
 */
(0,_wordpress_blocks__WEBPACK_IMPORTED_MODULE_0__.registerBlockType)(_block_json__WEBPACK_IMPORTED_MODULE_1__, {
  edit: _edit__WEBPACK_IMPORTED_MODULE_2__["default"],
  save: _save__WEBPACK_IMPORTED_MODULE_3__["default"]
});
})();

/******/ })()
;
//# sourceMappingURL=index.js.map