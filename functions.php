<?php

/**
 * Theme Functions
 */

require_once get_template_directory() . '/inc/cpt-projects.php';
require_once get_template_directory() . '/inc/ajax-projects.php';

function sb_enqueue_assets()
{
    if (is_post_type_archive('sb_project')) {
        wp_enqueue_style(
            'sb-projects',
            get_template_directory_uri() . '/assets/css/projects.css',
            [],
            '1.0.0'
        );

        wp_enqueue_script(
            'sb-projects',
            get_template_directory_uri() . '/assets/js/projects.js',
            [],
            '1.0.0',
            true
        );

        wp_localize_script('sb-projects', 'sbProjects', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce'   => wp_create_nonce('sb_projects_nonce'),
        ]);
    }
}
add_action('wp_enqueue_scripts', 'sb_enqueue_assets');
