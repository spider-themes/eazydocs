/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./src/eazydocs-toolbar/editor.scss":
/*!******************************************!*\
  !*** ./src/eazydocs-toolbar/editor.scss ***!
  \******************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "react/jsx-runtime":
/*!**********************************!*\
  !*** external "ReactJSXRuntime" ***!
  \**********************************/
/***/ ((module) => {

module.exports = window["ReactJSXRuntime"];

/***/ }),

/***/ "@wordpress/block-editor":
/*!*************************************!*\
  !*** external ["wp","blockEditor"] ***!
  \*************************************/
/***/ ((module) => {

module.exports = window["wp"]["blockEditor"];

/***/ }),

/***/ "@wordpress/components":
/*!************************************!*\
  !*** external ["wp","components"] ***!
  \************************************/
/***/ ((module) => {

module.exports = window["wp"]["components"];

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

/***/ "@wordpress/rich-text":
/*!**********************************!*\
  !*** external ["wp","richText"] ***!
  \**********************************/
/***/ ((module) => {

module.exports = window["wp"]["richText"];

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
/*!***************************************!*\
  !*** ./src/eazydocs-toolbar/index.js ***!
  \***************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/block-editor */ "@wordpress/block-editor");
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_rich_text__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/rich-text */ "@wordpress/rich-text");
/* harmony import */ var _wordpress_rich_text__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _editor_scss__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./editor.scss */ "./src/eazydocs-toolbar/editor.scss");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! react/jsx-runtime */ "react/jsx-runtime");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__);







const name = 'eazydocs/eazydocs-toolbar';
const EazyDocs_Toolbar = ({
  isActive,
  value,
  onChange
}) => {
  const [showPopover, setShowPopover] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)(false);
  const [numberValue, setNumberValue] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)('');
  const [shortcodeCounter, setShortcodeCounter] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)(1);
  const conditionalItems = eazydocs_local_object.ezd_get_conditional_items;
  const dataItems = conditionalItems.map(item => /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)("option", {
    value: item.value,
    children: item.title
  }, item.id));

  // Footnotes
  const reference = () => {
    if (isActive) {
      onChange((0,_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_4__.removeFormat)(value, name));
      return;
    }
    const selectedText = value.text.slice(value.start, value.end);
    let shortcode = '';

    // Get the number of footnotes in the editor
    let shortcodeNumber = jQuery('.is-root-container p').text().match(/\[reference number="(\d+)"\]/g);
    if (shortcodeNumber !== null) {
      shortcodeNumber = shortcodeNumber.length + 1;
    } else {
      shortcodeNumber = 1;
    }

    // Wrap selected text with shortcode if text is selected
    if (selectedText) {
      shortcode = `[reference number="${shortcodeNumber}"]${selectedText}[/reference]`;
    } else {
      // Insert shortcode at cursor position if no text is selected
      shortcode = `[reference number="${shortcodeNumber}"][/reference]`;
    }
    onChange((0,_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_4__.insert)(value, shortcode));
  };

  // Conditional Dropdown
  const conditional_data = () => {
    if (isActive) {
      onChange((0,_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_4__.removeFormat)(value, name));
      return;
    }
    setShowPopover(true);
  };

  // Insert shortcode with the selected value into the rich text
  const ezdToolbarDropDown = selectedValue => {
    // Insert shortcode with the selected value into the rich text
    const shortcodeNumber = shortcodeCounter;
    setShortcodeCounter(shortcodeCounter + 1);
    const selectedText = value.text.slice(value.start, value.end);
    let shortcode = '';

    // Wrap selected text with shortcode if text is selected
    if (selectedText) {
      shortcode = `[conditional_data dependency="${selectedValue}"]${selectedText}[/conditional_data]`;
    } else {
      // Insert shortcode at cursor position if no text is selected
      shortcode = `[conditional_data dependency="${selectedValue}"][/conditional_data]`;
    }
    onChange((0,_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_4__.insert)(value, shortcode));

    // Hide the popover after insertion
    setShowPopover(false);
  };
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsxs)(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.Fragment, {
    children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__.BlockControls, {
      children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.ToolbarGroup, {
        children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.DropdownMenu, {
          className: "eazydocs-toolbar__dropdown",
          icon: "ezd-icon",
          label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Insert EazyDocs Shortcode', 'eazydocs'),
          controls: [{
            title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Footnotes', 'eazydocs'),
            onClick: reference
          }, {
            title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Conditional Dropdown', 'eazydocs'),
            onClick: conditional_data
          }]
        })
      })
    }), showPopover && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsxs)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.Popover, {
      className: "ezd-conditional-dropdown-tool",
      position: "bottom center",
      onClose: () => setShowPopover(false),
      children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsxs)("select", {
        value: numberValue,
        onChange: e => setNumberValue(e.target.value),
        children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)("option", {
          value: "",
          children: "-- Select Option --"
        }), dataItems]
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_6__.jsx)("button", {
        onClick: () => ezdToolbarDropDown(numberValue),
        children: "Insert"
      })]
    })]
  });
};
(0,_wordpress_rich_text__WEBPACK_IMPORTED_MODULE_4__.registerFormatType)(name, {
  title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('EazyDocs Toolbar', 'eazydocs'),
  tagName: 'span',
  className: 'eazydocs-toolbar',
  edit: EazyDocs_Toolbar
});
})();

/******/ })()
;
//# sourceMappingURL=index.js.map