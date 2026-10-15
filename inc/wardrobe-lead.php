<?php

/**
 * Калькулятор шкафа: заявка с параметрами в Telegram.
 *
 * Адрес:  POST /wp-json/tm-calc/v1/wardrobe-lead
 * Цену сервер пересчитывает сам по выбору клиента, цифры из браузера не принимает.
 * Ограничение частоты и ловушка для ботов те же, что у кухонной заявки.
 */

if (! defined('ABSPATH')) {
    exit;
}

add_action(
    'rest_api_init',
    function () {
        register_rest_route(
            'tm-calc/v1',
            '/wardrobe-lead',
            array(
                'methods'             => 'POST',
                'callback'            => 'tm_wrd_rest_lead',
                'permission_callback' => '__return_true',
            )
        );
    }
);

/** Подписи для сообщения в Telegram (по-украински, как остальные заявки). */
function tm_wrd_lead_labels()
{
    return array(
        'type'     => array(
            'free'  => 'Корпусна',
            'built' => 'Вбудована',
        ),
        'sys'      => array(
            'slide' => 'Купе',
            'hinge' => 'Розпашні',
        ),
        'fill'     => array(
            'dsp'    => 'ДСП',
            'mirror' => 'Дзеркало',
            'combi'  => 'Комбі',
            'satin'  => 'Сатин',
        ),
        'interior' => array(
            'basic' => 'Базове',
            'std'   => 'Стандарт',
            'full'  => 'Повне',
        ),
    );
}

/**
 * Отправляет заявку в Telegram.
 *
 * @param array $lead name, phone, page, result (ответ tm_wrd_compute).
 * @return bool true, если Telegram принял сообщение.
 */
function tm_wrd_send_lead(array $lead)
{
    if (! defined('TM_TG_TOKEN') || ! defined('TM_TG_CHAT')) {
        error_log('[TM Wardrobe] Telegram constants are not defined.');
        return false;
    }

    $esc = function ($s) {
        return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
    };

    $r      = $lead['result'];
    $in     = $r['input'];
    $labels = tm_wrd_lead_labels();
    $fmt    = function ($n) {
        return number_format((int) $n, 0, ',', ' ');
    };

    $lines   = array();
    $lines[] = '📩 <b>Нова заявка з сайту</b>';
    $lines[] = '';
    $lines[] = '<b>Форма:</b> Калькулятор шафи';
    if ('' !== $lead['name']) {
        $lines[] = '<b>Ім\'я:</b> ' . $esc($lead['name']);
    }
    $lines[] = '<b>Телефон:</b> ' . $esc($lead['phone']);
    $lines[] = '';
    $lines[] = '🚪 <b>Параметри шафи</b>';
    $lines[] = 'Тип: ' . $esc($labels['type'][$in['type']]);
    $lines[] = 'Розміри: ' . $esc(number_format($in['width'], 1, ',', '')) . ' × '
        . $esc(number_format($in['height'], 1, ',', '')) . ' м, глибина ' . $esc($in['depth']) . ' см';
    $lines[] = 'Двері: ' . $esc($labels['sys'][$in['sys']]) . ', ' . $esc($r['doors']) . ' шт';
    $lines[] = 'Наповнення дверей: ' . $esc($labels['fill'][$in['fill']]);
    $lines[] = 'Всередині: ' . $esc($labels['interior'][$in['interior']])
        . ' (' . $esc($r['shelves']) . ' полиць, ' . $esc($r['rods']) . ' штанг'
        . ($r['drawers'] ? ', ' . $esc($r['drawers']) . ' ящиків' : '') . ')';
    $lines[] = '';
    $lines[] = '💰 <b>Розрахунок на сайті:</b> від ' . $fmt($r['min']) . ' до ' . $fmt($r['max']) . ' грн';
    if (! empty($lead['page'])) {
        $lines[] = '<b>Сторінка:</b> ' . $esc($lead['page']);
    }
    $lines[] = '';
    $lines[] = '🕐 ' . wp_date('d.m.Y H:i');

    $response = wp_remote_post(
        'https://api.telegram.org/bot' . TM_TG_TOKEN . '/sendMessage',
        array(
            'timeout' => 8,
            'body'    => array(
                'chat_id'                  => TM_TG_CHAT,
                'text'                     => implode("\n", $lines),
                'parse_mode'               => 'HTML',
                'disable_web_page_preview' => 'true',
            ),
        )
    );

    if (is_wp_error($response)) {
        error_log('[TM Wardrobe] Telegram: ' . $response->get_error_message());
        return false;
    }
    if (200 !== wp_remote_retrieve_response_code($response)) {
        error_log('[TM Wardrobe] Telegram HTTP ' . wp_remote_retrieve_response_code($response) . ': ' . wp_remote_retrieve_body($response));
        return false;
    }
    return true;
}

function tm_wrd_rest_lead($request)
{
    if (! function_exists('tm_wrd_compute')) {
        return new WP_Error('tm_wrd_missing', 'Calculator is not available.', array('status' => 500));
    }

    // Ловушка для ботов: люди это поле не видят и не заполняют.
    if ('' !== trim((string) $request->get_param('lead_site'))) {
        return rest_ensure_response(array('ok' => true));
    }

    if (tm_calc_lead_limited()) {
        return new WP_Error('tm_calc_rate', 'Too many requests.', array('status' => 429));
    }

    $name   = trim(wp_strip_all_tags((string) $request->get_param('lead_name')));
    $name   = function_exists('mb_substr') ? mb_substr($name, 0, 60) : substr($name, 0, 60);
    $phone  = preg_replace('/[^\d+\-\s()]/', '', (string) $request->get_param('lead_phone'));
    $phone  = trim((string) $phone);
    $digits = strlen(preg_replace('/\D/', '', $phone));
    if ($digits < 9 || $digits > 15) {
        return new WP_Error('tm_calc_phone', 'Invalid phone.', array('status' => 400));
    }

    $input = array();
    foreach (array('type', 'width', 'height', 'depth', 'sys', 'fill', 'interior') as $key) {
        $val = $request->get_param($key);
        if (null !== $val) {
            $input[$key] = is_scalar($val) ? $val : '';
        }
    }

    $result = tm_wrd_compute($input);
    $page   = esc_url_raw(substr((string) $request->get_param('page'), 0, 300));

    $ok = tm_wrd_send_lead(
        array(
            'name'   => $name,
            'phone'  => $phone,
            'page'   => $page,
            'result' => $result,
        )
    );

    if (! $ok) {
        return new WP_Error('tm_calc_send', 'Could not send.', array('status' => 502));
    }

    $response = rest_ensure_response(array('ok' => true));
    $response->header('Cache-Control', 'no-store, max-age=0');
    return $response;
}
