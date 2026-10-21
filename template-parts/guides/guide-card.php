<?php

/**
 * Карточка статьи: обложка, рубрика и время чтения, заголовок.
 * Используется в "Читайте також" (single-guide.php) и в секции guides_links.
 * Аргументы: id (ID статьи).
 */
defined('ABSPATH') || exit;

$id = (int) ($args['id'] ?? 0);
if (! $id) {
    return;
}

$cat = tm_guide_cat_name($id);
?>
<a class="ar-card" href="<?php echo esc_url(get_permalink($id)); ?>">
    <div class="ar-card__media">
        <?php
        // Картинка внутри ссылки с заголовком декоративная: alt пустой (правило 8 README).
        echo get_the_post_thumbnail($id, 'tm-card', array(
            'class'   => 'ar-card__img',
            'alt'     => '',
            'loading' => 'lazy',
            'sizes'   => '(max-width: 760px) 100vw, (max-width: 960px) 50vw, 380px',
        ));
        ?>
    </div>
    <div class="ar-card__meta">
        <?php echo esc_html(($cat ? $cat . ' · ' : '') . tm_read_time($id) . ' ' . tm_t('хв', 'мин')); ?>
    </div>
    <h3 class="ar-card__title"><?php echo esc_html(get_the_title($id)); ?></h3>
</a>