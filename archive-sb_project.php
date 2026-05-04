<?php

/**
 * Template: sb_project Archive
 */
get_header();

global $wp_query;

// Read URL params
$filter_type   = sanitize_text_field($_GET['type'] ?? '');
$filter_status = sanitize_text_field($_GET['status'] ?? '');
$filter_page   = absint($_GET['page'] ?? 1);

// Build tax_query if filters are present
$tax_query = [];

if ($filter_type) {
    $tax_query[] = [
        'taxonomy' => 'sb_project_type',
        'field'    => 'slug',
        'terms'    => $filter_type,
    ];
}

if ($filter_status) {
    $tax_query[] = [
        'taxonomy' => 'sb_project_status',
        'field'    => 'slug',
        'terms'    => $filter_status,
    ];
}

if (count($tax_query) > 1) {
    $tax_query['relation'] = 'AND';
}

// Modify main query
if (!empty($tax_query) || $filter_page > 1) {
    $args = [
        'post_type'      => 'sb_project',
        'posts_per_page' => 6,
        'paged'          => $filter_page,
    ];

    if (!empty($tax_query)) {
        $args['tax_query'] = $tax_query;
    }

    $wp_query = new WP_Query($args);
}
?>

<div class="projects-listing">
    <div class="projects-filters">
        <select class="projects-filters__type" name="sb_project_type">
            <option value=""><?php esc_html_e('All Types', 'stonebridge'); ?></option>

            <?php
            $types = get_terms(['taxonomy' => 'sb_project_type', 'hide_empty' => false]);
            foreach ($types as $type) : ?>
                <option value="<?php echo esc_attr($type->slug); ?>" <?php selected($filter_type, $type->slug); ?>>
                    <?php echo esc_html($type->name); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select class="projects-filters__status" name="sb_project_status">
            <option value=""><?php esc_html_e('All Statuses', 'stonebridge'); ?></option>
            <?php
            $statuses = get_terms(['taxonomy' => 'sb_project_status', 'hide_empty' => false]);
            foreach ($statuses as $status) : ?>
                <option value="<?php echo esc_attr($status->slug); ?>" <?php selected($filter_status, $status->slug); ?>>
                    <?php echo esc_html($status->name); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <?php
    get_template_part('template-parts/project-results', null, [
        'query'        => $wp_query,
        'current_page' => $filter_page ?: 1,
    ]);
    ?>
</div>

<?php get_footer(); ?>