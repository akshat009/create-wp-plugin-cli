import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';
import metadata from './block.json';

/**
 * Dynamic block — save() stays empty, actual output comes from render.php.
 */
registerBlockType( metadata.name, {
	edit: () => {
		const blockProps = useBlockProps();
		return (
			<div { ...blockProps }>
				{ __( 'Cart Summary (live on the frontend)', '{{SLUG}}' ) }
			</div>
		);
	},
	save: () => null,
} );
