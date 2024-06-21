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
console.log("Hello World! (from create-block-bookwyrm-reading block)");
/* eslint-enable no-console */

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

	let readingUrl = `https://corsproxy.io/?https%3A%2F%2F${BOOKWYRM_INSTANCE}%2Fuser%2F${BOOKWYRM_USER}%2Fshelf%2Freading.json%3Fpage%3D1`;

	function trimProtocolAndTrailingSlash ( url ) {
		return url.replace( /(http(s)?:\/\/)|(\/+$)/g, '').replace(/\/+/g, '/');
	}

	fetch(readingUrl)
		.then(res => res.json())
		.then(out => {
			out.orderedItems.forEach(renderReadingBook)
	})
	.catch(err => { throw err });

	const renderReadingBook = async (book) => {
		let currentBook = createBookDiv(book.isbn13);
		let author = await getBookAuthorReading(book.authors[0]);
		currentBook.classList.add(`book-${book.isbn13}`);
		currentBook.innerHTML = `<img src=${getBookCover(book.isbn13)} width="150" height="225" alt="cover ${book.title}" loading="lazy" style="border: 1px solid #ccc; background-color: #eee;"  ><p><b><cite>${book.title}</cite></b><br />${author}</p>`
		let readingHolder = document.querySelector('.reading--list');
		readingHolder.appendChild(currentBook);
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

	const getBookAuthorReading = async (bookAuthorNumber ) => {
		let authorUrl = `https://corsproxy.io/?${bookAuthorNumber}.json`
		return fetch( authorUrl )
			.then(res => res.json())
			.then(out => {
				return renderReadingAuthor(out.name);
			})
		.catch(err => { throw err });
	}
	
	
	const renderReadingAuthor = ( author ) => {
		let authorByLine = `by ${author}`
		return authorByLine;
	}

})();