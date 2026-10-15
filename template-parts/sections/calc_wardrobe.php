<?php

/**
 * Секция "Калькулятор шкафа (полная страница)".
 * Цена считается на сервере: при загрузке PHP показывает цену по умолчанию (видна без JS),
 * при выборе скрипт запрашивает новую цену у /wp-json/tm-calc/v1/wardrobe.
 * Поля секции в админке (все необязательные): eyebrow, title, intro, form_title.
 * Классы те же, что у кухонного калькулятора, стили общие.
 */
defined('ABSPATH') || exit;

if (! function_exists('tm_wrd_compute')) {
    return;
}

$eyebrow    = get_sub_field('eyebrow') ?: tm_t('Онлайн-розрахунок', 'Онлайн-расчёт');
$title      = get_sub_field('title') ?: tm_t('Калькулятор вартості шафи', 'Калькулятор стоимости шкафа');
$intro      = get_sub_field('intro') ?: tm_t(
    'Чотири питання — і побачите орієнтовну ціну шафи під ключ. Кількість дверей, полиць і штанг рахуємо самі.',
    'Четыре вопроса — и увидите ориентировочную цену шкафа под ключ. Количество дверей, полок и штанг считаем сами.'
);
$form_title = get_sub_field('form_title') ?: tm_t('Отримайте точний розрахунок', 'Получите точный расчёт');
$home       = function_exists('pll_home_url') ? pll_home_url() : home_url('/');

$types = array(
    'free'  => array(
        tm_t('Корпусна', 'Корпусный'),
        tm_t('Зі своїми боками, дном і дахом', 'Со своими боками, дном и крышей'),
        '<rect x="14" y="4" width="36" height="34" rx="2" fill="none" stroke="#4A2E1E" stroke-width="2.5"></rect><line x1="32" y1="6" x2="32" y2="36" stroke="#C9A063" stroke-width="1.5"></line><line x1="18" y1="38" x2="18" y2="42" stroke="#4A2E1E" stroke-width="2"></line><line x1="46" y1="38" x2="46" y2="42" stroke="#4A2E1E" stroke-width="2"></line>',
        tm_t('Схема корпусної шафи: окремий корпус на ніжках', 'Схема корпусного шкафа: отдельный корпус на ножках'),
    ),
    'built' => array(
        tm_t('Вбудована', 'Встроенный'),
        tm_t('Кріпиться до стін і стелі, дешевша', 'Крепится к стенам и потолку, дешевле'),
        '<rect x="2" y="2" width="8" height="40" fill="#E8DCC8"></rect><rect x="54" y="2" width="8" height="40" fill="#E8DCC8"></rect><rect x="2" y="2" width="60" height="4" fill="#E8DCC8"></rect><rect x="11" y="7" width="20" height="35" rx="1.5" fill="#4A2E1E"></rect><rect x="32" y="7" width="21" height="35" rx="1.5" fill="#4A2E1E" opacity="0.75"></rect>',
        tm_t('Схема вбудованої шафи: двері між стінами', 'Схема встроенного шкафа: двери между стенами'),
    ),
);

$heights = array(
    '2.2' => array(tm_t('2,2 м', '2,2 м'), tm_t('Стандарт', 'Стандарт')),
    '2.5' => array(tm_t('2,5 м', '2,5 м'), tm_t('Найпопулярніша', 'Самая популярная')),
    '2.8' => array(tm_t('До стелі', 'До потолка'), tm_t('2,8 м', '2,8 м')),
);

$depths = array(
    '45' => array(tm_t('45 см', '45 см'), tm_t('Для одягу на плічках упоперек', 'Для одежды на плечиках поперёк')),
    '60' => array(tm_t('60 см', '60 см'), tm_t('Стандарт', 'Стандарт')),
);

$systems = array(
    'slide' => array(
        tm_t('Купе', 'Купе'),
        tm_t('Розсувні, не займають місця', 'Раздвижные, не занимают места'),
        '<rect x="6" y="4" width="52" height="36" rx="2" fill="none" stroke="#C9B9A2" stroke-width="1.5"></rect><rect x="9" y="7" width="26" height="30" rx="1.5" fill="#4A2E1E"></rect><rect x="29" y="9" width="26" height="30" rx="1.5" fill="#C9A063"></rect><path d="M18 22 h10 m-3 -3 l3 3 l-3 3" fill="none" stroke="#FBF4E9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>',
        tm_t('Схема розсувних дверей купе', 'Схема раздвижных дверей купе'),
    ),
    'hinge' => array(
        tm_t('Розпашні', 'Распашные'),
        tm_t('Дешевші, потрібне місце перед шафою', 'Дешевле, нужно место перед шкафом'),
        '<rect x="6" y="4" width="52" height="36" rx="2" fill="none" stroke="#C9B9A2" stroke-width="1.5"></rect><rect x="9" y="7" width="22" height="30" rx="1.5" fill="#4A2E1E"></rect><path d="M34 7 L52 12 L52 34 L34 37 Z" fill="#C9A063"></path>',
        tm_t('Схема розпашних дверей', 'Схема распашных дверей'),
    ),
);

$fills = array(
    'dsp'    => array(tm_t('ДСП', 'ДСП'), tm_t('Під колір корпусу', 'Под цвет корпуса'), 'repeating-linear-gradient(92deg, #D9C3A2 0 6px, #CDB48F 6px 9px, #E0CCAE 9px 15px)', tm_t('Зразок дверей з ДСП під дерево', 'Образец дверей из ДСП под дерево')),
    'mirror' => array(tm_t('Дзеркало', 'Зеркало'), tm_t('Візуально більше простору', 'Визуально больше пространства'), 'linear-gradient(115deg, #DCE3E6 0%, #F4F7F8 38%, #C9D2D6 52%, #E9EEF0 100%)', tm_t('Зразок дзеркальних дверей', 'Образец зеркальных дверей')),
    'combi'  => array(tm_t('Комбі', 'Комби'), tm_t('Дзеркало плюс ДСП', 'Зеркало плюс ДСП'), 'linear-gradient(180deg, #D9C3A2 0 34%, #8A7A66 34% 36%, #E6ECEE 36% 64%, #8A7A66 64% 66%, #D9C3A2 66% 100%)', tm_t('Зразок комбінованих дверей: дзеркало та ДСП', 'Образец комбинированных дверей: зеркало и ДСП')),
    'satin'  => array(tm_t('Сатин', 'Сатин'), tm_t('Матове скло, без відбитків', 'Матовое стекло, без отпечатков'), 'linear-gradient(135deg, #E9ECEA 0%, #D7DCD9 100%)', tm_t('Зразок дверей з матового сатинового скла', 'Образец дверей из матового сатинового стекла')),
);

$interiors = array(
    'basic' => array(tm_t('Базове', 'Базовое'), tm_t('Полиці та штанга', 'Полки и штанга')),
    'std'   => array(tm_t('Стандарт', 'Стандарт'), tm_t('Плюс висувні ящики', 'Плюс выдвижные ящики')),
    'full'  => array(tm_t('Повне', 'Полное'), tm_t('Плюс пантограф, полиця для взуття, вішалка', 'Плюс пантограф, полка для обуви, вешалка')),
);

$defaults = array(
    'type'     => 'free',
    'width'    => 2.4,
    'height'   => 2.5,
    'depth'    => 60,
    'sys'      => 'slide',
    'fill'     => 'mirror',
    'interior' => 'std',
);
$result = tm_wrd_compute($defaults);

$fmt = static function (int $n): string {
    return number_format($n, 0, ',', "\u{00A0}");
};
$m = tm_t('м', 'м');
?>

<section class="cl-hero">
    <div class="cl-inner">
        <?php if (function_exists('rank_math_the_breadcrumbs')) : ?>
            <?php rank_math_the_breadcrumbs(); ?>
        <?php else : ?>
            <nav class="crumbs" aria-label="<?php echo esc_attr(tm_t('Навігаційний ланцюжок', 'Навигационная цепочка')); ?>">
                <a href="<?php echo esc_url($home); ?>"><?php echo esc_html(tm_t('Головна', 'Главная')); ?></a>
                <span aria-hidden="true">/</span>
                <span aria-current="page"><?php echo esc_html(tm_t('Калькулятор шафи', 'Калькулятор шкафа')); ?></span>
            </nav>
        <?php endif; ?>

        <div class="cl-hero__text">
            <div class="cl-eyebrow"><?php echo esc_html($eyebrow); ?></div>
            <h1 class="cl-h1"><?php echo esc_html($title); ?></h1>
            <p class="cl-intro"><?php echo esc_html($intro); ?></p>
        </div>
    </div>
</section>

<section class="cl-main">
    <form class="cl-layout" data-calc-wrd
        data-endpoint="<?php echo esc_url(rest_url('tm-calc/v1/wardrobe')); ?>"
        data-per="<?php echo esc_attr(tm_t('грн за погонний метр', 'грн за погонный метр')); ?>"
        data-doors="<?php echo esc_attr(tm_t('Дверей:', 'Дверей:')); ?>"
        data-err-busy="<?php echo esc_attr(tm_t('Забагато запитів. Спробуйте за хвилину.', 'Слишком много запросов. Попробуйте через минуту.')); ?>"
        data-err-fail="<?php echo esc_attr(tm_t('Не вдалося оновити ціну. Спробуйте ще раз.', 'Не удалось обновить цену. Попробуйте ещё раз.')); ?>"
        data-lead="<?php echo esc_url(rest_url('tm-calc/v1/wardrobe-lead')); ?>"
        data-err-phone="<?php echo esc_attr(tm_t('Перевірте номер телефону.', 'Проверьте номер телефона.')); ?>"
        data-err-send="<?php echo esc_attr(tm_t('Не вдалося надіслати. Зателефонуйте нам, будь ласка.', 'Не удалось отправить. Позвоните нам, пожалуйста.')); ?>"
        onsubmit="return false">

        <div class="cl-steps">

            <div class="cl-step">
                <div class="cl-step-head"><span class="cl-num">1</span>
                    <h2 class="cl-step-title"><?php echo esc_html(tm_t('Тип шафи', 'Тип шкафа')); ?></h2>
                </div>
                <div class="cl-opts cl-opts--2" role="radiogroup">
                    <?php foreach ($types as $value => $o) : ?>
                        <label class="cl-pick">
                            <input type="radio" name="type" value="<?php echo esc_attr($value); ?>" data-label="<?php echo esc_attr($o[0]); ?>" <?php checked($value, $defaults['type']); ?>>
                            <span class="cl-opt">
                                <svg class="cl-ico" viewBox="0 0 64 44" role="img" aria-label="<?php echo esc_attr($o[3]); ?>"><?php echo $o[2]; // phpcs:ignore WordPress.Security.EscapeOutput -- константа выше 
                                                                                                                                ?></svg>
                                <span class="cl-opt-title"><?php echo esc_html($o[0]); ?></span>
                                <span class="cl-opt-sub"><?php echo esc_html($o[1]); ?></span>
                            </span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="cl-step">
                <div class="cl-step-head"><span class="cl-num">2</span>
                    <h2 class="cl-step-title"><?php echo esc_html(tm_t('Розміри', 'Размеры')); ?></h2>
                </div>
                <div class="cl-len-row">
                    <label class="cl-len-hint" for="cl-width"><?php echo esc_html(tm_t('Ширина шафи', 'Ширина шкафа')); ?></label>
                    <span class="cl-len"><span data-calc-width-out><?php echo esc_html(number_format($defaults['width'], 1, ',', '')); ?></span> <?php echo esc_html($m); ?></span>
                </div>
                <input class="cl-range" id="cl-width" type="range" name="width" min="1" max="4" step="0.1" value="<?php echo esc_attr($defaults['width']); ?>">
                <div class="cl-scale"><span>1,0 <?php echo esc_html($m); ?></span><span>4,0 <?php echo esc_html($m); ?></span></div>
                <div class="cl-hint"><?php echo esc_html(tm_t('Дверей:', 'Дверей:')); ?> <b data-calc-doors><?php echo esc_html($result['doors']); ?></b> <span><?php echo esc_html(tm_t('рахуємо самі за шириною', 'считаем сами по ширине')); ?></span></div>

                <div class="cl-sub">
                    <div class="cl-sub-label"><?php echo esc_html(tm_t('Висота', 'Высота')); ?></div>
                    <div class="cl-opts cl-opts--3" role="radiogroup">
                        <?php foreach ($heights as $value => $o) : ?>
                            <label class="cl-pick">
                                <input type="radio" name="height" value="<?php echo esc_attr($value); ?>" data-label="<?php echo esc_attr($o[0]); ?>" <?php checked((float) $value, $defaults['height']); ?>>
                                <span class="cl-opt">
                                    <span class="cl-opt-title"><?php echo esc_html($o[0]); ?></span>
                                    <span class="cl-opt-sub"><?php echo esc_html($o[1]); ?></span>
                                </span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="cl-sub">
                    <div class="cl-sub-label"><?php echo esc_html(tm_t('Глибина', 'Глубина')); ?></div>
                    <div class="cl-opts cl-opts--2" role="radiogroup">
                        <?php foreach ($depths as $value => $o) : ?>
                            <label class="cl-pick">
                                <input type="radio" name="depth" value="<?php echo esc_attr($value); ?>" data-label="<?php echo esc_attr($o[0]); ?>" <?php checked((int) $value, $defaults['depth']); ?>>
                                <span class="cl-opt">
                                    <span class="cl-opt-title"><?php echo esc_html($o[0]); ?></span>
                                    <span class="cl-opt-sub"><?php echo esc_html($o[1]); ?></span>
                                </span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="cl-step">
                <div class="cl-step-head"><span class="cl-num">3</span>
                    <h2 class="cl-step-title"><?php echo esc_html(tm_t('Двері', 'Двери')); ?></h2>
                </div>
                <div class="cl-opts cl-opts--2" role="radiogroup">
                    <?php foreach ($systems as $value => $o) : ?>
                        <label class="cl-pick">
                            <input type="radio" name="sys" value="<?php echo esc_attr($value); ?>" data-label="<?php echo esc_attr($o[0]); ?>" <?php checked($value, $defaults['sys']); ?>>
                            <span class="cl-opt">
                                <svg class="cl-ico" viewBox="0 0 64 44" role="img" aria-label="<?php echo esc_attr($o[3]); ?>"><?php echo $o[2]; // phpcs:ignore WordPress.Security.EscapeOutput -- константа выше 
                                                                                                                                ?></svg>
                                <span class="cl-opt-title"><?php echo esc_html($o[0]); ?></span>
                                <span class="cl-opt-sub"><?php echo esc_html($o[1]); ?></span>
                            </span>
                        </label>
                    <?php endforeach; ?>
                </div>

                <div class="cl-sub">
                    <div class="cl-sub-label"><?php echo esc_html(tm_t('Наповнення дверей', 'Наполнение дверей')); ?></div>
                    <div class="cl-opts" role="radiogroup">
                        <?php foreach ($fills as $value => $o) : ?>
                            <label class="cl-pick">
                                <input type="radio" name="fill" value="<?php echo esc_attr($value); ?>" data-label="<?php echo esc_attr($o[0]); ?>" <?php checked($value, $defaults['fill']); ?>>
                                <span class="cl-opt">
                                    <span class="cl-sw" role="img" aria-label="<?php echo esc_attr($o[3]); ?>" style="background: <?php echo esc_attr($o[2]); ?>"></span>
                                    <span class="cl-opt-title"><?php echo esc_html($o[0]); ?></span>
                                    <span class="cl-opt-sub"><?php echo esc_html($o[1]); ?></span>
                                </span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="cl-step">
                <div class="cl-step-head"><span class="cl-num">4</span>
                    <h2 class="cl-step-title"><?php echo esc_html(tm_t('Наповнення', 'Наполнение')); ?></h2>
                </div>
                <div class="cl-opts cl-opts--3" role="radiogroup">
                    <?php foreach ($interiors as $value => $o) : ?>
                        <label class="cl-pick">
                            <input type="radio" name="interior" value="<?php echo esc_attr($value); ?>" data-label="<?php echo esc_attr($o[0]); ?>" <?php checked($value, $defaults['interior']); ?>>
                            <span class="cl-opt">
                                <span class="cl-opt-title"><?php echo esc_html($o[0]); ?></span>
                                <span class="cl-opt-sub"><?php echo esc_html($o[1]); ?></span>
                            </span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

        </div>

        <aside class="cl-summary" aria-live="polite">
            <div class="cl-card">
                <div class="cl-card-label"><?php echo esc_html(tm_t('Орієнтовна вартість', 'Ориентировочная стоимость')); ?></div>
                <div class="cl-price" data-calc-price><?php echo esc_html($fmt($result['min']) . ' – ' . $fmt($result['max']) . ' ' . tm_t('грн', 'грн')); ?></div>
                <div class="cl-per" data-calc-per><?php echo esc_html('≈ ' . $fmt((int) (round($result['per_meter'] / 1000) * 1000)) . ' ' . tm_t('грн за погонний метр', 'грн за погонный метр')); ?></div>
                <!-- <div class="cl-turnkey"><?php echo esc_html(tm_t('Ціна під ключ: матеріали, фурнітура, доставка, встановлення', 'Цена под ключ: материалы, фурнитура, доставка, установка')); ?></div> -->

                <div class="cl-rows">
                    <div class="cl-row"><span><?php echo esc_html(tm_t('Тип', 'Тип')); ?></span><b data-sum="type"><?php echo esc_html($types[$defaults['type']][0]); ?></b></div>
                    <div class="cl-row"><span><?php echo esc_html(tm_t('Розміри', 'Размеры')); ?></span><b data-sum="size"><?php echo esc_html(number_format($defaults['width'], 1, ',', '') . ' × ' . number_format($defaults['height'], 1, ',', '') . ' × ' . number_format($defaults['depth'] / 100, 1, ',', '') . ' ' . $m); ?></b></div>
                    <div class="cl-row"><span><?php echo esc_html(tm_t('Двері', 'Двери')); ?></span><b data-sum="sys"><?php echo esc_html($systems[$defaults['sys']][0]); ?></b></div>
                    <div class="cl-row"><span><?php echo esc_html(tm_t('Наповнення дверей', 'Наполнение дверей')); ?></span><b data-sum="fill"><?php echo esc_html($fills[$defaults['fill']][0]); ?></b></div>
                    <div class="cl-row"><span><?php echo esc_html(tm_t('Всередині', 'Внутри')); ?></span><b data-sum="interior"><?php echo esc_html($interiors[$defaults['interior']][0]); ?></b></div>
                </div>

                <?php if (function_exists('tm_wrd_send_lead')) : ?>
                    <div class="cl-lead" data-calc-lead>
                        <div class="cl-lead-title"><?php echo esc_html($form_title); ?></div>
                        <input class="cl-input" type="text" name="lead_name" autocomplete="name" placeholder="<?php echo esc_attr(tm_t('Ваше ім’я', 'Ваше имя')); ?>">
                        <input class="cl-input" type="tel" name="lead_phone" autocomplete="tel" placeholder="<?php echo esc_attr(tm_t('Телефон', 'Телефон')); ?>" required>
                        <input class="cl-hp" type="text" name="lead_site" tabindex="-1" autocomplete="off" aria-hidden="true">
                        <button type="button" class="cl-send" data-calc-send><?php echo esc_html(tm_t('Надіслати параметри', 'Отправить параметры')); ?></button>
                        <div class="cl-fine"><?php echo esc_html(tm_t('Ціна орієнтовна. Остаточна вартість — після замірів і погодження проєкту.', 'Цена ориентировочная. Окончательная стоимость — после замеров и согласования проекта.')); ?></div>
                    </div>
                    <div class="cl-thanks" data-calc-thanks hidden>
                        <div class="cl-thanks-ico">✓</div>
                        <div class="cl-thanks-title"><?php echo esc_html(tm_t('Дякуємо!', 'Спасибо!')); ?></div>
                        <div class="cl-thanks-text"><?php echo esc_html(tm_t('Ми отримали параметри вашої шафи і зателефонуємо, щоб уточнити деталі.', 'Мы получили параметры вашего шкафа и позвоним, чтобы уточнить детали.')); ?></div>
                    </div>
                <?php else : ?>
                    <div class="cl-fine"><?php echo esc_html(tm_t('Ціна орієнтовна. Остаточна вартість — після замірів і погодження проєкту.', 'Цена ориентировочная. Окончательная стоимость — после замеров и погодження проєкту.')); ?></div>
                <?php endif; ?>

                <div class="cl-error" data-calc-error hidden></div>
            </div>
        </aside>

    </form>
</section>