<?php

/**
 * Template: sb_project Archive
 */
get_header();

global $wp_query;
?>

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