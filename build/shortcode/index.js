/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./src/colors-palette.js":
/*!*******************************!*\
  !*** ./src/colors-palette.js ***!
  \*******************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
const {
  __
} = wp.i18n;
const colors = [{
  name: __('Black', 'boilerplate'),
  color: '#000000'
}, {
  name: __('White', 'boilerplate'),
  color: '#ffffff'
}, {
  name: __('Red', 'boilerplate'),
  color: '#ff0000'
}, {
  name: __('Green', 'boilerplate'),
  color: '#00ff00'
}, {
  name: __('Blue', 'boilerplate'),
  color: '#0000ff'
}, {
  name: __('Yellow', 'boilerplate'),
  color: '#ffff00'
}];
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (colors);

/***/ }),

/***/ "./src/custom-functions.js":
/*!*********************************!*\
  !*** ./src/custom-functions.js ***!
  \*********************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   doc_ids: () => (/* binding */ doc_ids)
/* harmony export */ });
/**
 * Custom Functions
 */
function doc_ids(arr) {
  var doc_ids = '';
  if (arr) {
    for (let i = 0; i < arr.length; i++) {
      let doc_split = arr[i].split('|');
      let doc_id = doc_split[0].trim();
      let comma = i === arr.length - 1 ? '' : ',';
      doc_ids += doc_id + comma;
    }
  }
  return doc_ids;
}

/***/ }),

/***/ "./src/shortcode/block.json":
/*!**********************************!*\
  !*** ./src/shortcode/block.json ***!
  \**********************************/
/***/ ((module) => {

module.exports = /*#__PURE__*/JSON.parse('{"apiVersion":2,"name":"eazydocs/shortcode","version":"0.1.0","title":"EazyDocs Shortcode","category":"eazydocs","icon":"media-document","description":"Display the Docs on the website frontend.","supports":{"html":false,"anchor":true},"attributes":{"col":{"type":"number","default":3},"include":{"type":"array","default":[]},"exclude":{"type":"string"},"show_docs":{"type":"number"},"show_articles":{"type":"number"},"more":{"type":"string","default":"View Details"},"show_topic":{"type":"checkbox","default":true},"topic_label":{"type":"string","default":"Topics"},"parent_docs_order":{"type":"string","default":"menu_order"},"parent_docs_order_by":{"type":"string","default":"asc"},"child_docs_order":{"type":"string","default":"desc"},"docs_layout":{"type":"string","default":"grid"}},"textdomain":"eazydocs","editorScript":"file:./index.js"}');

/***/ }),

/***/ "./src/shortcode/edit.js":
/*!*******************************!*\
  !*** ./src/shortcode/edit.js ***!
  \*******************************/
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
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _editor_scss__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./editor.scss */ "./src/shortcode/editor.scss");
/* harmony import */ var _custom_functions__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../custom-functions */ "./src/custom-functions.js");
/* harmony import */ var _colors_palette__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../colors-palette */ "./src/colors-palette.js");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! react/jsx-runtime */ "react/jsx-runtime");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_8__);






const {
  Fragment
} = wp.element;

// editor style


// Custom functions


// colors


function Edit({
  attributes,
  setAttributes
}) {
  const {
    col,
    include,
    exclude,
    show_docs,
    show_articles,
    more,
    list,
    show_topic,
    topic_label,
    child_docs_order,
    parent_docs_order,
    parent_docs_order_by,
    docs_layout
  } = attributes;
  const blockProps = (0,_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1__.useBlockProps)();
  const docs = (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_4__.useSelect)(select => {
    return select("core").getEntityRecords('postType', 'docs', {
      parent: 0,
      status: ['publish', 'private']
    });
  }, []);
  const docSuggestions = docs ? docs.map(doc => doc.id + " | " + doc.title.rendered) : [];

  // console.log( docSuggestions )

  // Set attributes value
  const onChangeCol = newCol => {
    setAttributes({
      col: newCol == '' ? 3 : newCol
    });
  };
  const orderOptions = [{
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Ascending', 'eazydocs'),
    value: 'asc'
  }, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Descending', 'eazydocs'),
    value: 'desc'
  }];
  const layoutOptions = [{
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Masonry', 'eazydocs'),
    value: 'masonry'
  }, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Grid', 'eazydocs'),
    value: 'grid'
  }];
  const parentOrderOptions = [{
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('No Order', 'eazydocs'),
    value: 'none'
  }, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Post ID', 'eazydocs'),
    value: 'ID'
  }, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Post Author', 'eazydocs'),
    value: 'author'
  }, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Title', 'eazydocs'),
    value: 'title'
  }, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Date', 'eazydocs'),
    value: 'date'
  }, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Last Modified Date', 'eazydocs'),
    value: 'modified'
  }, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Random', 'eazydocs'),
    value: 'rand'
  }, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Comment Count', 'eazydocs'),
    value: 'comment_count'
  }, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Menu Order', 'eazydocs'),
    value: 'menu_order'
  }];

  // Shortcode attributes
  let include_doc_ids = (0,_custom_functions__WEBPACK_IMPORTED_MODULE_6__.doc_ids)(include) ? 'include="' + (0,_custom_functions__WEBPACK_IMPORTED_MODULE_6__.doc_ids)(include) + '"' : '';
  let exclude_doc_ids = (0,_custom_functions__WEBPACK_IMPORTED_MODULE_6__.doc_ids)(exclude) ? 'exclude="' + (0,_custom_functions__WEBPACK_IMPORTED_MODULE_6__.doc_ids)(exclude) + '"' : '';
  let columns = col ? 'col="' + col + '"' : '';
  let ppp = show_docs ? 'show_docs="' + show_docs + '"' : '';
  let articles = show_articles ? 'show_articles="' + show_articles + '"' : '';
  let more_txt = more ? 'more="' + more + '"' : '';
  jQuery('.eazydocs-pro-block-notice').on('click', function (e) {
    e.preventDefault();
    let href = jQuery(this).attr('href');
    Swal.fire({
      title: 'Opps...',
      html: 'This is a PRO feature. You need to <a href="admin.php?page=eazydocs-pricing"><strong class="upgrade-link">Upgrade&nbsp;&nbsp;➤</strong></a> to the Premium Version to use this feature',
      icon: "warning",
      buttons: [false, "Close"],
      dangerMode: true
    });
  });
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_8__.jsxs)(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_8__.Fragment, {
    children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_8__.jsx)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1__.InspectorControls, {
      children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_8__.jsxs)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.PanelBody, {
        title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Filters', 'eazydocs'),
        initialOpen: true,
        children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_8__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.RangeControl, {
          value: col // Bind the control value to the attribute
          ,
          initialPosition: 3,
          label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Columns', 'eazydocs'),
          max: 4,
          min: 1,
          shiftStep: 1,
          onChange: value => setAttributes({
            col: value
          }) // Update the attribute when changed
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_8__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.TextControl, {
          help: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Button/link to get the full docs', 'eazydocs'),
          label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('View More Button', 'eazydocs'),
          value: more,
          onChange: value => setAttributes({
            more: value
          })
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_8__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.CheckboxControl, {
          label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Show Topic', 'eazydocs'),
          checked: show_topic,
          onChange: value => setAttributes({
            show_topic: value
          })
        }), show_topic && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_8__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.TextControl, {
          label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Topics Count Text', 'eazydocs'),
          value: topic_label,
          onChange: value => setAttributes({
            topic_label: value
          })
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_8__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.SelectControl, {
          label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Parent Docs Order By', 'eazydocs'),
          value: parent_docs_order || 'menu_order',
          options: parentOrderOptions,
          className: eazydocs_local_object.is_ezd_pro_block == 'yes' ? '' : 'eazydocs-pro-block-notice',
          onChange: value => setAttributes({
            parent_docs_order: value
          })
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_8__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.SelectControl, {
          label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Parent Docs Order', 'eazydocs'),
          value: parent_docs_order_by || 'asc',
          options: orderOptions,
          className: eazydocs_local_object.is_ezd_pro_block == 'yes' ? '' : 'eazydocs-pro-block-notice',
          onChange: value => setAttributes({
            parent_docs_order_by: value
          })
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_8__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.SelectControl, {
          label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Child Docs Order', 'eazydocs'),
          value: child_docs_order,
          options: orderOptions,
          onChange: value => setAttributes({
            child_docs_order: value
          })
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_8__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.__experimentalNumberControl, {
          label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Number of Docs', 'eazydocs'),
          isShiftStepEnabled: true,
          onChange: value => setAttributes({
            show_docs: value
          }),
          shiftStep: 1,
          value: show_docs,
          min: 1,
          __nextHasNoMarginBottom: true,
          help: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Number of Main Docs to show', 'eazydocs')
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_8__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.__experimentalNumberControl, {
          label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Number of Articles', 'eazydocs'),
          isShiftStepEnabled: true,
          onChange: value => setAttributes({
            show_articles: value
          }),
          shiftStep: 1,
          value: show_articles,
          min: 1,
          __nextHasNoMarginBottom: true,
          help: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Number of Articles to show under each Docs.', 'eazydocs')
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_8__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.RadioControl, {
          label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Docs Layout ', 'eazydocs'),
          selected: docs_layout,
          options: layoutOptions,
          className: eazydocs_local_object.is_ezd_pro_block == 'yes' ? '' : 'eazydocs-pro-block-notice',
          onChange: value => setAttributes({
            docs_layout: value
          })
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_8__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.FormTokenField, {
          __experimentalAutoSelectFirstMatch: true,
          __experimentalExpandOnFocus: true,
          label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Docs to Show', 'eazydocs'),
          suggestions: docSuggestions,
          value: include,
          onChange: value => setAttributes({
            include: value
          })
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_8__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.FormTokenField, {
          __experimentalAutoSelectFirstMatch: true,
          __experimentalExpandOnFocus: true,
          label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Docs Not to Show', 'eazydocs'),
          suggestions: docSuggestions,
          value: exclude,
          onChange: value => setAttributes({
            exclude: value
          })
        })]
      })
    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_8__.jsxs)("div", {
      ...blockProps,
      children: ["[eazydocs ", columns, " ", include_doc_ids, " ", exclude_doc_ids, " ", ppp, " ", articles, " ", more_txt, "]"]
    })]
  });
}

/***/ }),

/***/ "./src/shortcode/editor.scss":
/*!***********************************!*\
  !*** ./src/shortcode/editor.scss ***!
  \***********************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "./src/shortcode/save.js":
/*!*******************************!*\
  !*** ./src/shortcode/save.js ***!
  \*******************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ save)
/* harmony export */ });
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/block-editor */ "@wordpress/block-editor");
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _custom_functions__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../custom-functions */ "./src/custom-functions.js");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! react/jsx-runtime */ "react/jsx-runtime");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__);
// import { __ } from '@wordpress/i18n';


// Custom functions


/**
 * The save function defines the way in which the different attributes should
 * be combined into the final markup, which is then serialized by the block
 * editor into `post_content`.
 *
 * @see https://developer.wordpress.org/block-editor/developers/block-api/block-edit-save/#save
 *
 * @return {WPElement} Element to render.
 */

function save(props) {
  const {
    col,
    include,
    exclude,
    show_docs,
    show_articles,
    more,
    parent_docs_order,
    child_docs_order,
    parent_docs_order_by,
    show_topic,
    topic_label,
    docs_layout
  } = props.attributes;
  const blockProps = _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_0__.useBlockProps.save();

  //  Shorlettcode attributes
  let include_doc_ids = (0,_custom_functions__WEBPACK_IMPORTED_MODULE_1__.doc_ids)(include) ? 'include="' + (0,_custom_functions__WEBPACK_IMPORTED_MODULE_1__.doc_ids)(include) + '"' : '';
  let exclude_doc_ids = (0,_custom_functions__WEBPACK_IMPORTED_MODULE_1__.doc_ids)(exclude) ? 'exclude="' + (0,_custom_functions__WEBPACK_IMPORTED_MODULE_1__.doc_ids)(exclude) + '"' : '';
  let columns = col ? 'col="' + col + '"' : '';
  let ppp = show_docs ? 'show_docs="' + show_docs + '"' : '';
  let articles = show_articles ? 'show_articles="' + show_articles + '"' : '';
  let more_txt = more ? 'more="' + more + '"' : '';
  let is_topic = show_topic ? 'show_topic="' + show_topic + '"' : '';
  let is_topic_label = is_topic ? 'topic_label="' + topic_label + '"' : '';
  let is_parent_docs_order = parent_docs_order ? 'parent_docs_order="' + parent_docs_order + '"' : '';
  let is_child_docs_order = child_docs_order ? 'child_docs_order="' + child_docs_order + '"' : '';
  let is_parent_docs_order_by = parent_docs_order_by ? 'parent_docs_order_by="' + parent_docs_order_by + '"' : '';
  let is_docs_layout = docs_layout ? 'docs_layout="' + docs_layout + '"' : '';
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__.jsx)(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__.Fragment, {
    children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__.jsxs)("div", {
      ...blockProps,
      children: ["[eazydocs ", columns, " ", include_doc_ids, " ", exclude_doc_ids, " ", ppp, " ", articles, " ", more_txt, " ", is_topic, " ", is_topic_label, " ", is_parent_docs_order, " ", is_child_docs_order, " ", is_parent_docs_order_by, " ", is_docs_layout, "]"]
    })
  });
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
/*!********************************!*\
  !*** ./src/shortcode/index.js ***!
  \********************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/blocks */ "@wordpress/blocks");
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _block_json__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./block.json */ "./src/shortcode/block.json");
/* harmony import */ var _edit__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./edit */ "./src/shortcode/edit.js");
/* harmony import */ var _save__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./save */ "./src/shortcode/save.js");

//import './style.scss';



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