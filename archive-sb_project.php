<?php

/**
 * Template: sb_project Archive
 */
get_header();

global $wp_query;
?>

<div class="projects-filters">
    <select class="projects-filters__type" name="sb_project_type">
        <option value=""><?php esc_html_e('All Types', 'stonebridge'); ?></option>
        <?php
        $types = get_terms(['taxonomy' => 'sb_project_type', 'hide_empty' => false]);
        foreach ($types as $type) : ?>
            <option value="<?php echo esc_attr($type->slug); ?>">
                <?php echo esc_html($type->name); ?>
            </option>
        <?php endforeach; ?>
    </select>

    <select class="projects-filters__status" name="sb_project_status">
        <option value=""><?php esc_html_e('All Statuses', 'stonebridge'); ?></option>
        <?php
        $statuses = get_terms(['taxonomy' => 'sb_project_status', 'hide_empty' => false]);
        foreach ($statuses as $status) : ?>
            <option value="<?php echo esc_attr($status->slug); ?>">
                <?php echo esc_html($status->name); ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>

<div class="projects-listing">
    <?php if (have_posts()) : ?>
        <div class="projects-grid">
            <?php while (have_posts()) : the_post(); ?>
                <?php get_template_part('template-parts/project-card'); ?>
            <?php endwhile; ?>
        </div>
    <?php else : ?>
        <p><?php esc_html_e('No projects found.', 'stonebridge'); ?></p>
    <?php endif; ?>
</div>

<?php get_footer(); ?>