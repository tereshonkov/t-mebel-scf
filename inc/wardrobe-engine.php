<?php

/**
 * Калькулятор шкафа: серверная формула.
 *
 * Чистая функция: берёт выбор клиента и настройки, возвращает цену и разбивку.
 * Общие с кухней числа (цена ДСП, отходы, кромка, диапазон) приходят из tm_calc_settings().
 *
 *   $r = tm_wrd_compute( array( 'width' => 2.4, 'fill' => 'mirror' ) );
 *   $r['min'], $r['max'];
 */

if (! defined('ABSPATH')) {
    exit;
}

/** Допустимые варианты выбора (всё остальное заменяется первым значением). */
function tm_wrd_choices()
{
    return array(
        'type'     => array('free', 'built'),
        'sys'      => array('slide', 'hinge'),
        'fill'     => array('dsp', 'mirror', 'combi', 'satin'),
        'interior' => array('basic', 'std', 'full'),
        'depth'    => array(60, 45),
    );
}

/** Приводит вход клиента к безопасному виду. */
function tm_wrd_normalize($in)
{
    $ch  = tm_wrd_choices();
    $out = array();

    foreach (array('type', 'sys', 'fill', 'interior') as $key) {
        $val         = isset($in[$key]) ? (string) $in[$key] : '';
        $out[$key] = in_array($val, $ch[$key], true) ? $val : $ch[$key][0];
    }

    $depth         = isset($in['depth']) ? (int) $in['depth'] : 60;
    $out['depth']  = in_array($depth, $ch['depth'], true) ? $depth : 60;

    $width         = isset($in['width']) ? (float) $in['width'] : 2.4;
    $out['width']  = min(5.0, max(1.0, $width));

    $height        = isset($in['height']) ? (float) $in['height'] : 2.5;
    $out['height'] = min(3.0, max(1.8, $height));

    return $out;
}

/**
 * Главная функция. $w и $s можно передать для тестов.
 * $w - настройки шкафа, $s - общие настройки из калькулятора кухни.
 */
function tm_wrd_compute($input, $w = null, $s = null)
{
    $w  = is_array($w) ? $w : tm_wrd_settings();
    $s  = is_array($s) ? $s : tm_calc_settings();
    $in = tm_wrd_normalize((array) $input);

    $width  = $in['width'];
    $height = $in['height'];
    $front  = $width * $height;   // площадь фасада, от неё всё и считается

    // Секции внутри и двери снаружи.
    $sections = max(2, (int) round($width / max(0.3, $w['section_width'])));
    $doors    = 'slide' === $in['sys']
        ? max(2, (int) ceil($width / max(0.4, $w['door_width_max'])))
        : $sections;

    // Корпус: ДСП считаем от площади фасада, с поправками на глубину и тип.
    $depth_k   = 45 === $in['depth'] ? $w['depth_45_k'] : 1.0;
    $type_k    = 'built' === $in['type'] ? $w['built_k'] : 1.0;
    $carcass_m2   = $front * $w['carcass_m2_per_front'] * $depth_k * $type_k * (1 + $s['carcass_waste'] / 100);
    $carcass_cost = $carcass_m2 * $s['carcass_price_m2'];

    // Наполнение дверей по площади фасада.
    $fill_cost = $front * $w['fill_' . $in['fill']];

    // Кромка и порезка: от корпуса и от тех дверей, что из ДСП.
    $edge_base = $carcass_cost + (in_array($in['fill'], array('dsp', 'combi'), true) ? $fill_cost : 0);
    $edge_cut  = $edge_base * $w['edge_cut_percent'] / 100;

    // Система дверей.
    $system = 'slide' === $in['sys']
        ? $doors * $w['slide_per_door'] + $width * $w['rail_per_m']
        : $doors * $w['hinge_per_door'];
    if ('full' === $in['interior']) {
        $system += $doors * $w['soft_close_door'];
    }

    // Наполнение внутри.
    $shelves = $sections * $w['shelves_per_section'];
    $rods    = $sections;
    $drawers = 'basic' === $in['interior']
        ? 0
        : (int) round($sections * ('full' === $in['interior'] ? $w['drawers_full'] : $w['drawers_std']));

    $inside = $shelves * $w['shelf_price'] + $rods * $w['rod_price'] + $drawers * $w['drawer_price'];
    if ('full' === $in['interior']) {
        $inside += $w['pantograph_price'] + $w['shoe_shelf_price'] + $w['pullout_price'];
    }

    $materials = $carcass_cost + $fill_cost + $edge_cut;
    $hardware  = $system + $inside;

    // Работа: часть не зависит от размера (выезд, проект, монтаж), часть от площади.
    $work = $w['work_base'] + $front * $w['work_per_m2'];

    $total = $materials + $hardware + $work;

    // Диапазон для клиента, правила те же, что у кухни.
    $round = max(1, (int) $s['round_to']);
    $delta = $s['range_percent'] / 100;
    $min   = (int) (floor($total * (1 - $delta) / $round) * $round);
    $max   = (int) (ceil($total * (1 + $delta) / $round) * $round);

    return array(
        'input'     => $in,
        'front'     => round($front, 2),
        'sections'  => $sections,
        'doors'     => $doors,
        'shelves'   => $shelves,
        'rods'      => $rods,
        'drawers'   => $drawers,
        'total'     => (int) round($total),
        'min'       => $min,
        'max'       => $max,
        'per_meter' => (int) round($total / $width),
        'parts'     => array(
            'carcass'   => (int) round($carcass_cost),
            'fill'      => (int) round($fill_cost),
            'edge_cut'  => (int) round($edge_cut),
            'materials' => (int) round($materials),
            'system'    => (int) round($system),
            'inside'    => (int) round($inside),
            'hardware'  => (int) round($hardware),
            'work'      => (int) round($work),
        ),
    );
}
