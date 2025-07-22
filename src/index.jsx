import domReady from '@wordpress/dom-ready';
import { createRoot } from '@wordpress/element';

import apiFetch from '@wordpress/api-fetch';

import CSSEditorUI from './css-editor-ui';
import TransformedStylesUI from './transformed-styles-ui';

import './index.scss';

domReady( () => {
	const root = createRoot( document.getElementById( 'admin-css-editor-root' ) );
	root.render(
		<>
			<CSSEditorUI styles=".foo {color: #000;}" />
			<TransformedStylesUI source="& .boo { color: blue; }" wrapper="h3" />
		</>
	);
} );
