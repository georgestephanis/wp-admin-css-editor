
import CSSEditor from './css-editor';

export default function CSSEditorUI( { styles, editable = true, setStyles } ) {
	return <div className="css-editor-ui">
		<CSSEditor styles={ styles } editable={ editable } setStyles={ setStyles } />
	</div>;
}
