<?php
/**
 * Render callback for the bookwyrm-reading block.
 *
 * @see https://github.com/WordPress/gutenberg/blob/trunk/docs/reference-guides/block-api/block-metadata.md#render
 *
 * @package Blocks_For_Bookwyrm
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Ensure attributes is set.
// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- $attributes is provided by WordPress Gutenberg.
if ( ! isset( $attributes ) || ! is_array( $attributes ) ) {
	$attributes = array();
}

// Get attributes.
$blocks_for_bookwyrm_user     = isset( $attributes['bookwyrmUserName'] ) ? $attributes['bookwyrmUserName'] : '';
$blocks_for_bookwyrm_instance = isset( $attributes['bookwyrmInstance'] ) ? $attributes['bookwyrmInstance'] : '';

// Validate inputs.
if ( empty( $blocks_for_bookwyrm_user ) || empty( $blocks_for_bookwyrm_instance ) ) {
	echo '<div ' . wp_kses_data( get_block_wrapper_attributes() ) . '>';
	echo '<p style="font-weight: bold;">⚠️ Sorry, there has been an error fetching the feed.</p>';
	echo '</div>';
	return;
}

// Clean instance URL - remove protocol and trailing slashes.
$blocks_for_bookwyrm_instance = preg_replace( '/(http(s)?:\/\/)|(\/+$)/', '', $blocks_for_bookwyrm_instance );
$blocks_for_bookwyrm_instance = preg_replace( '/\/+/', '/', $blocks_for_bookwyrm_instance );
$blocks_for_bookwyrm_instance = trim( $blocks_for_bookwyrm_instance, '/' );

// Build API URL - construct manually to avoid double encoding.
$blocks_for_bookwyrm_api_url = 'https://' . $blocks_for_bookwyrm_instance . '/user/' . rawurlencode( $blocks_for_bookwyrm_user ) . '/shelf/reading.json?page=1';

// Make API request.
$blocks_for_bookwyrm_response = wp_remote_get(
	$blocks_for_bookwyrm_api_url,
	array(
		'timeout'   => 15,
		'sslverify' => true,
		'headers'   => array(
			'Accept' => 'application/json',
		),
	)
);

// Check for errors.
if ( is_wp_error( $blocks_for_bookwyrm_response ) ) {
	echo '<div ' . wp_kses_data( get_block_wrapper_attributes() ) . '>';
	echo '<p style="font-weight: bold;">⚠️ Sorry, there has been an error fetching the feed.</p>';
	echo '</div>';
	return;
}

// Check HTTP response code.
$blocks_for_bookwyrm_response_code = wp_remote_retrieve_response_code( $blocks_for_bookwyrm_response );
if ( 200 !== $blocks_for_bookwyrm_response_code ) {
	echo '<div ' . wp_kses_data( get_block_wrapper_attributes() ) . '>';
	echo '<p style="font-weight: bold;">⚠️ Sorry, there has been an error fetching the feed.</p>';
	echo '</div>';
	return;
}

$blocks_for_bookwyrm_body = wp_remote_retrieve_body( $blocks_for_bookwyrm_response );
$blocks_for_bookwyrm_data = json_decode( $blocks_for_bookwyrm_body, true );

// Check if we have valid data.
if ( ! isset( $blocks_for_bookwyrm_data['orderedItems'] ) || ! is_array( $blocks_for_bookwyrm_data['orderedItems'] ) ) {
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
<div <?php echo wp_kses_data( get_block_wrapper_attributes() ); ?> data-user="<?php echo esc_attr( $blocks_for_bookwyrm_user ); ?>" data-instance="<?php echo esc_attr( $blocks_for_bookwyrm_instance ); ?>">
	<div class="reading--list">
		<?php foreach ( $blocks_for_bookwyrm_data['orderedItems'] as $blocks_for_bookwyrm_book ) : ?>
			<?php
			// Try multiple ISBN formats - isbn13, isbn10, or isbn.
			$blocks_for_bookwyrm_isbn = '';
			if ( isset( $blocks_for_bookwyrm_book['isbn13'] ) && ! empty( $blocks_for_bookwyrm_book['isbn13'] ) ) {
				$blocks_for_bookwyrm_isbn = $blocks_for_bookwyrm_book['isbn13'];
			} elseif ( isset( $blocks_for_bookwyrm_book['isbn10'] ) && ! empty( $blocks_for_bookwyrm_book['isbn10'] ) ) {
				$blocks_for_bookwyrm_isbn = $blocks_for_bookwyrm_book['isbn10'];
			} elseif ( isset( $blocks_for_bookwyrm_book['isbn'] ) && ! empty( $blocks_for_bookwyrm_book['isbn'] ) ) {
				$blocks_for_bookwyrm_isbn = $blocks_for_bookwyrm_book['isbn'];
			}

			if ( empty( $blocks_for_bookwyrm_isbn ) ) {
				continue;
			}

			$blocks_for_bookwyrm_book_title = isset( $blocks_for_bookwyrm_book['title'] ) ? esc_html( $blocks_for_bookwyrm_book['title'] ) : '';
			$blocks_for_bookwyrm_author_url = isset( $blocks_for_bookwyrm_book['authors'] ) && is_array( $blocks_for_bookwyrm_book['authors'] ) && ! empty( $blocks_for_bookwyrm_book['authors'] ) ? $blocks_for_bookwyrm_book['authors'][0] : '';
			$blocks_for_bookwyrm_author     = get_book_author_reading( $blocks_for_bookwyrm_author_url );
			$blocks_for_bookwyrm_cover_url  = get_book_cover( $blocks_for_bookwyrm_isbn );
			?>
			<div class="book book-<?php echo esc_attr( $blocks_for_bookwyrm_isbn ); ?>">
				<img src="<?php echo esc_url( $blocks_for_bookwyrm_cover_url ); ?>" width="150" height="225" alt="cover <?php echo esc_attr( $blocks_for_bookwyrm_book_title ); ?>" loading="lazy" style="border: 1px solid #ccc; background-color: #eee;">
				<p>
					<b><cite><?php echo esc_html( $blocks_for_bookwyrm_book_title ); ?></cite></b>
					<?php if ( $blocks_for_bookwyrm_author ) : ?>
						<br><?php echo wp_kses_post( $blocks_for_bookwyrm_author ); ?>
					<?php endif; ?>
				</p>
			</div>
		<?php endforeach; ?>
	</div>
</div>
