<?php

/**
 * Секция "Таблица цен": текст и ссылки слева, таблица справа.
 * Колонок 2 или 3: третья выводится, только если заполнен заголовок head_3.
 * Первая ячейка строки это заголовок строки (th scope="row").
 */
defined('ABSPATH') || exit;

$eyebrow = get_sub_field('eyebrow');
$title   = get_sub_field('title');
$text    = get_sub_field('text');
$link_1  = get_sub_field('btn_primary');
$link_2  = get_sub_field('btn_secondary');
$rows    = get_sub_field('rows') ?: array();

$cols  = '' !== trim((string) get_sub_field('head_3')) ? 3 : 2;
$heads = array();
for ($i = 1; $i <= $cols; $i++) {
    $heads[] = (string) get_sub_field('head_' . $i);
}

// Строки без первой ячейки не выводим.
$rows = array_filter($rows, fn($row) => '' !== trim((string) ($row['c1'] ?? '')));

if (! $rows) {
    return;
}

$links = tm_link($link_1, 'price-table__link') . tm_link($link_2, 'price-table__link');
?>
<section class="price-table">
    <div class="price-table__inner">

        <div class="price-table__info">
            <?php if ($eyebrow) : ?>
                <div class="price-table__eyebrow"><?php echo esc_html($eyebrow); ?></div>
            <?php endif; ?>

            <?php if ($title) : ?>
                <h2 class="price-table__title"><?php echo esc_html($title); ?></h2>
            <?php endif; ?>

            <?php if ($text) : ?>
                <div class="price-table__text"><?php echo wp_kses_post(wpautop($text)); ?></div>
            <?php endif; ?>

            <?php if ($links) : ?>
                <div class="price-table__links"><?php echo $links; // tm_link() уже экранирует 
                                                ?></div>
            <?php endif; ?>
        </div>

        <div class="price-table__wrap" role="region" tabindex="0"
            aria-label="<?php echo esc_attr($title ?: tm_t('Таблиця цін', 'Таблица цен')); ?>">
            <table class="price-table__table price-table__table--<?php echo (int) $cols; ?>">
                <thead>
                    <tr>
                        <?php foreach ($heads as $head) : ?>
                            <th scope="col"><?php echo esc_html($head); ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $row) : ?>
                        <tr>
                            <th scope="row"><?php echo esc_html($row['c1']); ?></th>
                            <?php for ($i = 2; $i <= $cols; $i++) : ?>
                                <td><?php echo esc_html($row['c' . $i] ?? ''); ?></td>
                            <?php endfor; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </div>
</section>