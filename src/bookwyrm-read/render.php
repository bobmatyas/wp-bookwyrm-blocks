<?php
/**
 * Render callback for the bookwyrm-read block.
 *
 * @see https://github.com/WordPress/gutenberg/blob/trunk/docs/reference-guides/block-api/block-metadata.md#render
 *
 * @package Bookwyrm_Blocks
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
$api_url = 'https://' . $instance . '/user/' . rawurlencode( $user ) . '/shelf/read.json?page=1';

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

// Helper function to get author from cover name.
if ( ! function_exists( 'get_book_author_read' ) ) {
	/**
	 * Get author name from cover name.
	 *
	 * @param string $cover_name The cover name string.
	 * @return string The author name with "by " prefix.
	 */
	function get_book_author_read( $cover_name ) {
		if ( empty( $cover_name ) ) {
			return '';
		}
		$author_name = preg_match( '/^[^:]+/', $cover_name, $matches ) ? $matches[0] : '';
		return $author_name ? 'by ' . esc_html( $author_name ) : '';
	}
}

// Output the block.
?>
<div <?php echo wp_kses_data( get_block_wrapper_attributes() ); ?> data-user="<?php echo esc_attr( $user ); ?>" data-instance="<?php echo esc_attr( $instance ); ?>">
	<div class="read--list">
		<?php foreach ( $data['orderedItems'] as $book ) : ?>
			<?php
			$isbn = isset( $book['isbn13'] ) ? $book['isbn13'] : '';
			if ( empty( $isbn ) ) {
				continue;
			}

			$has_cover  = isset( $book['cover'] ) && isset( $book['cover']['name'] );
			$cover_alt  = $has_cover ? esc_attr( $book['cover']['name'] ) : '';
			$cover_name = $has_cover ? $book['cover']['name'] : '';
			$book_title = isset( $book['title'] ) ? esc_html( $book['title'] ) : '';
			$author     = get_book_author_read( $cover_name );
			$cover_url  = get_book_cover( $isbn );
			?>
			<div class="book book-<?php echo esc_attr( $isbn ); ?>">
				<img src="<?php echo esc_url( $cover_url ); ?>" width="150" height="225" alt="<?php echo esc_attr( $cover_alt ); ?>" loading="lazy" class="bookwyrm-book-cover">
				<p class="bookwyrm-book-title">
					<cite><?php echo esc_html( $book_title ); ?></cite>
					<?php if ( $author ) : ?>
						<br><?php echo esc_html( $author ); ?>
					<?php endif; ?>
				</p>
			</div>
		<?php endforeach; ?>
	</div>
</div>
