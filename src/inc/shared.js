export function createBookDiv () {
	let newElement = document.createElement('div');
	return newElement;
}
export function getBookCover ( isbn ) {
	/* get covers from OpenLibrary as they are more standardized 
		Format: https://covers.openlibrary.org/b/isbn/9780385533225-S.jpg
	*/
	return `https://covers.openlibrary.org/b/isbn/${isbn}-L.jpg`;
}

export function renderError ( container ) {
	const newError = document.createElement('p');
	newError.textContent = '⚠️ Sorry, there has been an error fetching the feed.';
	newError.style.fontWeight = 'bold';
	const readListDiv = document.querySelector( container );
	document.querySelector( container ).style.display = 'block';
	readListDiv.appendChild( newError );
}

export function trimProtocolAndTrailingSlash ( url ) {
	return url.replace( /(http(s)?:\/\/)|(\/+$)/g, '').replace(/\/+/g, '/');
}

export function configurePluginMessage ( configured ) {
	if ( configured === 'no') 
		return `⚠️ Configure your BookWyrm username and instance in the block settings.`
	else 
		return `This message only appears in the editor.`
}