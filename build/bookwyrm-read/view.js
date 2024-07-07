/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./src/inc/shared.js":
/*!***************************!*\
  !*** ./src/inc/shared.js ***!
  \***************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   configurePluginMessage: () => (/* binding */ configurePluginMessage),
/* harmony export */   createBookDiv: () => (/* binding */ createBookDiv),
/* harmony export */   getBookCover: () => (/* binding */ getBookCover),
/* harmony export */   renderError: () => (/* binding */ renderError),
/* harmony export */   trimProtocolAndTrailingSlash: () => (/* binding */ trimProtocolAndTrailingSlash)
/* harmony export */ });
function createBookDiv() {
  let newElement = document.createElement('div');
  return newElement;
}
function getBookCover(isbn) {
  /* get covers from OpenLibrary as they are more standardized 
  	Format: https://covers.openlibrary.org/b/isbn/9780385533225-S.jpg
  */
  return `https://covers.openlibrary.org/b/isbn/${isbn}-L.jpg`;
}
function renderError(container) {
  const newError = document.createElement('p');
  newError.textContent = '⚠️ Sorry, there has been an error fetching the feed.';
  newError.style.fontWeight = 'bold';
  const readListDiv = document.querySelector(container);
  document.querySelector(container).style.display = 'block';
  readListDiv.appendChild(newError);
}
function trimProtocolAndTrailingSlash(url) {
  return url.replace(/(http(s)?:\/\/)|(\/+$)/g, '').replace(/\/+/g, '/');
}
function configurePluginMessage(configured) {
  if (configured === 'no') return `⚠️ Configure your BookWyrm username and instance in the block settings.`;else return `This message only appears in the editor.`;
}

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
/*!***********************************!*\
  !*** ./src/bookwyrm-read/view.js ***!
  \***********************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _inc_shared__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../inc/shared */ "./src/inc/shared.js");
/**
 * Use this file for JavaScript code that you want to run in the front-end
 * on posts/pages that contain this block.
 *
 * When this file is defined as the value of the `viewScript` property
 * in `block.json` it will be enqueued on the front end of the site.
 *
 * Example:
 *
 * ```js
 * {
 *   "viewScript": "file:./view.js"
 * }
 * ```
 *
 * If you're not making any changes to this file because your project doesn't need any
 * JavaScript running in the front-end, then you should delete this file and remove
 * the `viewScript` property from `block.json`.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-metadata/#view-script
 */

/* eslint-disable no-console */

/* eslint-enable no-console */

/* Default config */


(function () {
  const bookwyrmCcontainerElement = document.querySelector('.wp-block-bookwyrm-blocks-bookwyrm-read-block');
  let BOOKWYRM_USER = bookwyrmCcontainerElement.getAttribute('data-user');
  let BOOKWYRM_INSTANCE = (0,_inc_shared__WEBPACK_IMPORTED_MODULE_0__.trimProtocolAndTrailingSlash)(bookwyrmCcontainerElement.getAttribute('data-instance'));
  if (!BOOKWYRM_USER || BOOKWYRM_USER == '' || BOOKWYRM_USER == null || !BOOKWYRM_INSTANCE || BOOKWYRM_INSTANCE == '' || BOOKWYRM_INSTANCE == null) {
    (0,_inc_shared__WEBPACK_IMPORTED_MODULE_0__.renderError)('div.read--list');
    return;
  }
  let readUrl = `https://corsproxy.io/?https%3A%2F%2F${BOOKWYRM_INSTANCE}%2Fuser%2F${BOOKWYRM_USER}%2Fshelf%2Fread.json?page=1`;
  fetch(readUrl).then(res => res.json()).then(out => {
    out.orderedItems.forEach(renderReadBook);
  }).catch(err => {
    (0,_inc_shared__WEBPACK_IMPORTED_MODULE_0__.renderError)('div.read--list');
    return;
  });
  const renderReadBook = book => {
    let currentBook = (0,_inc_shared__WEBPACK_IMPORTED_MODULE_0__.createBookDiv)(book.isbn13);
    currentBook.classList.add(`book-${book.isbn13}`);
    const hasCover = ('cover' in book);
    let coverAlt = '';
    let coverName = '';
    if (hasCover === true) {
      coverAlt = `${book.cover.name}`;
      coverName = `${book.cover.name}`;
    } else {
      coverAlt = '';
      coverName = '';
    }
    let cover = `<img src=${(0,_inc_shared__WEBPACK_IMPORTED_MODULE_0__.getBookCover)(book.isbn13)} width="150" height="225" alt="${coverAlt}" loading="lazy" class="bookwyrm-book-cover">`;
    let author = getBookAuthorRead(coverName);
    let title = `${book.title}`;
    currentBook.innerHTML = `${cover}<p class="bookwyrm-book-title"><cite>${title}</cite></b><br /> ${author}</p>`;
    let readingHolder = document.querySelector('.read--list');
    readingHolder.appendChild(currentBook);
  };
  const getBookAuthorRead = bookName => {
    if (bookName === '') return ``;else {
      let authorName = bookName.match(/^[^:]+/)[0];
      let authorByline = `by ${authorName}`;
      return authorByline;
    }
  };
})();
/******/ })()
;
//# sourceMappingURL=view.js.map