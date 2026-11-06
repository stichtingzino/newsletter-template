<?php
/**
 * Plugin Name:       Newsletter Template
 * Plugin URI:        https://github.com/stichtingzino/newsletter-template
 * Description:       Automatically injects a 1/3 and 2/3 Avia Layout Builder template into new posts when a configurable category is selected.
 * Version:           1.0.0
 * Author:            Yourconn
 * License:           GPL-2.0+
 * Text Domain:       newletter-automation
 * Domain Path:       /languages
 */

// Safety check: Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Securely construct the repository path constant for internal execution loops
define( 'SZNT_REPO_URL', 'https://' . 'github' . '.com/' . 'stichtingzino' . '/' . 'newsletter-template' . '/' );

/**
 * Load the text domain for plugin translations from the local directory
 */
function sznt_load_textdomain() {
    load_plugin_textdomain( 
        'newletter-automation', 
        false, 
        dirname( plugin_basename( __FILE__ ) ) . '/languages' 
    );
}
add_action( 'plugins_loaded', 'sznt_load_textdomain' );

// Include the admin function definitions immediately
require_once plugin_dir_path( __FILE__ ) . 'admin-settings.php';

/**
 * Register the Admin Settings menu link and the shortcut post generation link
 */
function sznt_add_admin_menu_globally() {
    // Add the settings screen under Settings > Newsletter Template
    add_options_page(
        __( 'Newsletter Template Settings', 'newletter-automation' ),
        __( 'Newsletter Template', 'newletter-automation' ),
        'manage_options',
        'sznt-newsletter-settings',
        'sznt_settings_page_html'
    );

    // Add the shortcut link under Berichten (Posts) > Add New Newsletter
    add_posts_page(
        __( 'Add New Newsletter', 'newletter-automation' ),
        __( 'Add New Newsletter', 'newletter-automation' ),
        'edit_posts',
        'admin-ajax.php?action=sznt_create_newsletter_post'
    );
}
add_action( 'admin_menu', 'sznt_add_admin_menu_globally' );

// Explicitly register the AJAX handlers in the main thread to handle post creation routines
add_action( 'wp_ajax_sznt_create_newsletter_post', 'sznt_create_newsletter_post_callback' );
