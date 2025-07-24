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
			'wp-admin-code-editor',
			plugins_url( 'build/index.css', __FILE__ ),
			array(),
			$asset_file['version']
		);

		wp_enqueue_script(
			'wp-admin-code-editor',
			plugins_url( 'build/index.js', __FILE__ ),
			$asset_file['dependencies'],
			$asset_file['version'],
			true
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
	$user_styles = \WP_Theme_JSON_Resolver::get_user_data();

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

		<h3><?php _e( 'Custom CSS from the Full Site Editor (Newer System)' ); ?></h3>

		<?php
		$user_styles_stylesheet = $user_styles->get_stylesheet( [ 'custom-css' ] );
		if ( $user_styles_stylesheet ) {
			_admin_page_output_css_editor( 'blockeditor-css', $user_styles_stylesheet, $codemirror_settings );
		} else {
			_e( 'No Full-Site Editor CSS' );
		}
		?>

		<h3><?php _e( 'Custom Block CSS from the Full Site Editor' ); ?></h3>

		<?php
		$user_styles_raw = $user_styles->get_raw_data();
		if ( isset( $user_styles_raw, $user_styles_raw['styles'], $user_styles_raw['styles']['blocks'] ) ) {
			foreach ( $user_styles_raw['styles']['blocks'] as $block_type => $block_properties ) {
				if ( isset( $block_properties['css'] ) ) {
					printf( '<h4>%s</h4>', $block_type );
					_admin_page_output_css_editor( "{$block_type}-css", $block_properties['css'], $codemirror_settings );
				}
			}
		}
		?>

		<div id="admin-css-editor-root"></div>

		<h3><?php _e( 'CSS Variables from the Full Site Editor'); ?></h3>

		<h4>User:</h4>

		<pre><?php
			echo str_replace(
				[ '{--',       ';--',       ';}'     ],
				[ "{\r\n\t--", ";\r\n\t--", ";\r\n}" ],
				$user_styles->get_stylesheet( [ 'variables' ] )
			);
		?></pre>

		<h4>Core:</h4>

		<pre><?php
			echo str_replace(
				[ '{--',       ';--',       ';}'     ],
				[ "{\r\n\t--", ";\r\n\t--", ";\r\n}" ],
				\WP_Theme_JSON_Resolver::get_core_data()->get_stylesheet( [ 'variables' ] )
			);
		?></pre>

	</div>
	<?php
}
