<?php
/**
 * Render callback for the bookwyrm-reading block.
 *
 * @see https://github.com/WordPress/gutenberg/blob/trunk/docs/reference-guides/block-api/block-metadata.md#render
 *
 * @package Blocks_For_Bookwyrm
 */

// Ensure attributes is set.
if ( ! isset( $attributes ) || ! is_array( $attributes ) ) {
	$attributes = array();
}

// Get attributes.
$user     = isset( $attributes['bookwyrmUserName'] ) ? $attributes['bookwyrmUserName'] : '';
$instance = isset( $attributes['bookwyrmInstance'] ) ? $attributes['bookwyrmInstance'] : '';

// Validate inputs.
if ( empty( $user ) || empty( $instance ) ) {
	echo '<div ' . wp_kses_data( get_block_wrapper_attributes() ) . '>';
	echo '<p style="font-weight: bold;">⚠️ Sorry, there has been an error fetching the feed.</p>';
	echo '</div>';
	return;
}

// Clean instance URL - remove protocol and trailing slashes.
$instance = preg_replace( '/(http(s)?:\/\/)|(\/+$)/', '', $instance );
$instance = preg_replace( '/\/+/', '/', $instance );
$instance = trim( $instance, '/' );

// Build API URL - construct manually to avoid double encoding.
$api_url = 'https://' . $instance . '/user/' . rawurlencode( $user ) . '/shelf/reading.json?page=1';

// Make API request.
$response = wp_remote_get(
	$api_url,
	array(
		'timeout'   => 15,
		'sslverify' => true,
		'headers'   => array(
			'Accept' => 'application/json',
		),
	)
);

// Check for errors.
if ( is_wp_error( $response ) ) {
	echo '<div ' . wp_kses_data( get_block_wrapper_attributes() ) . '>';
	echo '<p style="font-weight: bold;">⚠️ Sorry, there has been an error fetching the feed.</p>';
	echo '</div>';
	return;
}

// Check HTTP response code.
$response_code = wp_remote_retrieve_response_code( $response );
if ( 200 !== $response_code ) {
	echo '<div ' . wp_kses_data( get_block_wrapper_attributes() ) . '>';
	echo '<p style="font-weight: bold;">⚠️ Sorry, there has been an error fetching the feed.</p>';
	echo '</div>';
	return;
}

$body = wp_remote_retrieve_body( $response );
$data = json_decode( $body, true );

// Check if we have valid data.
if ( ! isset( $data['orderedItems'] ) || ! is_array( $data['orderedItems'] ) ) {
	echo '<div ' . wp_kses_data( get_block_wrapper_attributes() ) . '>';
	echo '<p style="font-weight: bold;">⚠️ Sorry, there has been an error fetching the feed.</p>';
	echo '</div>';
	return;
}

// Helper function to get book cover.
if ( ! function_exists( 'get_book_cover' ) ) {
	/**
	 * Get book cover URL from ISBN.
	 *
	 * @param string $isbn The ISBN of the book.
	 * @return string The cover URL.
	 */
	function get_book_cover( $isbn ) {
		return 'https://covers.openlibrary.org/b/isbn/' . esc_attr( $isbn ) . '-L.jpg';
	}
}

// Helper function to fetch author name.
if ( ! function_exists( 'get_book_author_reading' ) ) {
	/**
	 * Fetch author name from Bookwyrm API.
	 *
	 * @param string $author_url The author URL from Bookwyrm.
	 * @return string The author name with "by " prefix, or empty string.
	 */
	function get_book_author_reading( $author_url ) {
		if ( empty( $author_url ) ) {
			return '';
		}

		// Ensure URL ends with .json.
		$author_url = rtrim( $author_url, '/' );
		// Use substr comparison for compatibility.
		if ( substr( $author_url, -5 ) !== '.json' ) {
			$author_url .= '.json';
		}

		$response = wp_remote_get(
			$author_url,
			array(
				'timeout'   => 10,
				'sslverify' => true,
				'headers'   => array(
					'Accept' => 'application/json',
				),
			)
		);

		if ( is_wp_error( $response ) ) {
			return '';
		}

		$body        = wp_remote_retrieve_body( $response );
		$author_data = json_decode( $body, true );

		if ( isset( $author_data['name'] ) ) {
			return 'by ' . esc_html( $author_data['name'] );
		}

		return '';
	}
}

// Output the block.
?>
<div <?php echo wp_kses_data( get_block_wrapper_attributes() ); ?> data-user="<?php echo esc_attr( $user ); ?>" data-instance="<?php echo esc_attr( $instance ); ?>">
	<div class="reading--list">
		<?php foreach ( $data['orderedItems'] as $book ) : ?>
			<?php
			$isbn = isset( $book['isbn13'] ) ? $book['isbn13'] : '';
			if ( empty( $isbn ) ) {
				continue;
			}

			$book_title = isset( $book['title'] ) ? esc_html( $book['title'] ) : '';
			$author_url = isset( $book['authors'] ) && is_array( $book['authors'] ) && ! empty( $book['authors'] ) ? $book['authors'][0] : '';
			$author     = get_book_author_reading( $author_url );
			$cover_url  = get_book_cover( $isbn );
			?>
			<div class="book book-<?php echo esc_attr( $isbn ); ?>">
				<img src="<?php echo esc_url( $cover_url ); ?>" width="150" height="225" alt="cover <?php echo esc_attr( $book_title ); ?>" loading="lazy" style="border: 1px solid #ccc; background-color: #eee;">
				<p>
					<b><cite><?php echo esc_html( $book_title ); ?></cite></b>
					<?php if ( $author ) : ?>
						<br><?php echo wp_kses_post( $author ); ?>
					<?php endif; ?>
				</p>
			</div>
		<?php endforeach; ?>
	</div>
</div>
