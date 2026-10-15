<?php

/**
 * Калькулятор шкафа: настройки цен в админке.
 *
 * Общие с кухней числа (цена ДСП, отходы, кромка, диапазон, округление)
 * берутся из tm_calc_settings(), здесь только то, что есть лишь у шкафа.
 */

if (! defined('ABSPATH')) {
    exit;
}

const TM_WRD_OPTION = 'tm_wrd_settings';

/**
 * Поля: группа => [ ключ => [подпись, значение по умолчанию, единица] ].
 * Цены материалов - Viyar, октябрь 2026, без скидки (стекло и зеркало идут с порезкой).
 */
function tm_wrd_fields()
{
    return array(
        'Корпус'              => array(
            'carcass_m2_per_front' => array('ДСП на 1 м² фасада (бока, полки, перегородки)', 2.2, 'м²'),
            'section_width'        => array('Средняя ширина секции', 0.8, 'м'),
            'depth_45_k'           => array('Коэффициент: глубина 45 см', 0.88, '×'),
            'built_k'              => array('Коэффициент: встроенный шкаф', 0.85, '×'),
            'edge_cut_percent' => array('Кромка и порезка (от корпуса и дверей ДСП)', 14, '%'),
        ),
        'Двери'               => array(
            'door_width_max'  => array('Максимальная ширина одной двери', 1.0, 'м'),
            'slide_per_door'  => array('Купе: профиль рамы и ролики на дверь', 1200, 'грн'),
            'rail_per_m'      => array('Купе: направляющие верх и низ', 700, 'грн за м'),
            'hinge_per_door'  => array('Распашные: петли и фурнитура на дверь', 450, 'грн'),
            'soft_close_door' => array('Доводчик на дверь (премиум-наполнение)', 600, 'грн'),
        ),
        'Наполнение дверей'   => array(
            'fill_dsp'    => array('ДСП под цвет корпуса', 730, 'грн за м²'),
            'fill_mirror' => array('Зеркало серебро 4 мм', 1370, 'грн за м²'),
            'fill_combi'  => array('Комби: зеркало плюс ДСП', 1050, 'грн за м²'),
            'fill_satin'  => array('Сатин или крашеное стекло', 1950, 'грн за м²'),
        ),
        'Наполнение внутри'   => array(
            'shelf_price'        => array('Полка', 70, 'грн'),
            'rod_price'          => array('Штанга с креплениями', 200, 'грн'),
            'drawer_price'       => array('Выдвижной ящик', 900, 'грн'),
            'shelves_per_section' => array('Полок в секции', 3, 'шт'),
            'drawers_std'        => array('Ящиков на секцию: стандарт', 1, 'шт'),
            'drawers_full'       => array('Ящиков на секцию: полное', 2, 'шт'),
            'pantograph_price'   => array('Пантограф', 1800, 'грн'),
            'shoe_shelf_price'   => array('Полка для обуви', 700, 'грн'),
            'pullout_price'      => array('Выдвижная вешалка', 450, 'грн'),
        ),
        'Работа и монтаж'     => array(
            'work_base'   => array('Замер, проект, доставка, монтаж (не зависит от размера)', 4000, 'грн'),
            'work_per_m2' => array('Работа на 1 м² фасада', 1200, 'грн'),
        ),
    );
}

/** Значения по умолчанию одним плоским массивом. */
function tm_wrd_defaults()
{
    $out = array();
    foreach (tm_wrd_fields() as $group) {
        foreach ($group as $key => $field) {
            $out[$key] = $field[1];
        }
    }
    return $out;
}

/** Текущие настройки: сохранённое поверх значений по умолчанию. */
function tm_wrd_settings()
{
    $saved = get_option(TM_WRD_OPTION, array());
    if (! is_array($saved)) {
        $saved = array();
    }
    return array_merge(tm_wrd_defaults(), array_intersect_key($saved, tm_wrd_defaults()));
}

/** Пункт меню: подраздел у существующего "Калькулятор". */
add_action(
    'admin_menu',
    function () {
        add_submenu_page(
            'tm-calc-settings',
            'Калькулятор шкафа',
            'Шкаф',
            'manage_options',
            'tm-wrd-settings',
            'tm_wrd_render_page'
        );
    },
    20
);

add_action(
    'admin_init',
    function () {
        register_setting(
            'tm_wrd_group',
            TM_WRD_OPTION,
            array(
                'type'              => 'array',
                'sanitize_callback' => 'tm_wrd_sanitize',
                'default'           => array(),
            )
        );
    }
);

function tm_wrd_sanitize($input)
{
    $defaults = tm_wrd_defaults();
    $clean    = array();
    foreach ($defaults as $key => $default) {
        if (isset($input[$key]) && '' !== $input[$key]) {
            $value         = str_replace(',', '.', (string) $input[$key]);
            $value         = is_numeric($value) ? (float) $value : $default;
            $clean[$key] = max(0, $value);
        } else {
            $clean[$key] = $default;
        }
    }
    return $clean;
}

/** Страница настроек. */
function tm_wrd_render_page()
{
    if (! current_user_can('manage_options')) {
        return;
    }
    $values = tm_wrd_settings();
?>
    <div class="wrap">
        <h1>Калькулятор шкафа: настройки</h1>
        <p>Только то, что отличает шкаф от кухни. Цена ДСП, запас на отходы, кромка с порезкой, диапазон и округление берутся из настроек кухни, чтобы на сайте не было двух разных цен на один и тот же материал.</p>
        <form method="post" action="options.php">
            <?php settings_fields('tm_wrd_group'); ?>
            <?php foreach (tm_wrd_fields() as $group => $fields) : ?>
                <h2><?php echo esc_html($group); ?></h2>
                <table class="form-table" role="presentation">
                    <?php foreach ($fields as $key => $field) : ?>
                        <tr>
                            <th scope="row"><label for="tm_wrd_<?php echo esc_attr($key); ?>"><?php echo esc_html($field[0]); ?></label></th>
                            <td>
                                <input type="text" inputmode="decimal" class="small-text"
                                    id="tm_wrd_<?php echo esc_attr($key); ?>"
                                    name="<?php echo esc_attr(TM_WRD_OPTION . '[' . $key . ']'); ?>"
                                    value="<?php echo esc_attr($values[$key]); ?>">
                                <span class="description"><?php echo esc_html($field[2]); ?> (по умолчанию: <?php echo esc_html($field[1]); ?>)</span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php endforeach; ?>
            <?php submit_button('Сохранить'); ?>
        </form>
        <?php if (function_exists('tm_wrd_compute')) : ?>
            <h2>Проверка расчёта (для вас, клиенты этого не видят)</h2>
            <p>Три эталонных шкафа пересчитываются по текущим настройкам. Сохраните изменения и обновите страницу, чтобы увидеть новый результат.</p>
            <table class="widefat striped" style="max-width:1100px">
                <thead>
                    <tr>
                        <th>Шкаф</th>
                        <th>Фасад</th>
                        <th>Дверей</th>
                        <th>Итого</th>
                        <th>Диапазон клиенту</th>
                        <th>За метр</th>
                        <th>Материалы</th>
                        <th>Фурнитура</th>
                        <th>Работа</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (tm_wrd_reference() as $name => $input) : ?>
                        <?php $r = tm_wrd_compute($input); ?>
                        <tr>
                            <td><?php echo esc_html($name); ?></td>
                            <td><?php echo esc_html($r['front']); ?> м²</td>
                            <td><?php echo esc_html($r['doors']); ?></td>
                            <td><strong><?php echo esc_html(number_format($r['total'], 0, ',', ' ')); ?></strong></td>
                            <td><?php echo esc_html(number_format($r['min'], 0, ',', ' ') . ' - ' . number_format($r['max'], 0, ',', ' ')); ?></td>
                            <td><?php echo esc_html(number_format($r['per_meter'], 0, ',', ' ')); ?></td>
                            <td><?php echo esc_html(number_format($r['parts']['materials'], 0, ',', ' ')); ?></td>
                            <td><?php echo esc_html(number_format($r['parts']['hardware'], 0, ',', ' ')); ?></td>
                            <td><?php echo esc_html(number_format($r['parts']['work'], 0, ',', ' ')); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
<?php
}

/** Эталонные шкафы для проверки формулы. */
function tm_wrd_reference()
{
    return array(
        'Базовый (2,0 × 2,2 м, глубина 45, двери ДСП)'   => array(
            'type'     => 'free',
            'width'    => 2.0,
            'height'   => 2.2,
            'depth'    => 45,
            'sys'      => 'slide',
            'fill'     => 'dsp',
            'interior' => 'basic',
        ),
        'Стандарт (2,4 × 2,5 м, глубина 60, зеркало)'    => array(
            'type'     => 'free',
            'width'    => 2.4,
            'height'   => 2.5,
            'depth'    => 60,
            'sys'      => 'slide',
            'fill'     => 'mirror',
            'interior' => 'std',
        ),
        'Премиум (3,0 × 2,8 м, глубина 60, сатин)'       => array(
            'type'     => 'free',
            'width'    => 3.0,
            'height'   => 2.8,
            'depth'    => 60,
            'sys'      => 'slide',
            'fill'     => 'satin',
            'interior' => 'full',
        ),
    );
}
?>