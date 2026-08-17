<?php
/**
 * React Admin App Build Pipeline Manager (@wordpress/scripts).
 *
 * Scoped to wp-admin: WordPress core does not ship a React runtime to
 * frontend visitors, so this pipeline is for the plugin's own admin
 * screens only. For interactive frontend markup, use the "Frontend
 * Interactivity" module (WordPress's native Interactivity API) instead
 * of mounting a React app publicly.
 *
 * @package {{NS}}\Admin
 */

namespace {{NS}}\Admin;

use {{NS}}\Contracts\Registrable;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Assets.
 */
class Assets implements Registrable {

	/**
	 * Register asset hooks.
	 *
	 * @return void
	 */
	public function register(): void {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
	}

	/**
	 * Enqueue compiled React admin app assets.
	 *
	 * @param string $hook_suffix Current admin page hook suffix.
	 * @return void
	 */
	public function enqueue_assets( $hook_suffix = '' ) {
{{REACT_ADMIN_HOOK_GUARD}}		$asset_file = {{PREFIX_UPPER}}_PATH . 'assets/build/index.asset.php';

		if ( ! file_exists( $asset_file ) ) {
			return;
		}

		$asset = require $asset_file;

		wp_enqueue_script(
			'{{PREFIX}}-admin-app',
			{{PREFIX_UPPER}}_URL . 'assets/build/index.js',
			$asset['dependencies'],
			$asset['version'],
			true
		);

		if ( file_exists( {{PREFIX_UPPER}}_PATH . 'assets/build/index.css' ) ) {
			wp_enqueue_style(
				'{{PREFIX}}-admin-app',
				{{PREFIX_UPPER}}_URL . 'assets/build/index.css',
				array(),
				$asset['version']
			);
		}
	}
}
