<?php

/**
 * Секция "Что мы изготавливаем": аккордеон категорий проектов.
 * Фото, количество и описание берутся из полей категории.
 */
defined('ABSPATH') || exit;

$eyebrow   = get_sub_field('eyebrow');
$title     = get_sub_field('title');
$text      = get_sub_field('text');
$btn_label = get_sub_field('btn_label');
$rows      = get_sub_field('items') ?: array();

// Превращаем строки повторителя в список категорий (WP_Term), пропуская пустые и удалённые.
$terms = array();
foreach ($rows as $row) {
    $term = ! empty($row['category']) ? get_term((int) $row['category'], 'project_category') : null;
    if ($term && ! is_wp_error($term)) {
        $terms[] = $term;
    }
}
if (! $terms) {
    return;
}
?>
<section class="pf">
    <div class="pf__inner">

        <div class="pf-intro">
            <?php if ($eyebrow) : ?>
                <div class="pf-eyebrow"><?php echo esc_html($eyebrow); ?></div>
            <?php endif; ?>
            <?php if ($title) : ?>
                <h2 class="pf-title"><?php echo esc_html($title); ?></h2>
            <?php endif; ?>
            <?php if ($text) : ?>
                <p class="pf-text"><?php echo esc_html($text); ?></p>
            <?php endif; ?>
        </div>

        <div class="pf-accordion">
            <?php foreach ($terms as $i => $term) : ?>
                <?php
                $image = get_field('cat_image', $term);
                $count = get_field('cat_count', $term);
                $short = get_field('cat_short', $term);
                ?>
                <a class="pf-panel" href="<?php echo esc_url(get_term_link($term)); ?>">
                    <?php
                    if ($image) {
                        $alt = trim((string) get_post_meta((int) $image, '_wp_attachment_image_alt', true));
                        echo wp_get_attachment_image($image, 'large', false, array(
                            'class'   => 'pf-panel__img',
                            'alt'     => $alt ?: esc_html($term->name),
                            'sizes'   => '(max-width: 760px) 100vw, 600px',
                            'loading' => 'lazy',
                        ));
                    }
                    ?>
                    <span class="pf-scrim" aria-hidden="true"></span>

                    <span class="pf-collapsed" aria-hidden="true">
                        <span class="pf-vlabel"><?php echo esc_html($term->name); ?></span>
                        <span class="pf-index"><?php echo esc_html(sprintf('%02d', $i + 1)); ?></span>
                    </span>

                    <span class="pf-expanded">
                        <?php if ($count) : ?>
                            <span class="pf-count"><?php echo esc_html($count); ?></span>
                        <?php endif; ?>
                        <span class="pf-name"><?php echo esc_html($term->name); ?></span>
                        <?php if ($short) : ?>
                            <span class="pf-desc"><?php echo esc_html($short); ?></span>
                        <?php endif; ?>
                        <?php if ($btn_label) : ?>
                            <span class="pf-btn"><?php echo esc_html($btn_label); ?></span>
                        <?php endif; ?>
                    </span>
                </a>
            <?php endforeach; ?>
        </div>

    </div>
</section>