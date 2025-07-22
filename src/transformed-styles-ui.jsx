// https://github.com/WordPress/gutenberg/tree/trunk/packages/block-editor/src/utils/transform-styles
import { transformStyles } from '@wordpress/block-editor';
import { useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

import CSSEditor from './css-editor';

export default function TransformedStylesUI( { source, wrapper } ) {
	const [ raw, setRaw ] = useState( source );

	return <dl className="transformed-styles">
		<dt>
			<h4>{ __( 'CSS Editor:' ) }</h4>
			<CSSEditor styles={ raw } setStyles={ setRaw } />
		</dt>
		<dd>
			<h4>{ __( 'Rendered Preview:' ) }</h4>
			<CSSEditor styles={ transformStyles( [ { css: raw } ], wrapper ).join() } editable={ false } />
		</dd>
	</dl>;
}
