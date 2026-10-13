<?php

/**
 * Секция "Калькулятор кухни" (короткая версия, по макету).
 * Цена считается на сервере: при загрузке показывается цена по умолчанию (видна без JS),
 * при выборе скрипт запрашивает новую цену у /wp-json/tm-calc/v1/estimate.
 * Поля секции в админке (все необязательные): eyebrow, title, note, button_text, button_url.
 */
defined('ABSPATH') || exit;

if (! function_exists('tm_calc_compute')) {
    return;
}

$eyebrow     = get_sub_field('eyebrow') ?: tm_t('Калькулятор вартості кухні', 'Калькулятор стоимости кухни');
$title       = get_sub_field('title') ?: tm_t('Скільки коштуватиме ваша кухня', 'Сколько будет стоить ваша кухня');
$note        = get_sub_field('note') ?: tm_t(
    'Додайте фурнітуру, стільницю та підсвітку у повному калькуляторі.',
    'Добавьте фурнитуру, столешницу и подсветку в полном калькуляторе.'
);
$button_text = get_sub_field('button_text') ?: tm_t('Повний розрахунок →', 'Полный расчёт →');
$button_url  = get_sub_field('button_url');
if (! $button_url) {
    // Страница калькулятора: находим украинскую по адресу, на русской берём её перевод (Polylang).
    $calc_page = get_page_by_path('calculator');
    if ($calc_page && function_exists('pll_get_post')) {
        $translated = pll_get_post($calc_page->ID);
        $calc_page  = $translated ? get_post($translated) : $calc_page;
    }
    $button_url = $calc_page ? get_permalink($calc_page) : home_url(tm_t('/calculator/', '/ru/kalkulyator/'));
}

$defaults = array('shape' => 'corner', 'length' => 4, 'facade' => 'standard');
$result   = tm_calc_compute($defaults);

$shapes = array(
    'straight' => tm_t('Пряма', 'Прямая'),
    'corner'   => tm_t('Кутова', 'Угловая'),
    'u'        => tm_t('П-подібна', 'П-образная'),
    'island'   => tm_t('З островом', 'С островом'),
);

$facades = array(
    'basic'    => tm_t('Базовий', 'Базовый'),
    'standard' => tm_t('Стандарт', 'Стандарт'),
    'premium'  => tm_t('Преміум', 'Премиум'),
);

$fmt = static function (int $n): string {
    return number_format($n, 0, ',', "\u{00A0}");
};
?>
<section class="calc" id="calculator">
    <div class="calc__box">

        <form class="calc__form" data-calc
            data-endpoint="<?php echo esc_url(rest_url('tm-calc/v1/estimate')); ?>"
            data-err-busy="<?php echo esc_attr(tm_t('Забагато запитів. Спробуйте за хвилину.', 'Слишком много запросов. Попробуйте через минуту.')); ?>"
            data-err-fail="<?php echo esc_attr(tm_t('Не вдалося оновити ціну. Спробуйте ще раз.', 'Не удалось обновить цену. Попробуйте ещё раз.')); ?>"
            onsubmit="return false">

            <div class="calc__eyebrow"><?php echo esc_html($eyebrow); ?></div>
            <h2 class="calc__title"><?php echo esc_html($title); ?></h2>

            <fieldset class="calc__group">
                <legend class="calc__label"><?php echo esc_html(tm_t('Форма', 'Форма')); ?></legend>
                <div class="calc__chips">
                    <?php foreach ($shapes as $value => $label) : ?>
                        <label class="calc__chip">
                            <input type="radio" name="shape" value="<?php echo esc_attr($value); ?>" <?php checked($value, $defaults['shape']); ?>>
                            <span><?php echo esc_html($label); ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </fieldset>

            <div class="calc__group">
                <div class="calc__len-row">
                    <label class="calc__label" for="calc-length"><?php echo esc_html(tm_t('Довжина', 'Длина')); ?></label>
                    <span class="calc__len"><span data-calc-length-out><?php echo esc_html(number_format($defaults['length'], 1, ',', '')); ?></span> <?php echo esc_html(tm_t('м', 'м')); ?></span>
                </div>
                <input class="calc__range" id="calc-length" type="range" name="length" min="2" max="9" step="0.5" value="<?php echo esc_attr($defaults['length']); ?>">
            </div>

            <fieldset class="calc__group">
                <legend class="calc__label"><?php echo esc_html(tm_t('Фасади', 'Фасады')); ?></legend>
                <div class="calc__chips">
                    <?php foreach ($facades as $value => $label) : ?>
                        <label class="calc__chip">
                            <input type="radio" name="facade" value="<?php echo esc_attr($value); ?>" <?php checked($value, $defaults['facade']); ?>>
                            <span><?php echo esc_html($label); ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </fieldset>

        </form>

        <aside class="calc__result" aria-live="polite">
            <div class="calc__result-label"><?php echo esc_html(tm_t('Орієнтовна вартість', 'Ориентировочная стоимость')); ?></div>
            <div class="calc__price" data-calc-price><?php echo esc_html($fmt($result['min']) . ' – ' . $fmt($result['max']) . ' ' . tm_t('грн', 'грн')); ?></div>
            <p class="calc__note"><?php echo esc_html($note); ?></p>
            <a class="calc__cta" href="<?php echo esc_url($button_url); ?>"><?php echo esc_html($button_text); ?></a>
            <div class="calc__error" data-calc-error hidden></div>
        </aside>

    </div>
</section>