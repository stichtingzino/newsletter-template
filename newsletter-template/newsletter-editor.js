( function( wp ) {
    // Wait until the DOM and Gutenberg are fully operational
    wp.domReady( function() {
        let hasApplied = false;
        
        // Ensure data was successfully provided from the PHP configuration hook
        if ( typeof szntSettings === 'undefined' || ! szntSettings.targetCategorySlug ) {
            return;
        }

        const targetCategorySlug = szntSettings.targetCategorySlug;
        
        // Listen to changes inside the Gutenberg editor stores safely
        wp.data.subscribe( function() {
            // Safe fallback check to prevent script crashes if editor store isn't fully ready
            if ( ! wp.data.select( 'core/editor' ) || ! wp.data.select( 'core' ) ) {
                return;
            }

            const editor = wp.data.select( 'core/editor' );
            
            // Check if the current post content is completely empty
            const isPostEmpty = editor.getEditedPostContent() === '';
            const currentCategories = editor.getEditedPostAttribute( 'categories' ) || [];
            
            // Fetch taxonomy terms from store to safely resolve slugs to terms IDs
            const categoriesData = wp.data.select( 'core' ).getEntityRecords( 'taxonomy', 'category', { per_page: -1 } );
            if ( ! categoriesData ) return;

            const newsletterCat = categoriesData.find( function( cat ) {
                return cat.slug === targetCategorySlug;
            } );
            if ( ! newsletterCat ) return;

            // Trigger automation if the category is checked, post editor is empty, and it has not executed yet
            if ( currentCategories.includes( newsletterCat.id ) && isPostEmpty && ! hasApplied ) {
                hasApplied = true;
                
                // Fetch our custom newsletter template block configuration structure
                const pattern = wp.data.select( 'core' ).getBlockPatterns().find( function( p ) {
                    return p.name === 'sznt/newsletter-template';
                } );
                
                if ( pattern ) {
                    const blocks = wp.blocks.parse( pattern.content );
                    wp.data.dispatch( 'core/editor' ).insertBlocks( blocks );
                    
                    // Fire a snackbar notification inside the block editor UI
                    wp.data.dispatch( 'core/notices' ).createNotice(
                        'success',
                        'Newsletter template automatically loaded!',
                        { isDismissible: true, type: 'snackbar' }
                    );
                }
            }
        } );
    } );
} )( window.wp );
