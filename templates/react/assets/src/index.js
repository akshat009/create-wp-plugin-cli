import { createRoot, useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

/**
 * Minimal interactive demo — proves the compiled bundle actually mounts and
 * runs client-side inside wp-admin. Replace with your real admin app.
 */
function App() {
	const [ count, setCount ] = useState( 0 );

	return (
		<button type="button" onClick={ () => setCount( ( n ) => n + 1 ) }>
			{ __( 'Clicked', '{{SLUG}}' ) } { count } { __( 'times', '{{SLUG}}' ) }
		</button>
	);
}

const rootEl = document.getElementById( '{{PREFIX}}-app-root' );

if ( rootEl ) {
	createRoot( rootEl ).render( <App /> );
}
