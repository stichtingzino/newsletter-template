<?php
/**
 * Admin Settings Functionality
 * This file is only loaded when is_admin() evaluates to true.
 */

// Safety check: Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 1. Register the Settings Page under Settings > Newsletter Template
 */
function ycnt_add_admin_menu() {
    add_options_page(
        'Newsletter Template Settings',
        'Newsletter Template',
        'manage_options',
        'ycnt-newsletter-settings',
        'ycnt_settings_page_html'
    );
}
add_action( 'admin_menu', 'ycnt_add_admin_menu' );

/**
 * 2. Register settings and fields
 */
function ycnt_settings_init() {
    register_setting( 'ycnt_settings_group', 'ycnt_target_category' );

    add_settings_section(
        'ycnt_main_section',
        'General Settings',
        'ycnt_section_text_callback',
        'ycnt-newsletter-settings'
    );

    add_settings_field(
        'ycnt_target_category_field',
        'Target Category',
        'ycnt_category_field_callback',
        'ycnt-newsletter-settings',
        'ycnt_main_section'
    );
}
add_action( 'admin_init', 'ycnt_settings_init' );

function ycnt_section_text_callback() {
    echo '<p>Select which category should trigger the automatic block template injection for new posts.</p>';
}

/**
 * Render a dropdown of all available categories
 */
function ycnt_category_field_callback() {
    $selected_cat = get_option( 'ycnt_target_category' );
    $categories   = get_categories( array( 'hide_empty' => 0 ) );
    
    echo '<select name="ycnt_target_category">';
    echo '<option value="">-- Select a Category --</option>';
    foreach ( $categories as $category ) {
        printf(
            '<option value="%s" %s>%s</option>',
            esc_attr( $category->slug ),
            selected( $selected_cat, $category->slug, false ),
            esc_html( $category->name )
        );
    }
    echo '</select>';
}

/**
 * Render the Admin Settings Page HTML
 */
function ycnt_settings_page_html() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }
    ?>
    <div class="wrap">
        <h1><?= esc_html( get_admin_page_title() ); ?></h1>
        <form action="options.php" method="post">
            <?php
            settings_fields( 'ycnt_settings_group' );
            do_settings_sections( 'ycnt-newsletter-settings' );
            submit_button( 'Save Settings' );
            ?>
        </form>
    </div>
    <?php
}
