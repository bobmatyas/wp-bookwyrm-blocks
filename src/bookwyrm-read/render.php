<?php
/**
 * Render callback for the bookwyrm-read block.
 *
 * @see https://github.com/WordPress/gutenberg/blob/trunk/docs/reference-guides/block-api/block-metadata.md#render
 *
 * @package Bookwyrm_Blocks
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
$blocks_for_bookwyrm_api_url = 'https://' . $blocks_for_bookwyrm_instance . '/user/' . rawurlencode( $blocks_for_bookwyrm_user ) . '/shelf/read.json?page=1';

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

// Helper function to get default cover URL.
if ( ! function_exists( 'get_default_book_cover' ) ) {
	/**
	 * Get default book cover placeholder URL.
	 *
	 * @param string $type The placeholder type: 'svg' or 'png'. Default 'svg'.
	 * @return string The default cover URL.
	 */
	function get_default_book_cover( $type = 'svg' ) {
		$plugin_dir = dirname( dirname( __DIR__ ) );
		$filename   = 'svg' === $type ? 'default-book-cover.svg' : 'default-book-cover.png';
		return plugins_url( 'assets/images/' . $filename, $plugin_dir . '/blocks-for-bookwyrm.php' );
	}
}

// Helper function to get book cover.
if ( ! function_exists( 'get_book_cover' ) ) {
	/**
	 * Get book cover URL from ISBN.
	 *
	 * @param string $isbn The ISBN of the book.
	 * @param string $default Optional default cover URL. If not provided, uses plugin default.
	 * @return string The cover URL.
	 */
	function get_book_cover( $isbn, $default = null ) {
		if ( empty( $isbn ) ) {
			return $default ? $default : get_default_book_cover();
		}
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
<div <?php echo wp_kses_data( get_block_wrapper_attributes() ); ?> data-user="<?php echo esc_attr( $blocks_for_bookwyrm_user ); ?>" data-instance="<?php echo esc_attr( $blocks_for_bookwyrm_instance ); ?>">
	<div class="read--list">
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

			$blocks_for_bookwyrm_has_cover  = isset( $blocks_for_bookwyrm_book['cover'] ) && isset( $blocks_for_bookwyrm_book['cover']['name'] );
			$blocks_for_bookwyrm_cover_alt  = $blocks_for_bookwyrm_has_cover ? esc_attr( $blocks_for_bookwyrm_book['cover']['name'] ) : '';
			$blocks_for_bookwyrm_cover_name = $blocks_for_bookwyrm_has_cover ? $blocks_for_bookwyrm_book['cover']['name'] : '';
			$blocks_for_bookwyrm_book_title = isset( $blocks_for_bookwyrm_book['title'] ) ? esc_html( $blocks_for_bookwyrm_book['title'] ) : '';
			$blocks_for_bookwyrm_author     = get_book_author_read( $blocks_for_bookwyrm_cover_name );
			$blocks_for_bookwyrm_placeholder_type = isset( $attributes['placeholderType'] ) ? $attributes['placeholderType'] : 'svg';
			$blocks_for_bookwyrm_default_cover = get_default_book_cover( $blocks_for_bookwyrm_placeholder_type );
			$blocks_for_bookwyrm_cover_url  = get_book_cover( $blocks_for_bookwyrm_isbn, $blocks_for_bookwyrm_default_cover );
			$blocks_for_bookwyrm_book_id    = ! empty( $blocks_for_bookwyrm_isbn ) ? esc_attr( $blocks_for_bookwyrm_isbn ) : 'no-isbn-' . uniqid();
			?>
			<div class="book book-<?php echo esc_attr( $blocks_for_bookwyrm_book_id ); ?>">
				<img src="<?php echo esc_url( $blocks_for_bookwyrm_cover_url ); ?>" width="150" height="225" alt="<?php echo esc_attr( $blocks_for_bookwyrm_cover_alt ? $blocks_for_bookwyrm_cover_alt : $blocks_for_bookwyrm_book_title ); ?>" loading="lazy" class="bookwyrm-book-cover" data-default-cover="<?php echo esc_attr( $blocks_for_bookwyrm_default_cover ); ?>" onload="if(this.naturalWidth === 1 && this.naturalHeight === 1) { this.src = this.dataset.defaultCover; }" onerror="this.onerror=null; this.src='<?php echo esc_js( $blocks_for_bookwyrm_default_cover ); ?>';">
				<p class="bookwyrm-book-title">
					<cite><?php echo esc_html( $blocks_for_bookwyrm_book_title ); ?></cite>
					<?php if ( $blocks_for_bookwyrm_author ) : ?>
						<br><?php echo esc_html( $blocks_for_bookwyrm_author ); ?>
					<?php endif; ?>
				</p>
			</div>
		<?php endforeach; ?>
	</div>
</div>
