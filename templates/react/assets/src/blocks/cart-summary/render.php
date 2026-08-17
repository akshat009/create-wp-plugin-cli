<?php
/**
 * Server-side render for the {{PREFIX}}/cart-summary block.
 *
 * @package {{NS}}\Woo\Blocks
 *
 * @var array    $attributes Block attributes (unused — this block has none).
 * @var string   $content    Default block content (unused, dynamic block).
 * @var WP_Block $block      Block instance (unused).
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
	return;
}

$count    = WC()->cart->get_cart_contents_count();
$subtotal = WC()->cart->get_cart_subtotal();

printf(
	'<div %1$s><span class="%2$s-cart-summary__count">%3$s</span> <span class="%2$s-cart-summary__subtotal">%4$s</span></div>',
	get_block_wrapper_attributes(), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core helper escapes attributes internally.
	esc_attr( '{{SLUG}}' ),
	esc_html(
		sprintf(
			/* translators: %d: number of items in cart. */
			_n( '%d item', '%d items', $count, '{{SLUG}}' ),
			$count
		)
	),
	wp_kses_post( $subtotal )
);
