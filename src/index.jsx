/* globals: GSCustomCss */

import domReady from '@wordpress/dom-ready';
import { createRoot } from '@wordpress/element';

import GSGlobalStylesUI from './gs-global-styles-ui';

import './index.scss';

domReady( () => {
	const root = createRoot( document.getElementById( 'admin-css-editor-root' ) );
	root.render(
		<>
			<GSGlobalStylesUI id={ GSCustomCss.GlobalStylesId } />
		</>
	);
} );
