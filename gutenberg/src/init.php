<?php
/**
 * Enqueue Gutenberg block assets for both frontend + backend.
 *
 * Assets enqueued:
 * 1. blocks.style.build.css - Frontend + Backend.
 * 2. blocks.build.js - Backend.
 * 3. blocks.editor.build.css - Backend.
 *
 * @uses {wp-blocks} for block type registration & related functions.
 * @uses {wp-element} for WP Element abstraction — structure of blocks.
 * @uses {wp-i18n} to internationalize the block's text.
 * @uses {wp-editor} for WP editor styles.
 * @since 1.0.0
 */
function my_custom_block_cgb_block_assets() {
	wp_register_style(
		'argon-gutenberg-block-frontend-css',
		plugins_url( 'dist/blocks.style.build.css', dirname( __FILE__ ) ), // Block style CSS.
		is_admin() ? array( 'wp-editor' ) : null, // Dependency to include the CSS after it.
		null // filemtime( plugin_dir_path( __DIR__ ) . 'dist/blocks.style.build.css' ) // Version: File modification time.
	);
	wp_register_script(
		'argon-gutenberg-block-js', // Handle.
		plugins_url( '/dist/blocks.build.js', dirname( __FILE__ ) ), // Block.build.js: We register the block here. Built with Webpack.
		array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor' ), // Dependencies, defined above.
		null, // filemtime( plugin_dir_path( __DIR__ ) . 'dist/blocks.build.js' ), // Version: filemtime — Gets file modification time.
		true // Enqueue the script in the footer.
	);

	// Register block editor styles for backend.
	wp_register_style(
		'argon-gutenberg-block-backend-css',
		plugins_url( 'dist/blocks.editor.build.css', dirname( __FILE__ ) ),
		array( 'wp-edit-blocks' ),
		filemtime( plugin_dir_path( __DIR__ ) . 'dist/blocks.editor.build.css' )
		//null // filemtime( plugin_dir_path( __DIR__ ) . 'dist/blocks.editor.build.css' ) // Version: File modification time.
	);
	wp_localize_script(
		'my_custom_block-cgb-block-js',
		'cgbGlobal',
		[
			'pluginDirPath' => plugin_dir_path( __DIR__ ),
			'pluginDirUrl'  => plugin_dir_url( __DIR__ ),
		]
	);
	register_block_type(
		'cgb/block-my-custom-block', array(
			// Enqueue blocks.style.build.css on both frontend & backend.
			//'style'         => 'argon-gutenberg-block-frontend-css',
			// Enqueue blocks.build.js in the editor only.
			'editor_script' => 'argon-gutenberg-block-js',
			// Enqueue blocks.editor.build.css in the editor only.
			'editor_style'  => 'argon-gutenberg-block-backend-css',
		)
	);
}
add_action('init', 'my_custom_block_cgb_block_assets');

function filter_block_categories_when_post_provided( $block_categories, $editor_context ) {
    if (!empty($editor_context->post)){
        array_push(
            $block_categories,
            array(
                'slug'  => 'argon',
                'title' => 'Argon',
                'icon'  => null,
			),

        );
    }
    return $block_categories;
}
add_filter('block_categories_all', 'filter_block_categories_when_post_provided', 10, 2);