<?php

/**
 * Theme Functions
 */

require_once get_template_directory() . '/inc/cpt-projects.php';

function sb_enqueue_assets()
{
    if (is_post_type_archive('sb_project')) {
        wp_enqueue_style(
            'sb-projects',
            get_template_directory_uri() . '/assets/css/projects.css',
            [],
            '1.0.0'
        );
    }
}
add_action('wp_enqueue_scripts', 'sb_enqueue_assets');
