<?php

/**
 * Registers the sb_project custom post type and its two taxonomies:
 * sb_project_type
 * sb_project_status
 */

function sb_register_project_cpt()
{
    register_post_type('sb_project', [
        'labels' => [
            'name'               => __('Projects', 'stonebridge'),
            'singular_name'      => __('Project', 'stonebridge'),
            'add_new'            => __('Add New Project', 'stonebridge'),
            'add_new_item'       => __('Add New Project', 'stonebridge'),
            'edit_item'          => __('Edit Project', 'stonebridge'),
            'view_item'          => __('View Project', 'stonebridge'),
            'all_items'          => __('All Projects', 'stonebridge'),
            'search_items'       => __('Search Projects', 'stonebridge'),
            'not_found'          => __('No projects found.', 'stonebridge'),
            'not_found_in_trash' => __('No projects found in Trash.', 'stonebridge'),
        ],
        'public'      => true,
        'has_archive' => true,
        'supports'    => ['title', 'excerpt', 'thumbnail'],
        'menu_icon'   => 'dashicons-building',
        'rewrite'     => ['slug' => 'projects'],
    ]);

    register_taxonomy('sb_project_type', 'sb_project', [
        'labels'       => [
            'name'          => __('Project Types', 'stonebridge'),
            'singular_name' => __('Project Type', 'stonebridge'),
        ],
        'hierarchical' => true,
        'rewrite'      => ['slug' => 'project-type'],
    ]);

    register_taxonomy('sb_project_status', 'sb_project', [
        'labels'       => [
            'name'          => __('Project Statuses', 'stonebridge'),
            'singular_name' => __('Project Status', 'stonebridge'),
        ],
        'hierarchical' => true,
        'rewrite'      => ['slug' => 'project-status'],
    ]);
}
add_action('init', 'sb_register_project_cpt');
