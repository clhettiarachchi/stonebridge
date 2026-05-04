<?php

/**
 * AJAX handler for sb_project filtering and pagination.
 * Verifies nonce, builds WP_Query with tax_query, returns card HTML.
 */

function sb_filter_projects()
{
    check_ajax_referer('sb_projects_nonce', 'nonce');

    $type   = sanitize_text_field($_POST['type'] ?? '');
    $status = sanitize_text_field($_POST['status'] ?? '');
    $page   = absint($_POST['page'] ?? 1);

    $args = [
        'post_type'      => 'sb_project',
        'posts_per_page' => 6,
        'paged'          => $page,
    ];

    // Build tax_query only when filters are active
    $tax_query = [];

    if ($type) {
        $tax_query[] = [
            'taxonomy' => 'sb_project_type',
            'field'    => 'slug',
            'terms'    => $type,
        ];
    }

    if ($status) {
        $tax_query[] = [
            'taxonomy' => 'sb_project_status',
            'field'    => 'slug',
            'terms'    => $status,
        ];
    }

    if (count($tax_query) > 1) {
        $tax_query['relation'] = 'AND';
    }

    if (!empty($tax_query)) {
        $args['tax_query'] = $tax_query;
    }

    $query = new WP_Query($args);

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            get_template_part('template-parts/project-card');
        }
    } else {
        echo '<p class="projects-empty">' . esc_html__('No projects found.', 'stonebridge') . '</p>';
    }

    // Pagination data for JS
    echo '<script>window._sbTotalPages = ' . absint($query->max_num_pages) . ';</script>';

    wp_reset_postdata();
    wp_die();
}

add_action('wp_ajax_sb_filter_projects', 'sb_filter_projects');
add_action('wp_ajax_nopriv_sb_filter_projects', 'sb_filter_projects');
