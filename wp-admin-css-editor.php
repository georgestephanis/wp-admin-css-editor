<?php
/**
 * Plugin Name: WP Admin CSS Editor
 * Plugin author: georgestephanis
 */
namespace Georgestephanis\CustomCSS;

/**
 * Initialize our custom REST routes.
 *
 * @return void
 */
function rest_api_init() {
	register_rest_route(
		'gscustomcss/v1',
		'customizer',
		array(
			'methods'             => 'GET',
			'callback'            => __NAMESPACE__ . '\rest_api_get_customizer_css',
			'permission_callback' => '__return_true'
		)
	);
}
add_action( 'rest_api_init', __NAMESPACE__ . '\rest_api_init' );

/**
 * Get the Customizer's CSS.
 *
 * @param \WP_Rest_Request $request The rest request being passed to the api.
 * @return array
 */
function rest_api_get_customizer_css(  \WP_Rest_Request $request  ) {
	$post = wp_get_custom_css_post();

	return array(
		'id'  => $post ? $post->id : 0,
		'css' => wp_get_custom_css(),
	);
}

/**
 * Get the FSE's CSS Variables.
 *
 * @return array
 */
function get_core_variables() {
	$core_variables = array();

	$core_variables_css = \WP_Theme_JSON_Resolver::get_core_data()->get_stylesheet( [ 'variables' ] );
	if ( preg_match( '#:root\{(.*)\}#', $core_variables_css, $matches ) ) {
		preg_match_all( '/(--wp-[^:]+): ([^;]+);/', $matches[1], $core_variables_matched, PREG_SET_ORDER );
		foreach ( $core_variables_matched as list( $match, $property, $value ) ) {
			$core_variables[ $property ] = $value;
		}
	}

	return $core_variables;
}

add_action( 'admin_menu', function() {
	add_theme_page(
		__( 'Theme Custom CSS' ),
		__( 'CSS' ),
		'edit_theme_options',
		'georgestephanis-custom-css',
		__NAMESPACE__ . '\admin_page'
	);
} );

add_action( 'admin_enqueue_scripts', function() {
	if ( 'appearance_page_georgestephanis-custom-css' === get_current_screen()->id ) {
		$asset_file = include( plugin_dir_path( __FILE__ ) . 'build/index.asset.php');
		wp_enqueue_style(
			'wp-admin-css-editor',
			plugins_url( 'build/index.css', __FILE__ ),
			array(),
			$asset_file['version']
		);

		wp_enqueue_script(
			'wp-admin-css-editor',
			plugins_url( 'build/index.js', __FILE__ ),
			$asset_file['dependencies'],
			$asset_file['version'],
			true
		);

		wp_localize_script(
			'wp-admin-css-editor',
			'GSCustomCss',
			array(
				'GlobalStylesId' => \WP_Theme_JSON_Resolver::get_user_data_from_wp_global_styles( null )['ID'],
				'CoreVariables' => get_core_variables(),
			)
		);
	}
});

function _admin_page_output_css_editor( $id, $content, $codemirror_settings ) {
	$id = sanitize_title( $id );

	printf( '<textarea id="%1$s">%2$s</textarea>', esc_attr( $id ), esc_textarea( $content ) );
	if ( false !== $codemirror_settings ) {
		wp_add_inline_script(
			'code-editor',
			sprintf(
				'jQuery( function() { wp.codeEditor.initialize( %1$s, %2$s ); } );',
				wp_json_encode( $id ),
				wp_json_encode( $codemirror_settings )
			)
		);
	}
}

function admin_page() {
	$codemirror_settings = wp_enqueue_code_editor( [ 'type' => 'text/css' ] );
	$codemirror_settings['codemirror']['readOnly'] = true;

	?>
	<div class="wrap">
		<h1 class="wp-heading-inline"><?php _e( 'Theme CSS' ); ?></h1>

		<h3><?php _e( 'Custom CSS from the Customizer (Older System)' ); ?></h3>

		<?php
		$custom_css = wp_get_custom_css();

		if ( $custom_css ) {
			_admin_page_output_css_editor( 'customizer-css', $custom_css, $codemirror_settings );
		} else {
			_e( 'No Customizer CSS' );
		}
		?>

		<!-- React Root for CodeMirror stuff: -->
		<div id="admin-css-editor-root"></div>
	</div>
	<?php
}
