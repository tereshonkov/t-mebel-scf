<?php
/**
 * Калькулятор кухни: серверная формула.
 *
 * Шаг 2: чистая функция расчёта. Ничего не знает про сайт, формы и Telegram.
 * Берёт выбор клиента и настройки из админки, возвращает цену и разбивку.
 *
 * Использование:
 *   $r = tm_calc_compute( array( 'shape' => 'corner', 'length' => 4, ... ) );
 *   $r['min'], $r['max']   // диапазон для клиента
 *   $r['parts']            // разбивка (только для админа и тестов, клиенту не отдаём)
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Допустимые варианты выбора (всё остальное заменяется значением по умолчанию). */
function tm_calc_choices() {
	return array(
		'shape'      => array( 'straight', 'corner', 'u', 'island' ),
		'facade'     => array( 'basic', 'standard', 'premium' ),
		'top'        => array( 'dsp', 'arpa', 'textured', 'compact' ),
		'backsplash' => array( 'none', 'standard', 'premium' ),
		'hardware'   => array( 'standard', 'premium' ),
	);
}

/**
 * Приводит вход клиента к безопасному виду.
 * Если клиент не выбрал столешницу, фартук или ручки (короткий калькулятор),
 * они подбираются по уровню фасадов: базовый, стандарт или премиум.
 */
function tm_calc_normalize( $in, $s ) {
	$ch  = tm_calc_choices();
	$out = array();

	foreach ( array( 'shape', 'facade', 'hardware' ) as $key ) {
		$val         = isset( $in[ $key ] ) ? (string) $in[ $key ] : '';
		$out[ $key ] = in_array( $val, $ch[ $key ], true ) ? $val : $ch[ $key ][0];
	}

	// Набор по умолчанию для каждого уровня фасадов.
	$presets = array(
		'basic'    => array( 'top' => 'dsp', 'backsplash' => 'none', 'handleless' => false ),
		'standard' => array( 'top' => 'arpa', 'backsplash' => 'standard', 'handleless' => false ),
		'premium'  => array( 'top' => 'compact', 'backsplash' => 'premium', 'handleless' => true ),
	);
	$preset = $presets[ $out['facade'] ];

	foreach ( array( 'top', 'backsplash' ) as $key ) {
		$val         = isset( $in[ $key ] ) ? (string) $in[ $key ] : '';
		$out[ $key ] = in_array( $val, $ch[ $key ], true ) ? $val : $preset[ $key ];
	}

	$length        = isset( $in['length'] ) ? (float) $in['length'] : 4.0;
	$out['length'] = min( 12.0, max( 1.5, $length ) );

	$out['upper']      = ! isset( $in['upper'] ) || filter_var( $in['upper'], FILTER_VALIDATE_BOOLEAN );
	$out['handleless'] = isset( $in['handleless'] ) ? filter_var( $in['handleless'], FILTER_VALIDATE_BOOLEAN ) : $preset['handleless'];

	$drawers        = isset( $in['drawers'] ) ? (int) $in['drawers'] : (int) $s['drawers_default'];
	$out['drawers'] = min( 20, max( 0, $drawers ) );
	$cargo          = isset( $in['cargo'] ) ? (int) $in['cargo'] : (int) $s['cargo_default'];
	$out['cargo']   = min( 6, max( 0, $cargo ) );

	return $out;
}

/**
 * Главная функция. $settings можно передать для тестов, иначе берутся из админки.
 */
function tm_calc_compute( $input, $settings = null ) {
	$s  = is_array( $settings ) ? $settings : tm_calc_settings();
	$in = tm_calc_normalize( (array) $input, $s );

	$lower = $in['length'];
	$upper = $in['upper'] ? $in['length'] : 0.0;

	// Модули считаем из длины.
	$mod_lower = max( 1, (int) round( $lower / max( 0.1, $s['module_width_lower'] ) ) );
	$mod_upper = $upper > 0 ? max( 1, (int) round( $upper / max( 0.1, $s['module_width_upper'] ) ) ) : 0;
	$modules   = $mod_lower + $mod_upper;

	// Материалы.
	$carcass_m2   = ( $lower * $s['carcass_m2_per_m_lower'] + $upper * $s['carcass_m2_per_m_upper'] ) * ( 1 + $s['carcass_waste'] / 100 );
	$carcass_cost = $carcass_m2 * $s['carcass_price_m2'];

	$facade_m2   = ( $lower * $s['facade_m2_per_m_lower'] + $upper * $s['facade_m2_per_m_upper'] ) * ( 1 + $s['facade_waste'] / 100 );
	$facade_cost = $facade_m2 * $s[ 'facade_price_' . $in['facade'] ];

	$edge_cut = ( $carcass_cost + $facade_cost ) * $s['edge_cut_percent'] / 100;

	$top_cost = $lower * $s[ 'top_price_' . $in['top'] ] * ( 1 + $s['top_waste'] / 100 );

	$splash_cost = 'none' === $in['backsplash'] ? 0.0 : $lower * $s[ 'backsplash_' . $in['backsplash'] ];

	$materials = $carcass_cost + $facade_cost + $edge_cut + $top_cost + $splash_cost;

	// Фурнитура.
	$premium = 'premium' === $in['hardware'];
	$k       = $premium ? 'premium' : 'standard';

	$doors  = $modules * $s['doors_per_module'];
	$hinges = $doors * $s['hinges_per_door'] * $s[ 'hinge_price_' . $k ];
	$drawer = $in['drawers'] * $s[ 'drawer_price_' . $k ];
	$cargo  = $in['cargo'] * $s[ 'cargo_price_' . $k ];
	$hanger = $mod_upper * $s['hanger_price'];
	$open   = $in['handleless']
		? $doors * $s[ 'p2o_price_' . $k ]
		: ( $doors + $in['drawers'] ) * $s['handle_price'];

	$hardware_base = $hinges + $drawer + $cargo + $hanger + $open;
	$small         = $hardware_base * $s['small_hardware_percent'] / 100;
	$hardware      = $hardware_base + $small;

	// Работа, доставка, монтаж.
	$work = $modules * $s['work_per_module'] * $s[ 'shape_' . $in['shape'] ];

	$total = $materials + $hardware + $work;

	// Диапазон для клиента.
	$round = max( 1, (int) $s['round_to'] );
	$delta = $s['range_percent'] / 100;
	$min   = (int) ( floor( $total * ( 1 - $delta ) / $round ) * $round );
	$max   = (int) ( ceil( $total * ( 1 + $delta ) / $round ) * $round );

	return array(
		'input'     => $in,
		'modules'   => $modules,
		'total'     => (int) round( $total ),
		'min'       => $min,
		'max'       => $max,
		'per_meter' => (int) round( $total / $lower ),
		'parts'     => array(
			'carcass'    => (int) round( $carcass_cost ),
			'facade'     => (int) round( $facade_cost ),
			'edge_cut'   => (int) round( $edge_cut ),
			'top'        => (int) round( $top_cost ),
			'backsplash' => (int) round( $splash_cost ),
			'materials'  => (int) round( $materials ),
			'hardware'   => (int) round( $hardware ),
			'work'       => (int) round( $work ),
		),
	);
}