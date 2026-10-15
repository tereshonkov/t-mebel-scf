<?php

/**
 * Секция "Плитки форм кухни" для главной (по макету): фото-карточки,
 * каждая ведёт в калькулятор с уже выбранной формой (/calculator/?shape=corner).
 * Цена "від X грн" считается на сервере той же формулой, что и калькулятор:
 * кухня 4 м с базовыми фасадами, то есть самая низкая цена для этой формы.
 *
 * Поля секции в админке (все необязательные):
 *   eyebrow, title, text и повторитель tiles: в каждой строке shape (форма) и image (фото).
 * Если строк нет, показываются все четыре формы без фото.
 */
defined('ABSPATH') || exit;

if (! function_exists('tm_calc_compute')) {
    return;
}

$eyebrow = get_sub_field('eyebrow') ?: tm_t('Калькулятор', 'Калькулятор');
$title   = get_sub_field('title') ?: tm_t('Скільки коштуватиме ваша кухня?', 'Сколько будет стоить ваша кухня?');
$text    = get_sub_field('text') ?: tm_t(
    'Оберіть форму, і за хвилину отримаєте орієнтовну ціну під ваші розміри та матеріали.',
    'Выберите форму, и за минуту получите ориентировочную цену под ваши размеры и материалы.'
);

$calc_url = tm_calc_page_url();

$labels = array(
    'straight' => tm_t('Пряма', 'Прямая'),
    'corner'   => tm_t('Кутова', 'Угловая'),
    'u'        => tm_t('П-подібна', 'П-образная'),
    'island'   => tm_t('З островом', 'С островом'),
);

// Карточки из повторителя (порядок задаёт редактор).
$tiles = array();
if (function_exists('have_rows') && have_rows('tiles')) {
    while (have_rows('tiles')) {
        the_row();
        $shape = (string) get_sub_field('shape');
        if (isset($labels[$shape])) {
            $tiles[] = array('shape' => $shape, 'image' => get_sub_field('image'));
        }
    }
}
if (! $tiles) {
    foreach ($labels as $shape => $label) {
        $tiles[] = array('shape' => $shape, 'image' => null);
    }
}

$from = tm_t('від', 'от');
$cur  = tm_t('грн', 'грн');
$go   = tm_t('Розрахувати →', 'Рассчитать →');
?>
<section class="ct">
    <div class="ct__inner">

        <div class="ct__head">
            <div>
                <div class="ct__eyebrow"><?php echo esc_html($eyebrow); ?></div>
                <h2 class="ct__title"><?php echo esc_html($title); ?></h2>
            </div>
            <p class="ct__text"><?php echo esc_html($text); ?></p>
        </div>

        <div class="ct__grid">
            <?php foreach ($tiles as $tile) : ?>
                <?php
                $shape  = $tile['shape'];
                $label  = $labels[$shape];
                $image  = $tile['image'];
                $result = tm_calc_compute(array('shape' => $shape, 'length' => 4, 'facade' => 'basic'));
                $price  = $from . ' ' . number_format($result['min'], 0, ',', "\u{00A0}") . ' ' . $cur;
                ?>
                <a class="ct__card" href="<?php echo esc_url(add_query_arg('shape', $shape, $calc_url)); ?>">
                    <?php echo tm_image($image, 'large', array('class' => 'ct__img', 'loading' => 'lazy')); // phpcs:ignore WordPress.Security.EscapeOutput 
                    ?>
                    <span class="ct__scrim"></span>
                    <span class="ct__body">
                        <span class="ct__name"><?php echo esc_html($label); ?></span>
                        <span class="ct__price"><?php echo esc_html($price); ?></span>
                        <span class="ct__go"><?php echo esc_html($go); ?></span>
                    </span>
                </a>
            <?php endforeach; ?>
        </div>

    </div>
</section>