import { useSelect } from '@wordpress/data';
import { getEntityRecord } from '@wordpress/core-data';
import { Spinner } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

import CSSEditorUI from './css-editor-ui';
import TransformedStylesUI from './transformed-styles-ui';

export default function GSGlobalStylesUI( { id } ) {
	const globalStyles = useSelect( ( select ) => {
		return select( 'core' ).getEntityRecord( 'root', 'globalStyles', GSCustomCss.GlobalStylesId );
	}, [] );

	if ( ! globalStyles ) {
        return <Spinner />;
	}

    const { styles, /* settings, */ title } = globalStyles;

    return <div className="gs-global-styles-ui">
        <h3>{ title.rendered } { __( '(FSE Styles)' ) }</h3>
        <CSSEditorUI styles={ styles.css } />
        {
            Object.keys( styles.blocks ).map( ( blockType ) => (
                <div key={blockType}>
                    <h4>{ blockType }</h4>
                    <TransformedStylesUI
                        source={ styles.blocks[ blockType ].css }
                        wrapper=".wrapper"
                    />
                </div>
            ) )
        }
    </div>;
}
