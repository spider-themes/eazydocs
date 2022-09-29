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
/* harmony export */   "doc_ids": () => (/* binding */ doc_ids)
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
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ Edit)
/* harmony export */ });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/block-editor */ "@wordpress/block-editor");
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _editor_scss__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./editor.scss */ "./src/shortcode/editor.scss");
/* harmony import */ var _custom_functions__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../custom-functions */ "./src/custom-functions.js");
/* harmony import */ var _colors_palette__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../colors-palette */ "./src/colors-palette.js");







const {
  Fragment
} = wp.element; // editor style

 // Custom functions

 // colors


function Edit(_ref) {
  let {
    attributes,
    setAttributes
  } = _ref;
  const {
    col,
    include,
    exclude,
    show_docs,
    show_articles,
    more,
    list
  } = attributes;
  const blockProps = (0,_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__.useBlockProps)();
  const docs = (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_4__.useSelect)(select => {
    return select("core").getEntityRecords('postType', 'docs', {
      parent: 0,
      status: ['publish', 'private']
    });
  }, []);
  const docSuggestions = docs && docs.map(doc => {
    return doc.id + " | " + doc.title.rendered;
  }); // console.log( docSuggestions )
  // Set attributes value

  const onChangeCol = newCol => {
    setAttributes({
      col: newCol == '' ? 3 : newCol
    });
  }; // Shortcode attributes


  let include_doc_ids = (0,_custom_functions__WEBPACK_IMPORTED_MODULE_6__.doc_ids)(include) ? 'include="' + (0,_custom_functions__WEBPACK_IMPORTED_MODULE_6__.doc_ids)(include) + '"' : '';
  let exclude_doc_ids = (0,_custom_functions__WEBPACK_IMPORTED_MODULE_6__.doc_ids)(exclude) ? 'exclude="' + (0,_custom_functions__WEBPACK_IMPORTED_MODULE_6__.doc_ids)(exclude) + '"' : '';
  let columns = col ? 'col="' + col + '"' : '';
  let ppp = show_docs ? 'show_docs="' + show_docs + '"' : '';
  let articles = show_articles ? 'show_articles="' + show_articles + '"' : '';
  let more_txt = more ? 'more="' + more + '"' : '';
  return (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(Fragment, null, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__.InspectorControls, null, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.PanelBody, {
    title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Filters', 'eazydocs'),
    initialOpen: true
  }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.RangeControl, {
    initialPosition: 3,
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Columns', 'eazydocs'),
    max: 4,
    min: 1,
    shiftStep: 1,
    onChange: onChangeCol
  }), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.__experimentalNumberControl, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Docs Show Count', 'eazydocs'),
    isShiftStepEnabled: true,
    onChange: value => setAttributes({
      show_docs: value
    }),
    shiftStep: 1,
    value: show_docs,
    min: 1,
    __nextHasNoMarginBottom: true
  }), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("small", null, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('How many docs to display.', 'eazydocs')), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("br", null), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("br", null), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.__experimentalNumberControl, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Docs Article Count', 'eazydocs'),
    isShiftStepEnabled: true,
    onChange: value => setAttributes({
      show_articles: value
    }),
    shiftStep: 1,
    value: show_articles,
    min: 1,
    __nextHasNoMarginBottom: true
  }), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("small", null, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Articles/child-docs show under every Docs.', 'eazydocs')), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("br", null), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("br", null), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.FormTokenField, {
    __experimentalAutoSelectFirstMatch: true,
    __experimentalExpandOnFocus: true,
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Docs to Show', 'eazydocs'),
    suggestions: docSuggestions,
    value: include,
    onChange: value => setAttributes({
      include: value
    })
  }), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.FormTokenField, {
    __experimentalAutoSelectFirstMatch: true,
    __experimentalExpandOnFocus: true,
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Docs Not to Show', 'eazydocs'),
    suggestions: docSuggestions,
    value: exclude,
    onChange: value => setAttributes({
      exclude: value
    })
  }), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.TextControl, {
    help: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Button/link to get the full docs', 'eazydocs'),
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('More Button Label', 'eazydocs'),
    value: more,
    onChange: value => setAttributes({
      more: value
    })
  }))), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", blockProps, "[eazydocs ", columns, " ", include_doc_ids, " ", exclude_doc_ids, " ", ppp, " ", articles, " ", more_txt, "]"));
}

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
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
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
    more
  } = props.attributes;
  const blockProps = _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1__.useBlockProps.save(); //  Shorlettcode attributes

  let include_doc_ids = (0,_custom_functions__WEBPACK_IMPORTED_MODULE_2__.doc_ids)(include) ? 'include="' + (0,_custom_functions__WEBPACK_IMPORTED_MODULE_2__.doc_ids)(include) + '"' : '';
  let exclude_doc_ids = (0,_custom_functions__WEBPACK_IMPORTED_MODULE_2__.doc_ids)(exclude) ? 'exclude="' + (0,_custom_functions__WEBPACK_IMPORTED_MODULE_2__.doc_ids)(exclude) + '"' : '';
  let columns = col ? 'col="' + col + '"' : '';
  let ppp = show_docs ? 'show_docs="' + show_docs + '"' : '';
  let articles = show_articles ? 'show_articles="' + show_articles + '"' : '';
  let more_txt = more ? 'more="' + more + '"' : '';
  return (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", blockProps, "[eazydocs ", columns, " ", include_doc_ids, " ", exclude_doc_ids, " ", ppp, " ", articles, " ", more_txt, "]"));
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

/***/ "./src/shortcode/block.json":
/*!**********************************!*\
  !*** ./src/shortcode/block.json ***!
  \**********************************/
/***/ ((module) => {

module.exports = JSON.parse('{"apiVersion":2,"name":"eazydocs/shortcode","version":"0.1.0","title":"EazyDocs Shortcode","category":"eazydocs","icon":"media-document","description":"Display the Docs on the website frontend.","supports":{"html":false,"anchor":true},"attributes":{"col":{"type":"number","default":3},"include":{"type":"array","default":[]},"exclude":{"type":"string"},"show_docs":{"type":"number"},"show_articles":{"type":"number"},"more":{"type":"string","default":"View Details"}},"textdomain":"eazydocs","editorScript":"file:./index.js"}');

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
// This entry need to be wrapped in an IIFE because it need to be isolated against other modules in the chunk.
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