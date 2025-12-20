<?php
/**
 * Plugin Name:       Blocks for BookWyrm
 * Description:       Add blocks for pulling currently reading and recently read books from BookWyrm instances.
 * Requires at least: 6.8
 * Tested up to:      6.9
 * Requires PHP:      8.2
 * Version:           1.0.5
 * Author URI:        https://www.bobmatyas.com
 * Author:            Bob Matyas
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       blocks-for-bookwyrm
 *
 * @package Blocks_For_Bookwyrm
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/reference/functions/register_block_type/
 */
function blocks_for_bookwyrm_register_blocks() {
	register_block_type( __DIR__ . '/build/bookwyrm-read' );
	register_block_type( __DIR__ . '/build/bookwyrm-reading' );
}
add_action( 'init', 'blocks_for_bookwyrm_register_blocks' );

/**
 * Make placeholder image URLs available to the block editor.
 *
 * @param array $settings The editor settings.
 * @return array Modified editor settings.
 */
function blocks_for_bookwyrm_add_editor_settings( $settings ) {
	$settings['blocksForBookwyrm'] = array(
		'placeholderSvg' => plugins_url( 'assets/images/default-book-cover.svg', __FILE__ ),
		'placeholderPng' => plugins_url( 'assets/images/default-book-cover.png', __FILE__ ),
	);
	return $settings;
}
add_filter( 'block_editor_settings_all', 'blocks_for_bookwyrm_add_editor_settings' );

/**
 * Enqueue placeholder image URLs as a global variable for the block editor.
 */
function blocks_for_bookwyrm_enqueue_editor_assets() {
	wp_add_inline_script(
		'wp-blocks',
		'window.blocksForBookwyrm = { placeholderSvg: ' . wp_json_encode( plugins_url( 'assets/images/default-book-cover.svg', __FILE__ ) ) . ', placeholderPng: ' . wp_json_encode( plugins_url( 'assets/images/default-book-cover.png', __FILE__ ) ) . ' };',
		'before'
	);
}
add_action( 'enqueue_block_editor_assets', 'blocks_for_bookwyrm_enqueue_editor_assets' );
