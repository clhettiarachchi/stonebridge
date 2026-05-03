<?php

/**
 * Partial: Project Card
 */
$location = get_post_meta(get_the_ID(), 'sb_location', true);
$year     = get_post_meta(get_the_ID(), 'sb_year', true);
$value    = get_post_meta(get_the_ID(), 'sb_value', true);
$types    = get_the_terms(get_the_ID(), 'sb_project_type');
$statuses = get_the_terms(get_the_ID(), 'sb_project_status');
?>

<div class="project-card">
    <?php if (has_post_thumbnail()) : ?>
        <div class="project-card__image">
            <?php the_post_thumbnail('medium'); ?>
        </div>
    <?php endif; ?>

    <div class="project-card__body">
        <h3 class="project-card__title"><?php the_title(); ?></h3>

        <?php if ($types && !is_wp_error($types)) : ?>
            <span class="project-card__type"><?php echo esc_html($types[0]->name); ?></span>
        <?php endif; ?>

        <?php if ($statuses && !is_wp_error($statuses)) : ?>
            <span class="project-card__status"><?php echo esc_html($statuses[0]->name); ?></span>
        <?php endif; ?>

        <div class="project-card__meta">
            <?php if ($location) : ?>
                <span class="project-card__location"><?php echo esc_html($location); ?></span>
            <?php endif; ?>
            <?php if ($year) : ?>
                <span class="project-card__year"><?php echo esc_html($year); ?></span>
            <?php endif; ?>
            <?php if ($value) : ?>
                <span class="project-card__value"><?php echo esc_html($value); ?></span>
            <?php endif; ?>
        </div>

        <div class="project-card__excerpt">
            <?php the_excerpt(); ?>
        </div>
    </div>
</div>