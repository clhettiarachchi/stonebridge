<?php

/**
 * Partial: Project results — grid and pagination.
 * Used by archive-sb_project.php and ajax-projects.php.
 */

$query        = $args['query'];
$current_page = $args['current_page'];
?>

<?php if ($query->have_posts()) : ?>
    <div class="projects-grid">
        <?php while ($query->have_posts()) : $query->the_post(); ?>
            <?php get_template_part('template-parts/project-card'); ?>
        <?php endwhile; ?>
    </div>

    <?php if ($query->max_num_pages > 1) : ?>
        <nav class="projects-pagination">
            <?php
            $pages = paginate_links([
                'total'     => $query->max_num_pages,
                'current'   => $current_page,
                'type'      => 'array',
                'prev_next' => false,
                'base'      => strtok(get_pagenum_link(), '?') . '%_%',
                'format'    => '?page=%#%',
            ]);
            foreach ($pages as $page) : ?>
                <?php echo $page; ?>
            <?php endforeach; ?>
        </nav>
    <?php endif; ?>

<?php else : ?>
    <p class="projects-empty"><?php esc_html_e('No projects found.', 'stonebridge'); ?></p>
<?php endif; ?>

<?php wp_reset_postdata(); ?>