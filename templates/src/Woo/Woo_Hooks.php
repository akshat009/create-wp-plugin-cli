<?php
/**
 * WooCommerce Integration Orchestrator.
 *
 * Wires up a payment gateway (classic checkout + WooCommerce Blocks), a
 * shipping method, a custom order email, and a custom product type. HPOS
 * (custom order tables) compatibility is declared separately in the main
 * plugin file. Every piece here is a real, working skeleton — replace the
 * TODOs with your actual business logic.
 *
 * @package {{NS}}\Woo
 */

namespace {{NS}}\Woo;

use {{NS}}\Contracts\Registrable;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Woo_Hooks.
 */
class Woo_Hooks implements Registrable {

	/**
	 * Register WooCommerce hooks.
	 *
	 * @return void
	 */
	public function register(): void {
		if ( ! class_exists( 'WooCommerce' ) ) {
			return;
		}

		add_action( 'woocommerce_single_product_summary', array( $this, 'custom_product_summary_note' ), 25 );

		add_filter( 'woocommerce_payment_gateways', array( $this, 'register_gateway' ) );
		add_filter( 'woocommerce_shipping_methods', array( $this, 'register_shipping_method' ) );
		add_filter( 'woocommerce_email_classes', array( $this, 'register_email' ) );
		add_filter( 'woocommerce_product_class', array( Products\Custom_Product::class, 'filter_product_class' ), 10, 2 );
		add_filter( 'product_type_selector', array( Products\Custom_Product::class, 'filter_product_type_selector' ) );

		add_action( 'init', array( Blocks\Cart_Summary_Block::class, 'register' ) );

		// WooCommerce Blocks (block-based checkout) has its own separate payment
		// method registry and Cart/Checkout content-integration registry — neither
		// the gateway registered via woocommerce_payment_gateways above, nor
		// anything hooked into the classic cart/checkout templates, is visible in
		// the block-based Cart/Checkout without registering here too.
		add_action(
			'woocommerce_blocks_loaded',
			function () {
				add_action(
					'woocommerce_blocks_payment_method_type_registration',
					function ( $registry ) {
						$registry->register( new Gateways\Blocks_Payment_Method_Type() );
					}
				);

				$register_integration = function ( $registry ) {
					$registry->register( new Blocks\Integration() );
				};
				add_action( 'woocommerce_blocks_cart_block_registration', $register_integration );
				add_action( 'woocommerce_blocks_checkout_block_registration', $register_integration );
			}
		);
	}

	/**
	 * Render a custom note on the single product page.
	 *
	 * @return void
	 */
	public function custom_product_summary_note() {
		echo '<div class="' . esc_attr( '{{SLUG}}-woo-note' ) . '">' . esc_html__( 'Special Product Note', '{{SLUG}}' ) . '</div>';
	}

	/**
	 * Register the custom payment gateway with WooCommerce (classic checkout).
	 *
	 * @param array $gateways Existing gateways.
	 * @return array
	 */
	public function register_gateway( $gateways ) {
		$gateways[] = Gateways\Gateway::class;
		return $gateways;
	}

	/**
	 * Register the custom shipping method with WooCommerce.
	 *
	 * @param array $methods Existing shipping methods.
	 * @return array
	 */
	public function register_shipping_method( $methods ) {
		$methods['{{PREFIX}}_shipping'] = Shipping\Shipping_Method::class;
		return $methods;
	}

	/**
	 * Register the custom order email with WooCommerce.
	 *
	 * @param array $emails Existing email classes (already-constructed instances).
	 * @return array
	 */
	public function register_email( $emails ) {
		$emails['{{PREFIX}}_custom_email'] = new Emails\Custom_Email();
		return $emails;
	}
}
