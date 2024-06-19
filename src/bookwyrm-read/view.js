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

	const renderError = () => {
		const newError = document.createElement('p');
  		newError.textContent = '⚠️ Sorry, there has been an error fetching the feed.';
		newError.style.fontWeight = 'bold';
  		const readListDiv = document.querySelector( 'div.read--list' );
		document.querySelector( 'div.read--list' ).style.display = 'block';
  		readListDiv.appendChild( newError );
	}

	const bookwyrmCcontainerElement = document.querySelector( '.wp-block-bookwyrm-blocks-bookwyrm-read-block')
	let BOOKWYRM_USER = bookwyrmCcontainerElement.getAttribute('data-user');
	let BOOKWYRM_INSTANCE = trimProtocolAndTrailingSlash(bookwyrmCcontainerElement.getAttribute( 'data-instance' ));

	if ( ( !BOOKWYRM_USER || BOOKWYRM_USER == '' || BOOKWYRM_USER == null ) || ( !BOOKWYRM_INSTANCE || BOOKWYRM_INSTANCE == '' || BOOKWYRM_INSTANCE == null ) ) {
		renderError();
		return;	
	}

	let readUrl = `https://corsproxy.io/?https%3A%2F%2F${BOOKWYRM_INSTANCE}%2Fuser%2F${BOOKWYRM_USER}%2Fshelf%2Fread.json?page=1`;

	function trimProtocolAndTrailingSlash ( url ) {
		return url.replace( /(http(s)?:\/\/)|(\/+$)/g, '').replace(/\/+/g, '/');
	}

	fetch(readUrl)
		.then( res => res.json() )
		.then( out => {
			out.orderedItems.forEach( renderReadBook )
		})
		.catch( err => { 
			renderError();
			return;
		});

	const renderReadBook = ( book ) => {
		let currentBook = createBookDiv( book.isbn13 );
		currentBook.classList.add( `book-${book.isbn13}` );
		const hasCover = 'cover' in book;
		let coverAlt = '';
		let coverName = '';
		if (hasCover === true) {
			coverAlt = `${book.cover.name}`
			coverName = `${book.cover.name}`
		} else {
			coverAlt = '';
			coverName = '';
		}
		let cover = `<img src=${getBookCover( book.isbn13 )} width="150" height="225" alt="${ coverAlt }" loading="lazy" class="bookwyrm-book-cover">`
		let author = getBookAuthorRead( coverName );
		let title = `${book.title}`
		currentBook.innerHTML = `${cover}<p class="bookwyrm-book-title"><cite>${title}</cite></b><br /> ${author}</p>`
		let readingHolder = document.querySelector( '.read--list' );
		readingHolder.appendChild( currentBook );
	}

	const createBookDiv = () => {
		let newElement = document.createElement('div');
		return newElement;
	}

	const getBookCover = ( isbn ) => {
		/* get covers from OpenLibrary as they are more standardized 
			Format: https://covers.openlibrary.org/b/isbn/9780385533225-S.jpg
		*/
		return `https://covers.openlibrary.org/b/isbn/${isbn}-L.jpg`;
	}

	const getBookAuthorRead = ( bookName ) => {
		if (bookName === '')
			return ``
		else {
			let authorName = charactersBeforeColon = bookName.match(/^[^:]+/)[0];
			let authorByline = `by ${authorName}`;
			return authorByline;
		}
	}

	const renderReadingAuthor = ( author ) => {
		let authorByLine = `by ${author}`
		return authorByLine;
	}

})();