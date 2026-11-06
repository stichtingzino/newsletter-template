<?php
/**
 * Plugin Name:       Newsletter Template
 * Plugin URI:        https://github.com/stichtingzino/newsletter-template
 * Description:       Automatically injects a 1/3 and 2/3 column layout template into a new post when a configurable category is selected.
 * Version:           1.0.0
 * Author:            Yourconn
 * License:           GPL-2.0+
 * Text Domain:       newletter-automation
 */

// Safety check: Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Safely include the admin settings file using standard WordPress path constants
if ( is_admin() ) {
    require_once plugin_dir_path( __FILE__ ) . 'admin-settings.php';
}

/**
 * 1. Register the Newsletter Block Pattern
 */
function ycnt_register_newsletter_pattern() {
    register_block_pattern(
        'ycnt/newsletter-template',
        array(
            'title'       => __( 'Newsletter Template', 'newletter-automation' ),
            'description' => __( 'Standard column layout for newsletters.', 'newletter-automation' ),
            'categories'  => array( 'text' ),
            'content'     => '<!-- wp:paragraph {"placeholder":"Inleiding: Typ hier de introductie van de nieuwsbrief...","className":"nieuwsbrief-intro"} -->
                            <p class="nieuwsbrief-intro"></p>
                            <!-- /wp:paragraph -->

                            ' . str_repeat('<!-- wp:columns {"verticalAlignment":"top"} -->
                            <div class="wp-block-columns are-vertically-aligned-top"><!-- wp:column {"width":"33.33%","verticalAlignment":"top"} -->
                            <div class="wp-block-column are-vertically-aligned-top" style="flex-basis:33.33%"><!-- wp:image {"sizeSlug":"large","linkDestination":"none"} -->
                            <figure class="wp-block-image size-large"><img src="" alt=""/></figure>
                            <!-- /wp:image --></div>
                            <!-- /wp:column -->

                            <!-- wp:column {"width":"66.66%","verticalAlignment":"top"} -->
                            <div class="wp-block-column are-vertically-aligned-top" style="flex-basis:66.66%"><!-- wp:heading {"level":3,"style":{"spacing":{"margin":{"top":"0px"}}},"placeholder":"Koptekst zonder top-margin"} -->
                            <h3 class="wp-block-heading" style="margin-top:0px"></h3>
                            <!-- /wp:heading -->

                            <!-- wp:paragraph {"placeholder":"Korte alinea van maximaal 4 regels..."} -->
                            <p></p>
                            <!-- /wp:paragraph --></div>
                            <!-- /wp:column --></div>
                            <!-- /wp:columns -->', 5),
        )
    );
}
add_action( 'init', 'ycnt_register_newsletter_pattern' );

/**
 * 2. Load the script in the Gutenberg block editor to monitor category selection
 */
function ycnt_enqueue_block_editor_assets() {
    // Get the configured category slug from the settings page
    $target_category = get_option( 'ycnt_target_category', '' );

    // If no category is configured, do not load the dynamic behavior
    if ( empty( $target_category ) ) {
        return;
    }

    $handle = 'wp-edit-post';

    // Injecting variables safely using the native WP API
    wp_localize_script( $handle, 'ycntSettings', array(
        'targetCategorySlug' => $target_category
    ) );

    // Pure JavaScript logic for the Gutenberg editor
    $script = "
    ( function( wp ) {
        wp.domReady( function() {
            let hasApplied = false;
            
            if ( typeof ycntSettings === 'undefined' || ! ycntSettings.targetCategorySlug ) {
                return;
            }

            const targetCategorySlug = ycntSettings.targetCategorySlug;
            
            wp.data.subscribe( function() {
                const editor = wp.data.select( 'core/editor' );
                if ( ! editor ) return;

                const isPostEmpty = editor.getEditedPostContent() === '';
                const currentCategories = editor.getEditedPostAttribute( 'categories' ) || [];
                
                const categoriesData = wp.data.select( 'core' ).getEntityRecords( 'taxonomy', 'category', { per_page: -1 } );
                if ( ! categoriesData ) return;

                const newsletterCat = categoriesData.find( cat => cat.slug === targetCategorySlug );
                if ( ! newsletterCat ) return;

                if ( currentCategories.includes( newsletterCat.id ) && isPostEmpty && ! hasApplied ) {
                    hasApplied = true;
                    
                    const pattern = wp.data.select( 'core' ).getBlockPatterns().find( p => p.name === 'ycnt/newsletter-template' );
                    
                    if ( pattern ) {
                        const blocks = wp.blocks.parse( pattern.content );
                        wp.data.dispatch( 'core/editor' ).insertBlocks( blocks );
                        
                        wp.data.dispatch( 'core/notices' ).createNotice(
                            'success',
                            'Newsletter template automatically loaded!',
                            { isDismissible: true, type: 'snackbar' }
                        );
                    }
                }
            });
        });
    } )( window.wp );
    ";

    wp_add_inline_script( $handle, $script );
}
add_action( 'enqueue_block_editor_assets', 'ycnt_enqueue_block_editor_assets' );
