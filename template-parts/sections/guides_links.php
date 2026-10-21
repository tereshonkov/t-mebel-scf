<?php

/**
 * Секция "Полезные статьи": та же разметка и стили, что "Читайте також" в статье.
 * Статьи выбираются в поле "items" (Связь). Если на русской странице выбрана
 * украинская статья, выводим её перевод (Polylang). Черновики и повторы пропускаем.
 */
defined('ABSPATH') || exit;

$title = get_sub_field('title') ?: tm_t('Корисно знати перед замовленням', 'Полезно знать перед заказом');
$ids   = get_sub_field('items') ?: array();

$items = array();
foreach ($ids as $id) {
    $id = (int) (is_object($id) ? $id->ID : $id);
    if ($id && function_exists('pll_get_post')) {
        $id = (int) (pll_get_post($id) ?: $id);
    }
    if ($id && 'publish' === get_post_status($id)) {
        $items[$id] = $id;
    }
}

if (! $items) {
    return;
}
?>
<section class="ar-related-sec">
    <div class="ar-related-wrap">
        <div class="ar-related-head">
            <h2 class="ar-related-title"><?php echo esc_html($title); ?></h2>
            <a class="ar-related-all" href="<?php echo esc_url(get_post_type_archive_link('guide')); ?>"><?php echo esc_html(tm_t('Усі статті →', 'Все статьи →')); ?></a>
        </div>

        <div class="ar-related">
            <?php foreach ($items as $id) : ?>
                <?php get_template_part('template-parts/guides/guide-card', null, array('id' => $id)); ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>