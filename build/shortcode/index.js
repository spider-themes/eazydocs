/******/ (function() { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./src/colors-palette.js":
/*!*******************************!*\
  !*** ./src/colors-palette.js ***!
  \*******************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
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
/* harmony default export */ __webpack_exports__["default"] = (colors);

/***/ }),

/***/ "./src/custom-functions.js":
/*!*********************************!*\
  !*** ./src/custom-functions.js ***!
  \*********************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   doc_ids: function() { return /* binding */ doc_ids; }
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

/***/ "./src/shortcode/edit.js":
/*!*******************************!*\
  !*** ./src/shortcode/edit.js ***!
  \*******************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ Edit; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/block-editor */ "@wordpress/block-editor");
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _editor_scss__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./editor.scss */ "./src/shortcode/editor.scss");
/* harmony import */ var _custom_functions__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../custom-functions */ "./src/custom-functions.js");
/* harmony import */ var _colors_palette__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ../colors-palette */ "./src/colors-palette.js");







const {
  Fragment
} = wp.element; // editor style

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
    docs_layout
  } = attributes;
  const blockProps = (0,_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__.useBlockProps)();
  const docs = (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_5__.useSelect)(select => {
    return select("core").getEntityRecords('postType', 'docs', {
      parent: 0,
      status: ['publish', 'private']
    });
  }, []);
  const docSuggestions = docs ? docs.map(doc => doc.id + " | " + doc.title.rendered) : []; // console.log( docSuggestions )
  // Set attributes value

  const onChangeCol = newCol => {
    setAttributes({
      col: newCol == '' ? 3 : newCol
    });
  };

  const orderOptions = [{
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Ascending', 'eazydocs'),
    value: 'asc'
  }, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Descending', 'eazydocs'),
    value: 'desc'
  }];
  const layoutOptions = [{
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Masonry', 'eazydocs'),
    value: 'masonry'
  }, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Grid', 'eazydocs'),
    value: 'grid'
  }];
  const parentOrderOptions = [{
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('No Order', 'eazydocs'),
    value: 'none'
  }, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Post ID', 'eazydocs'),
    value: 'ID'
  }, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Post Author', 'eazydocs'),
    value: 'author'
  }, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Title', 'eazydocs'),
    value: 'title'
  }, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Date', 'eazydocs'),
    value: 'date'
  }, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Last Modified Date', 'eazydocs'),
    value: 'modified'
  }, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Random', 'eazydocs'),
    value: 'rand'
  }, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Comment Count', 'eazydocs'),
    value: 'comment_count'
  }, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Menu Order', 'eazydocs'),
    value: 'menu_order'
  }]; // Shortcode attributes

  let include_doc_ids = (0,_custom_functions__WEBPACK_IMPORTED_MODULE_7__.doc_ids)(include) ? 'include="' + (0,_custom_functions__WEBPACK_IMPORTED_MODULE_7__.doc_ids)(include) + '"' : '';
  let exclude_doc_ids = (0,_custom_functions__WEBPACK_IMPORTED_MODULE_7__.doc_ids)(exclude) ? 'exclude="' + (0,_custom_functions__WEBPACK_IMPORTED_MODULE_7__.doc_ids)(exclude) + '"' : '';
  let columns = col ? 'col="' + col + '"' : '';
  let ppp = show_docs ? 'show_docs="' + show_docs + '"' : '';
  let articles = show_articles ? 'show_articles="' + show_articles + '"' : '';
  let more_txt = more ? 'more="' + more + '"' : '';
  const proAlert = eazydocs_local_object.is_ezd_premium == 'yes' ? '' : 'Pro';
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(Fragment, null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__.InspectorControls, null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.PanelBody, {
    title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Filters', 'eazydocs'),
    initialOpen: true
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.RangeControl, {
    initialPosition: 3,
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Columns', 'eazydocs'),
    max: 4,
    min: 1,
    shiftStep: 1,
    onChange: onChangeCol
  }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.TextControl, {
    help: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Button/link to get the full docs', 'eazydocs'),
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('View More Button', 'eazydocs'),
    value: more,
    onChange: value => setAttributes({
      more: value
    })
  }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.CheckboxControl, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Show Topic', 'eazydocs'),
    checked: show_topic,
    onChange: value => setAttributes({
      show_topic: value
    })
  }), show_topic && (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.TextControl, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Topics Count Text', 'eazydocs'),
    value: topic_label,
    onChange: value => setAttributes({
      topic_label: value
    })
  }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.SelectControl, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Parent Docs Order ' + `${proAlert}`, 'eazydocs'),
    value: parent_docs_order,
    options: parentOrderOptions,
    disabled: eazydocs_local_object.is_ezd_premium == 'yes' ? false : true,
    className: "eazydocs-pro-notice",
    onChange: value => setAttributes({
      parent_docs_order: value
    })
  }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.SelectControl, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Child Docs Order', 'eazydocs'),
    value: child_docs_order,
    options: orderOptions,
    onChange: value => setAttributes({
      child_docs_order: value
    })
  }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.__experimentalNumberControl, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Number of Docs', 'eazydocs'),
    isShiftStepEnabled: true,
    onChange: value => setAttributes({
      show_docs: value
    }),
    shiftStep: 1,
    value: show_docs,
    min: 1,
    __nextHasNoMarginBottom: true,
    help: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Number of Main Docs to show', 'eazydocs')
  }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.__experimentalNumberControl, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Number of Articles', 'eazydocs'),
    isShiftStepEnabled: true,
    onChange: value => setAttributes({
      show_articles: value
    }),
    shiftStep: 1,
    value: show_articles,
    min: 1,
    __nextHasNoMarginBottom: true,
    help: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Number of Articles to show under each Docs.', 'eazydocs')
  }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.RadioControl, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Docs Layout ' + `${proAlert}`, 'eazydocs'),
    selected: docs_layout,
    options: layoutOptions,
    disabled: eazydocs_local_object.is_ezd_premium == 'yes' ? false : true,
    onChange: value => setAttributes({
      docs_layout: value
    })
  }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.FormTokenField, {
    __experimentalAutoSelectFirstMatch: true,
    __experimentalExpandOnFocus: true,
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Docs to Show', 'eazydocs'),
    suggestions: docSuggestions,
    value: include,
    onChange: value => setAttributes({
      include: value
    })
  }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.FormTokenField, {
    __experimentalAutoSelectFirstMatch: true,
    __experimentalExpandOnFocus: true,
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Docs Not to Show', 'eazydocs'),
    suggestions: docSuggestions,
    value: exclude,
    onChange: value => setAttributes({
      exclude: value
    })
  }))), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", { ...blockProps
  }, "[eazydocs ", columns, " ", include_doc_ids, " ", exclude_doc_ids, " ", ppp, " ", articles, " ", more_txt, "]"));
}

/***/ }),

/***/ "./src/shortcode/save.js":
/*!*******************************!*\
  !*** ./src/shortcode/save.js ***!
  \*******************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ save; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/block-editor */ "@wordpress/block-editor");
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _custom_functions__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../custom-functions */ "./src/custom-functions.js");

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
    show_topic,
    topic_label,
    docs_layout
  } = props.attributes;
  const blockProps = _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1__.useBlockProps.save(); //  Shorlettcode attributes

  let include_doc_ids = (0,_custom_functions__WEBPACK_IMPORTED_MODULE_2__.doc_ids)(include) ? 'include="' + (0,_custom_functions__WEBPACK_IMPORTED_MODULE_2__.doc_ids)(include) + '"' : '';
  let exclude_doc_ids = (0,_custom_functions__WEBPACK_IMPORTED_MODULE_2__.doc_ids)(exclude) ? 'exclude="' + (0,_custom_functions__WEBPACK_IMPORTED_MODULE_2__.doc_ids)(exclude) + '"' : '';
  let columns = col ? 'col="' + col + '"' : '';
  let ppp = show_docs ? 'show_docs="' + show_docs + '"' : '';
  let articles = show_articles ? 'show_articles="' + show_articles + '"' : '';
  let more_txt = more ? 'more="' + more + '"' : '';
  let is_topic = show_topic ? 'show_topic="' + show_topic + '"' : '';
  let is_topic_label = is_topic ? 'topic_label="' + topic_label + '"' : '';
  let is_parent_docs_order = parent_docs_order ? 'parent_docs_order="' + parent_docs_order + '"' : '';
  let is_child_docs_order = child_docs_order ? 'child_docs_order="' + child_docs_order + '"' : '';
  let is_docs_layout = docs_layout ? 'docs_layout="' + docs_layout + '"' : '';
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", { ...blockProps
  }, "[eazydocs ", columns, " ", include_doc_ids, " ", exclude_doc_ids, " ", ppp, " ", articles, " ", more_txt, " ", is_topic, " ", is_topic_label, " ", is_parent_docs_order, " ", is_child_docs_order, " ", is_docs_layout, "]"));
}

/***/ }),

/***/ "./src/shortcode/editor.scss":
/*!***********************************!*\
  !*** ./src/shortcode/editor.scss ***!
  \***********************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "react":
/*!************************!*\
  !*** external "React" ***!
  \************************/
/***/ (function(module) {

module.exports = window["React"];

/***/ }),

/***/ "@wordpress/block-editor":
/*!*************************************!*\
  !*** external ["wp","blockEditor"] ***!
  \*************************************/
/***/ (function(module) {

module.exports = window["wp"]["blockEditor"];

/***/ }),

/***/ "@wordpress/blocks":
/*!********************************!*\
  !*** external ["wp","blocks"] ***!
  \********************************/
/***/ (function(module) {

module.exports = window["wp"]["blocks"];

/***/ }),

/***/ "@wordpress/components":
/*!************************************!*\
  !*** external ["wp","components"] ***!
  \************************************/
/***/ (function(module) {

module.exports = window["wp"]["components"];

/***/ }),

/***/ "@wordpress/data":
/*!******************************!*\
  !*** external ["wp","data"] ***!
  \******************************/
/***/ (function(module) {

module.exports = window["wp"]["data"];

/***/ }),

/***/ "@wordpress/element":
/*!*********************************!*\
  !*** external ["wp","element"] ***!
  \*********************************/
/***/ (function(module) {

module.exports = window["wp"]["element"];

/***/ }),

/***/ "@wordpress/i18n":
/*!******************************!*\
  !*** external ["wp","i18n"] ***!
  \******************************/
/***/ (function(module) {

module.exports = window["wp"]["i18n"];

/***/ }),

/***/ "./src/shortcode/block.json":
/*!**********************************!*\
  !*** ./src/shortcode/block.json ***!
  \**********************************/
/***/ (function(module) {

module.exports = JSON.parse('{"apiVersion":2,"name":"eazydocs/shortcode","version":"0.1.0","title":"EazyDocs Shortcode","category":"eazydocs","icon":"media-document","description":"Display the Docs on the website frontend.","supports":{"html":false,"anchor":true},"attributes":{"col":{"type":"number","default":3},"include":{"type":"array","default":[]},"exclude":{"type":"string"},"show_docs":{"type":"number"},"show_articles":{"type":"number"},"more":{"type":"string","default":"View Details"},"show_topic":{"type":"checkbox","default":true},"topic_label":{"type":"string","default":"Topics"},"parent_docs_order":{"type":"string","default":"none"},"child_docs_order":{"type":"string","default":"desc"},"docs_layout":{"type":"string","default":"grid"}},"textdomain":"eazydocs","editorScript":"file:./index.js"}');

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
/******/ 	!function() {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = function(module) {
/******/ 			var getter = module && module.__esModule ?
/******/ 				function() { return module['default']; } :
/******/ 				function() { return module; };
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	!function() {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = function(exports, definition) {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	!function() {
/******/ 		__webpack_require__.o = function(obj, prop) { return Object.prototype.hasOwnProperty.call(obj, prop); }
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	!function() {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = function(exports) {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	}();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry need to be wrapped in an IIFE because it need to be isolated against other modules in the chunk.
!function() {
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
}();
/******/ })()
;
//# sourceMappingURL=index.js.map