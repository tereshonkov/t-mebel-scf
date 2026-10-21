<?php

/**
 * Секция "География": заголовок и текст слева, справа до двух списков-"чипсов"
 * (районы города и города области). Списки задаются textarea, по одному названию в строке,
 * как факторы в price_form.
 */
defined('ABSPATH') || exit;

$eyebrow = get_sub_field('eyebrow');
$title   = get_sub_field('title');
$text    = get_sub_field('text');

// Textarea -> массив строк: режем по переносам, убираем пробелы и пустые строки.
$groups = array();
for ($i = 1; $i <= 2; $i++) {
    $items = preg_split('/\R/u', (string) get_sub_field('items_' . $i));
    $items = array_values(array_filter(array_map('trim', $items)));
    if ($items) {
        $groups[] = array(
            'label' => (string) get_sub_field('label_' . $i),
            'items' => $items,
        );
    }
}

if (! $title && ! $groups) {
    return;
}
?>
<section class="geo">
    <div class="geo__inner">

        <div class="geo__info">
            <?php if ($eyebrow) : ?>
                <div class="geo__eyebrow"><?php echo esc_html($eyebrow); ?></div>
            <?php endif; ?>

            <?php if ($title) : ?>
                <h2 class="geo__title"><?php echo esc_html($title); ?></h2>
            <?php endif; ?>

            <?php if ($text) : ?>
                <div class="geo__text"><?php echo wp_kses_post(wpautop($text)); ?></div>
            <?php endif; ?>
        </div>

        <?php if ($groups) : ?>
            <div class="geo__groups">
                <?php foreach ($groups as $group) : ?>
                    <div class="geo__group">
                        <?php if ($group['label']) : ?>
                            <h3 class="geo__label"><?php echo esc_html($group['label']); ?></h3>
                        <?php endif; ?>
                        <ul class="geo__chips">
                            <?php foreach ($group['items'] as $item) : ?>
                                <li class="geo__chip"><?php echo esc_html($item); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>
</section>