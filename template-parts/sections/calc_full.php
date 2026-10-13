<?php

/**
 * Секция "Калькулятор кухни (полная страница)", по макету.
 * Цена считается на сервере: при загрузке PHP показывает цену по умолчанию (видна без JS),
 * при выборе скрипт запрашивает новую цену у /wp-json/tm-calc/v1/estimate.
 * Поля секции в админке (все необязательные): eyebrow, title, intro, form_title.
 * Форма заявки появится сама, когда будет подключена функция tm_calc_send_lead (следующий шаг).
 */
defined('ABSPATH') || exit;

if (! function_exists('tm_calc_compute')) {
    return;
}

$eyebrow    = get_sub_field('eyebrow') ?: tm_t('Онлайн-розрахунок', 'Онлайн-расчёт');
$title      = get_sub_field('title') ?: tm_t('Калькулятор вартості кухні', 'Калькулятор стоимости кухни');
$intro      = get_sub_field('intro') ?: tm_t(
    'Оберіть параметри — і побачите орієнтовну ціну кухні на замовлення. Точну вартість розрахуємо безкоштовно після замірів.',
    'Выберите параметры — и увидите ориентировочную цену кухни на заказ. Точную стоимость рассчитаем бесплатно после замеров.'
);
$form_title = get_sub_field('form_title') ?: tm_t('Отримайте точний розрахунок', 'Получите точный расчёт');
$home       = function_exists('pll_home_url') ? pll_home_url() : home_url('/');

$ico_frame = '<rect x="2" y="2" width="60" height="40" rx="3" fill="none" stroke="#C9B9A2" stroke-width="1.5" stroke-dasharray="3 3"></rect><rect x="6" y="6" width="52" height="9" rx="1.5" fill="#4A2E1E"></rect>';

$shapes = array(
    'straight' => array(tm_t('Пряма', 'Прямая'), tm_t('Вздовж однієї стіни', 'Вдоль одной стены'), $ico_frame, tm_t('Схема прямої кухні, вид зверху', 'Схема прямой кухни, вид сверху')),
    'corner'   => array(tm_t('Кутова', 'Угловая'), tm_t('Дві стіни під кутом', 'Две стены под углом'), $ico_frame . '<rect x="6" y="6" width="9" height="32" rx="1.5" fill="#4A2E1E"></rect>', tm_t('Схема кутової кухні, вид зверху', 'Схема угловой кухни, вид сверху')),
    'u'        => array(tm_t('П-подібна', 'П-образная'), tm_t('Уздовж трьох стін', 'Вдоль трёх стен'), $ico_frame . '<rect x="6" y="6" width="9" height="32" rx="1.5" fill="#4A2E1E"></rect><rect x="49" y="6" width="9" height="32" rx="1.5" fill="#4A2E1E"></rect>', tm_t('Схема П-подібної кухні, вид зверху', 'Схема П-образной кухни, вид сверху')),
    'island'   => array(tm_t('З островом', 'С островом'), tm_t('Окремий модуль в центрі', 'Отдельный модуль в центре'), $ico_frame . '<rect x="20" y="25" width="24" height="10" rx="1.5" fill="#C9A063"></rect>', tm_t('Схема кухні з островом, вид зверху', 'Схема кухни с островом, вид сверху')),
);

$facades = array(
    'basic'    => array(tm_t('Базовий', 'Базовый'), tm_t('МДФ у плівці, економно', 'МДФ в плёнке, экономно'), 'linear-gradient(135deg, #EDE6DA 0%, #E2D9CA 100%)', tm_t('Зразок фасаду МДФ у плівці, світлий матовий', 'Образец фасада МДФ в плёнке, светлый матовый')),
    'standard' => array(tm_t('Стандарт', 'Стандарт'), tm_t('МДФ, стандартні декори', 'МДФ, стандартные декоры'), 'linear-gradient(135deg, #9AA593 0%, #87937F 100%)', tm_t('Зразок фасаду МДФ, шавлієвий мат', 'Образец фасада МДФ, шалфейный мат')),
    'premium'  => array(tm_t('Преміум', 'Премиум'), tm_t('Глянець, металік', 'Глянец, металлик'), 'linear-gradient(135deg, #5A4A3E 0%, #2F2620 55%, #4A3B30 100%)', tm_t('Зразок преміального глянцевого фасаду, темний', 'Образец премиального глянцевого фасада, тёмный')),
);

$tops = array(
    'dsp'      => array(tm_t('ДСП', 'ДСП'), tm_t('Базова плита', 'Базовая плита'), 'repeating-linear-gradient(90deg, #C8A87E 0 7px, #BC9B70 7px 10px, #D2B48B 10px 17px)', tm_t('Зразок стільниці з ДСП під дерево', 'Образец столешницы из ДСП под дерево')),
    'arpa'     => array('ARPA', tm_t('Вологостійка', 'Влагостойкая'), 'linear-gradient(160deg, #E8E2D8 0%, #D8D0C2 100%)', tm_t('Зразок стільниці ARPA, світлий', 'Образец столешницы ARPA, светлый')),
    'textured' => array(tm_t('Фактурна', 'Фактурная'), tm_t('Торець 90°', 'Торец 90°'), 'radial-gradient(circle at 20% 30%, #B9AFA2 0 1.5px, transparent 2px) 0 0 / 14px 14px, radial-gradient(circle at 70% 60%, #CFC6BA 0 1px, transparent 1.5px) 0 0 / 10px 10px, #F2EEE8', tm_t('Зразок фактурної стільниці, світлий з вкрапленнями', 'Образец фактурной столешницы, светлый с вкраплениями')),
    'compact'  => array(tm_t('Компакт-плита', 'Компакт-плита'), tm_t('Тонка, 12 мм', 'Тонкая, 12 мм'), 'linear-gradient(135deg, #4A4A48 0%, #3A3A38 100%)', tm_t('Зразок стільниці з компакт-плити, антрацит', 'Образец столешницы из компакт-плиты, антрацит')),
);

$hardware = array(
    'standard' => array(tm_t('Стандарт', 'Стандарт'), tm_t('Надійні базові механізми', 'Надёжные базовые механизмы')),
    'premium'  => array(tm_t('Німецька', 'Немецкая'), tm_t('Плавне закривання, преміальний клас', 'Плавное закрывание, премиальный класс')),
);

// Значения по умолчанию. Форму можно задать ссылкой: /calculator/?shape=island
$shape_get = isset($_GET['shape']) ? sanitize_key(wp_unslash($_GET['shape'])) : ''; // phpcs:ignore WordPress.Security.NonceVerification
$defaults  = array(
    'shape'    => isset($shapes[$shape_get]) ? $shape_get : 'corner',
    'length'   => 4,
    'facade'   => 'standard',
    'top'      => 'arpa',
    'hardware' => 'standard',
);
$result = tm_calc_compute($defaults);

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
                <span aria-current="page"><?php echo esc_html(tm_t('Калькулятор', 'Калькулятор')); ?></span>
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
    <form class="cl-layout" data-calc-full
        data-endpoint="<?php echo esc_url(rest_url('tm-calc/v1/estimate')); ?>"
        data-per="<?php echo esc_attr(tm_t('грн за погонний метр', 'грн за погонный метр')); ?>"
        data-err-busy="<?php echo esc_attr(tm_t('Забагато запитів. Спробуйте за хвилину.', 'Слишком много запросов. Попробуйте через минуту.')); ?>"
        data-err-fail="<?php echo esc_attr(tm_t('Не вдалося оновити ціну. Спробуйте ще раз.', 'Не удалось обновить цену. Попробуйте ещё раз.')); ?>"
        data-lead="<?php echo esc_url(rest_url('tm-calc/v1/lead')); ?>"
        data-err-phone="<?php echo esc_attr(tm_t('Перевірте номер телефону.', 'Проверьте номер телефона.')); ?>"
        data-err-send="<?php echo esc_attr(tm_t('Не вдалося надіслати. Зателефонуйте нам, будь ласка.', 'Не удалось отправить. Позвоните нам, пожалуйста.')); ?>"
        onsubmit="return false">

        <div class="cl-steps">

            <div class="cl-step">
                <div class="cl-step-head"><span class="cl-num">1</span>
                    <h2 class="cl-step-title"><?php echo esc_html(tm_t('Форма кухні', 'Форма кухни')); ?></h2>
                </div>
                <div class="cl-opts" role="radiogroup">
                    <?php foreach ($shapes as $value => $o) : ?>
                        <label class="cl-pick">
                            <input type="radio" name="shape" value="<?php echo esc_attr($value); ?>" data-label="<?php echo esc_attr($o[0]); ?>" <?php checked($value, $defaults['shape']); ?>>
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
                    <h2 class="cl-step-title"><?php echo esc_html(tm_t('Загальна довжина', 'Общая длина')); ?></h2>
                </div>
                <div class="cl-len-row">
                    <label class="cl-len-hint" for="cl-length"><?php echo esc_html(tm_t('Сума всіх стін з меблями', 'Сумма всех стен с мебелью')); ?></label>
                    <span class="cl-len"><span data-calc-length-out><?php echo esc_html(number_format($defaults['length'], 1, ',', '')); ?></span> <?php echo esc_html($m); ?></span>
                </div>
                <input class="cl-range" id="cl-length" type="range" name="length" min="2" max="9" step="0.5" value="<?php echo esc_attr($defaults['length']); ?>">
                <div class="cl-scale"><span>2 <?php echo esc_html($m); ?></span><span>9 <?php echo esc_html($m); ?></span></div>
            </div>

            <div class="cl-step">
                <div class="cl-step-head"><span class="cl-num">3</span>
                    <h2 class="cl-step-title"><?php echo esc_html(tm_t('Матеріал фасадів', 'Материал фасадов')); ?></h2>
                </div>
                <div class="cl-opts cl-opts--3" role="radiogroup">
                    <?php foreach ($facades as $value => $o) : ?>
                        <label class="cl-pick">
                            <input type="radio" name="facade" value="<?php echo esc_attr($value); ?>" data-label="<?php echo esc_attr($o[0]); ?>" <?php checked($value, $defaults['facade']); ?>>
                            <span class="cl-opt">
                                <span class="cl-sw" role="img" aria-label="<?php echo esc_attr($o[3]); ?>" style="background: <?php echo esc_attr($o[2]); ?>"></span>
                                <span class="cl-opt-title"><?php echo esc_html($o[0]); ?></span>
                                <span class="cl-opt-sub"><?php echo esc_html($o[1]); ?></span>
                            </span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="cl-step">
                <div class="cl-step-head"><span class="cl-num">4</span>
                    <h2 class="cl-step-title"><?php echo esc_html(tm_t('Стільниця', 'Столешница')); ?></h2>
                </div>
                <div class="cl-opts" role="radiogroup">
                    <?php foreach ($tops as $value => $o) : ?>
                        <label class="cl-pick">
                            <input type="radio" name="top" value="<?php echo esc_attr($value); ?>" data-label="<?php echo esc_attr($o[0]); ?>" <?php checked($value, $defaults['top']); ?>>
                            <span class="cl-opt">
                                <span class="cl-sw" role="img" aria-label="<?php echo esc_attr($o[3]); ?>" style="background: <?php echo esc_attr($o[2]); ?>"></span>
                                <span class="cl-opt-title"><?php echo esc_html($o[0]); ?></span>
                                <span class="cl-opt-sub"><?php echo esc_html($o[1]); ?></span>
                            </span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="cl-step">
                <div class="cl-step-head"><span class="cl-num">5</span>
                    <h2 class="cl-step-title"><?php echo esc_html(tm_t('Фурнітура', 'Фурнитура')); ?></h2>
                </div>
                <div class="cl-opts cl-opts--2" role="radiogroup">
                    <?php foreach ($hardware as $value => $o) : ?>
                        <label class="cl-pick">
                            <input type="radio" name="hardware" value="<?php echo esc_attr($value); ?>" data-label="<?php echo esc_attr($o[0]); ?>" <?php checked($value, $defaults['hardware']); ?>>
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

                <div class="cl-rows">
                    <div class="cl-row"><span><?php echo esc_html(tm_t('Форма', 'Форма')); ?></span><b data-sum="shape"><?php echo esc_html($shapes[$defaults['shape']][0]); ?></b></div>
                    <div class="cl-row"><span><?php echo esc_html(tm_t('Довжина', 'Длина')); ?></span><b data-sum="length"><?php echo esc_html(number_format($defaults['length'], 1, ',', '') . ' ' . $m); ?></b></div>
                    <div class="cl-row"><span><?php echo esc_html(tm_t('Фасади', 'Фасады')); ?></span><b data-sum="facade"><?php echo esc_html($facades[$defaults['facade']][0]); ?></b></div>
                    <div class="cl-row"><span><?php echo esc_html(tm_t('Стільниця', 'Столешница')); ?></span><b data-sum="top"><?php echo esc_html($tops[$defaults['top']][0]); ?></b></div>
                    <div class="cl-row"><span><?php echo esc_html(tm_t('Фурнітура', 'Фурнитура')); ?></span><b data-sum="hardware"><?php echo esc_html($hardware[$defaults['hardware']][0]); ?></b></div>
                </div>

                <?php if (function_exists('tm_calc_send_lead')) : ?>
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
                        <div class="cl-thanks-text"><?php echo esc_html(tm_t('Ми отримали параметри вашої кухні і зателефонуємо, щоб уточнити деталі.', 'Мы получили параметры вашей кухни и позвоним, чтобы уточнить детали.')); ?></div>
                    </div>
                <?php else : ?>
                    <div class="cl-fine"><?php echo esc_html(tm_t('Ціна орієнтовна. Остаточна вартість — після замірів і погодження проєкту.', 'Цена ориентировочная. Окончательная стоимость — после замеров и согласования проекта.')); ?></div>
                <?php endif; ?>

                <div class="cl-error" data-calc-error hidden></div>
            </div>
        </aside>

    </form>
</section>