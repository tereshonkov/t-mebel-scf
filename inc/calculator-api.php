<?php

/**
 * Калькулятор кухни: адрес для расчёта цены (REST).
 *
 * Шаг 3: сайт спрашивает у сервера цену, сервер считает по формуле из
 * calculator-engine.php и отдаёт только итоговый диапазон. Коэффициенты и
 * разбивка наружу не уходят.
 *
 * Адрес:  /wp-json/tm-calc/v1/estimate
 * Метод:  GET или POST (GET удобен для проверки в браузере)
 * Пример: /wp-json/tm-calc/v1/estimate?shape=corner&length=4&facade=standard&top=arpa&backsplash=standard&hardware=premium&handleless=1
 */

if (! defined('ABSPATH')) {
    exit;
}

const TM_CALC_RATE_LIMIT  = 60;  // запросов с одного IP...
const TM_CALC_RATE_WINDOW = 300; // ...за столько секунд (5 минут).

add_action(
    'rest_api_init',
    function () {
        register_rest_route(
            'tm-calc/v1',
            '/estimate',
            array(
                'methods'             => array('GET', 'POST'),
                'callback'            => 'tm_calc_rest_estimate',
                'permission_callback' => '__return_true',
            )
        );
    }
);

/** Простая защита от частых запросов с одного адреса. */
function tm_calc_rate_limited()
{
    $ip  = isset($_SERVER['REMOTE_ADDR']) ? (string) $_SERVER['REMOTE_ADDR'] : 'unknown';
    $key = 'tm_calc_rl_' . md5($ip);

    $count = (int) get_transient($key);
    if ($count >= TM_CALC_RATE_LIMIT) {
        return true;
    }
    set_transient($key, $count + 1, TM_CALC_RATE_WINDOW);
    return false;
}

function tm_calc_rest_estimate($request)
{
    if (! function_exists('tm_calc_compute')) {
        return new WP_Error('tm_calc_missing', 'Calculator is not available.', array('status' => 500));
    }

    if (tm_calc_rate_limited()) {
        return new WP_Error('tm_calc_rate', 'Too many requests.', array('status' => 429));
    }

    $input = array();
    foreach (array('shape', 'length', 'upper', 'facade', 'top', 'backsplash', 'hardware', 'handleless', 'drawers', 'cargo') as $key) {
        $val = $request->get_param($key);
        if (null !== $val) {
            $input[$key] = is_scalar($val) ? $val : '';
        }
    }

    $r = tm_calc_compute($input);

    $response = rest_ensure_response(
        array(
            'min'       => $r['min'],
            'max'       => $r['max'],
            'per_meter' => (int) (round($r['per_meter'] / 1000) * 1000),
        )
    );
    $response->header('Cache-Control', 'no-store, max-age=0');
    return $response;
}

/**
 * Калькулятор шкафа: тот же принцип, отдельный адрес.
 *
 * Адрес:  /wp-json/tm-calc/v1/wardrobe
 * Пример: /wp-json/tm-calc/v1/wardrobe?width=2.4&height=2.5&depth=60&sys=slide&fill=mirror&interior=std
 */
add_action(
    'rest_api_init',
    function () {
        register_rest_route(
            'tm-calc/v1',
            '/wardrobe',
            array(
                'methods'             => array('GET', 'POST'),
                'callback'            => 'tm_wrd_rest_estimate',
                'permission_callback' => '__return_true',
            )
        );
    }
);

function tm_wrd_rest_estimate($request)
{
    if (! function_exists('tm_wrd_compute')) {
        return new WP_Error('tm_wrd_missing', 'Calculator is not available.', array('status' => 500));
    }

    if (tm_calc_rate_limited()) {
        return new WP_Error('tm_calc_rate', 'Too many requests.', array('status' => 429));
    }

    $input = array();
    foreach (array('type', 'width', 'height', 'depth', 'sys', 'fill', 'interior') as $key) {
        $val = $request->get_param($key);
        if (null !== $val) {
            $input[$key] = is_scalar($val) ? $val : '';
        }
    }

    $r = tm_wrd_compute($input);

    $response = rest_ensure_response(
        array(
            'min'       => $r['min'],
            'max'       => $r['max'],
            'per_meter' => (int) (round($r['per_meter'] / 1000) * 1000),
            'doors'     => $r['doors'],
        )
    );
    $response->header('Cache-Control', 'no-store, max-age=0');
    return $response;
}
