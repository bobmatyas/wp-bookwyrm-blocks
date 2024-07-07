<?php
/**
 * @see https://github.com/WordPress/gutenberg/blob/trunk/docs/reference-guides/block-api/block-metadata.md#render
 */
?>

<div <?php echo get_block_wrapper_attributes(); ?> data-user=<? echo esc_attr( $attributes['bookwyrmUserName'] ) ?> data-instance=<? echo esc_attr( $attributes['bookwyrmInstance'] ) ?>>
	<div class="read--list"></div>
</div>
