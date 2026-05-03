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

// Meta box registration
function sb_register_project_meta_box()
{
    add_meta_box(
        'sb_project_details',
        __('Project Details', 'stonebridge'),
        'sb_render_project_meta_box',
        'sb_project',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'sb_register_project_meta_box');

// Meta box markup
function sb_render_project_meta_box($post)
{
    wp_nonce_field('sb_project_meta_save', 'sb_project_meta_nonce');

    $location = get_post_meta($post->ID, 'sb_location', true);
    $year     = get_post_meta($post->ID, 'sb_year', true);
    $value    = get_post_meta($post->ID, 'sb_value', true);
?>
    <table class="form-table">
        <tr>
            <th><label for="sb_location"><?php esc_html_e('Location', 'stonebridge'); ?></label></th>
            <td><input type="text" id="sb_location" name="sb_location" value="<?php echo esc_attr($location); ?>" class="regular-text"></td>
        </tr>
        <tr>
            <th><label for="sb_year"><?php esc_html_e('Year', 'stonebridge'); ?></label></th>
            <td><input type="number" id="sb_year" name="sb_year" value="<?php echo esc_attr($year); ?>" class="small-text"></td>
        </tr>
        <tr>
            <th><label for="sb_value"><?php esc_html_e('Project Value (AUD)', 'stonebridge'); ?></label></th>
            <td><input type="text" id="sb_value" name="sb_value" value="<?php echo esc_attr($value); ?>" class="regular-text" placeholder="e.g. $42M"></td>
        </tr>
    </table>
<?php
}

// Save meta
function sb_save_project_meta($post_id)
{
    if (
        !isset($_POST['sb_project_meta_nonce']) ||
        !wp_verify_nonce($_POST['sb_project_meta_nonce'], 'sb_project_meta_save') ||
        defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ||
        !current_user_can('edit_post', $post_id)
    ) return;

    if (isset($_POST['sb_location'])) {
        update_post_meta($post_id, 'sb_location', sanitize_text_field($_POST['sb_location']));
    }
    if (isset($_POST['sb_year'])) {
        update_post_meta($post_id, 'sb_year', absint($_POST['sb_year']));
    }
    if (isset($_POST['sb_value'])) {
        update_post_meta($post_id, 'sb_value', sanitize_text_field($_POST['sb_value']));
    }
}
add_action('save_post_sb_project', 'sb_save_project_meta');
