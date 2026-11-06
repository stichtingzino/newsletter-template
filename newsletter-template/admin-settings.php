<?php
/**
 * Admin Settings & Avia Shortcode Generation
 * This file is loaded globally to ensure hooks are available during AJAX routes.
 */

// Safety check: Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 1. AJAX Handler: Create a pre-formatted post with Avia Layout Builder codes
 */
function sznt_create_newsletter_post_callback() {
    if ( ! current_user_can( 'edit_posts' ) ) {
        wp_die( __( 'You do not have sufficient permissions to access this page.', 'newletter-automation' ) );
    }

    // Generate the raw Avia Advanced Layout Builder shortcodes
    $avia_content = "[av_textblock size='' av-medium-font-size='' av-small-font-size='' av-mini-font-size='' font_color='' color='' id='' custom_class='' template_class='' av_uid='' sc_version='1.0' admin_preview_bg='']\n";
    $avia_content .= "Inleiding: Typ hier de introductie van de nieuwsbrief...\n";
    $avia_content .= "[/av_textblock]\n\n";

    for ( $i = 0; $i < 5; $i++ ) {
        // 1/3 Column containing an Image Placeholder
        $avia_content .= "[av_one_third first min_height='' av_element_min_height='' linear_gradient='' background_color='' background_gradient_color1='' background_gradient_color2='' background_gradient_direction='vertical' background_gradient_style='linear' border='' border_color='' radius='' radius_sync='true' space='' background_image='' background_position='top left' background_repeat='no-repeat' padding='' padding_sync='true' animation='' id='' custom_class='' template_class='' av_uid='' sc_version='1.0']\n";
        $avia_content .= "[av_image src='' attachment='' attachment_size='' alt='' title='' link='' target='' size='portfolio' caption='' heading='' overlay_opacity='0.4' overlay_color='#000000' overlay_text_color='#ffffff' animation='no-animation' id='' custom_class='' template_class='' av_uid='' sc_version='1.0' admin_preview_bg=''][/av_image]\n";
        $avia_content .= "[/av_one_third]\n\n";

        // 2/3 Column containing a Heading (0px margin) and a Paragraph
        $avia_content .= "[av_two_third min_height='' av_element_min_height='' linear_gradient='' background_color='' background_gradient_color1='' background_gradient_color2='' background_gradient_direction='vertical' background_gradient_style='linear' border='' border_color='' radius='' radius_sync='true' space='' background_image='' background_position='top left' background_repeat='no-repeat' padding='' padding_sync='true' animation='' id='' custom_class='' template_class='' av_uid='' sc_version='1.0']\n";
        $avia_content .= "[av_heading heading='Koptekst van deze rij' tag='h3' style='custom' size='' subheading_active='' subheading_size='' margin='0px,0px,10px,0px' padding='0' color='' custom_font='' id='' custom_class='' template_class='' av_uid='' sc_version='1.0' admin_preview_bg=''][/av_heading]\n";
        $avia_content .= "[av_textblock size='' av-medium-font-size='' av-small-font-size='' av-mini-font-size='' font_color='' color='' id='' custom_class='' template_class='' av_uid='' sc_version='1.0' admin_preview_bg='']\n";
        $avia_content .= "Korte alinea van maximaal 4 regels...\n";
        $avia_content .= "[/av_textblock]\n";
        $avia_content .= "[/av_two_third]\n\n";
    }

    $newsletter_post_data = array(
        'post_title'   => __( 'Newsletter ', 'newletter-automation' ) . date( 'd-m-Y' ),
        'post_content' => $avia_content,
        'post_status'  => 'draft',
        'post_type'    => 'post',
    );

    $post_id = wp_insert_post( $newsletter_post_data );

    if ( $post_id && ! is_wp_error( $post_id ) ) {
        $target_slug = get_option( 'sznt_target_category', '' );
        if ( ! empty( $target_slug ) ) {
            $category = get_category_by_slug( $target_slug );
            if ( $category ) {
                wp_set_post_categories( $post_id, array( $category->term_id ) );
            }
        }

        // Set the foundational Enfold layout status flags
        update_post_meta( $post_id, '_aviaLayoutBuilder_status', 'active' );
        update_post_meta( $post_id, '_avia_builder_active', 'active' );

        wp_redirect( admin_url( 'post.php?post=' . $post_id . '&action=edit' ) );
        exit;
    } else {
        wp_die( __( 'Failed to auto-create the newsletter template draft.', 'newletter-automation' ) );
    }
}

/**
 * 2. Force Enfold Avia Builder state to 'active' on load for our dynamic drafts
 */
function sznt_force_avia_builder_active_on_load( $builder_status, $post_id ) {
    // Check if the current post screen belongs to an automated newsletter draft
    $current_post = get_post( $post_id );
    if ( $current_post && $current_post->post_status === 'draft' ) {
        $target_slug = get_option( 'sznt_target_category', '' );
        if ( ! empty( $target_slug ) && has_category( $target_slug, $post_id ) ) {
            // Force Enfold to evaluate the builder layout mode as strictly 'active'
            return 'active';
        }
    }
    return $builder_status;
}
// Intercept Enfold's internal layout builder status filter
add_filter( 'avf_builder_active', 'sznt_force_avia_builder_active_on_load', 9999, 2 );

/**
 * 3. Register settings and fields
 */
function sznt_settings_init() {
    register_setting( 'sznt_settings_group', 'sznt_target_category' );

    add_settings_section(
        'sznt_main_section',
        __( 'General Settings', 'newletter-automation' ),
        'sznt_section_text_callback',
        'sznt-newsletter-settings'
    );

    add_settings_field(
        'sznt_target_category_field',
        __( 'Target Category', 'newletter-automation' ),
        'sznt_category_field_callback',
        'sznt-newsletter-settings',
        'sznt_main_section'
    );
}
add_action( 'admin_init', 'sznt_settings_init' );

function sznt_section_text_callback() {
    echo '<p>' . esc_html__( 'Select which category should be assigned automatically to the newsletter template drafts.', 'newletter-automation' ) . '</p>';
}

function sznt_category_field_callback() {
    $selected_cat = get_option( 'sznt_target_category' );
    $categories   = get_categories( array( 'hide_empty' => 0 ) );
    
    echo '<select name="sznt_target_category">';
    echo '<option value="">' . esc_html__( '-- Select a Category --', 'newletter-automation' ) . '</option>';
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
 * 4. Render the Admin Settings Page HTML
 */
function sznt_settings_page_html() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }
    ?>
    <div class="wrap">
        <h1><?= esc_html( get_admin_page_title() ); ?></h1>
        <form action="options.php" method="post">
            <?php
            settings_fields( 'sznt_settings_group' );
            do_settings_sections( 'sznt-newsletter-settings' );
            submit_button( __( 'Save Settings', 'newletter-automation' ) );
            ?>
        </form>
    </div>
    <?php
}
