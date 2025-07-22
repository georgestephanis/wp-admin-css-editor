import { useState } from '@wordpress/element';

import CodeMirror from '@uiw/react-codemirror';
import { EditorView } from '@codemirror/view';
import { css } from '@codemirror/lang-css';

const styleTheme = EditorView.baseTheme({
	"&.cm-editor.cm-focused": {
		outline: "0 solid orange",
	}
});

export default function CSSEditor( { styles, editable = true, setStyles } ) {
	// If a setStyles is passed in, use that.  Otherwise fall back to an internal State.
	const [ value, setValue ] = ( setStyles || ! editable ) ? [ styles, setStyles ] : useState( styles );

	return <>
		<CodeMirror value={ value } height="200px" extensions={[ styleTheme, css() ]} onChange={ setValue } editable={ editable } />
	</>;
}
