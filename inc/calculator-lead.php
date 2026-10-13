<?php

/**
 * Калькулятор кухни: заявка с параметрами в Telegram.
 *
 * Адрес:  POST /wp-json/tm-calc/v1/lead
 * Приходит: имя, телефон и выбор клиента (форма, длина, фасады, столешница, фурнитура).
 * Цену сервер пересчитывает сам по этому выбору, цифры из браузера не принимает.
 * Токен и chat_id те же, что в inc/telegram.php: TM_TG_TOKEN и TM_TG_CHAT в wp-config.php.
 *
 * Пока этот файл подключён, на странице калькулятора сама появляется форма заявки
 * (шаблон проверяет, есть ли функция tm_calc_send_lead).
 */

if (! defined('ABSPATH')) {
    exit;
}

const TM_CALC_LEAD_LIMIT  = 5;   // заявок с одного IP...
const TM_CALC_LEAD_WINDOW = 600; // ...за столько секунд (10 минут).

add_action(
    'rest_api_init',
    function () {
        register_rest_route(
            'tm-calc/v1',
            '/lead',
            array(
                'methods'             => 'POST',
                'callback'            => 'tm_calc_rest_lead',
                'permission_callback' => '__return_true',
            )
        );
    }
);

/** Не больше нескольких заявок подряд с одного адреса. */
function tm_calc_lead_limited()
{
    $ip  = isset($_SERVER['REMOTE_ADDR']) ? (string) $_SERVER['REMOTE_ADDR'] : 'unknown';
    $key = 'tm_calc_lead_' . md5($ip);

    $count = (int) get_transient($key);
    if ($count >= TM_CALC_LEAD_LIMIT) {
        return true;
    }
    set_transient($key, $count + 1, TM_CALC_LEAD_WINDOW);
    return false;
}

/** Подписи для сообщения в Telegram (по-украински, как остальные заявки). */
function tm_calc_lead_labels()
{
    return array(
        'shape'    => array(
            'straight' => 'Пряма',
            'corner'   => 'Кутова',
            'u'        => 'П-подібна',
            'island'   => 'З островом',
        ),
        'facade'   => array(
            'basic'    => 'Базовий',
            'standard' => 'Стандарт',
            'premium'  => 'Преміум',
        ),
        'top'      => array(
            'dsp'      => 'ДСП',
            'arpa'     => 'ARPA',
            'textured' => 'Фактурна',
            'compact'  => 'Компакт-плита',
        ),
        'hardware' => array(
            'standard' => 'Стандарт',
            'premium'  => 'Німецька',
        ),
    );
}

/**
 * Отправляет заявку в Telegram.
 *
 * @param array $lead name, phone, page, result (ответ tm_calc_compute).
 * @return bool true, если Telegram принял сообщение.
 */
function tm_calc_send_lead(array $lead)
{
    if (! defined('TM_TG_TOKEN') || ! defined('TM_TG_CHAT')) {
        error_log('[TM Calc] Telegram constants are not defined.');
        return false;
    }

    $esc = function ($s) {
        return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
    };

    $r      = $lead['result'];
    $in     = $r['input'];
    $labels = tm_calc_lead_labels();
    $fmt    = function ($n) {
        return number_format((int) $n, 0, ',', ' ');
    };

    $lines   = array();
    $lines[] = '📩 <b>Нова заявка з сайту</b>';
    $lines[] = '';
    $lines[] = '<b>Форма:</b> Калькулятор кухні';
    if ('' !== $lead['name']) {
        $lines[] = '<b>Ім\'я:</b> ' . $esc($lead['name']);
    }
    $lines[] = '<b>Телефон:</b> ' . $esc($lead['phone']);
    $lines[] = '';
    $lines[] = '🧮 <b>Параметри кухні</b>';
    $lines[] = 'Форма: ' . $esc($labels['shape'][$in['shape']]);
    $lines[] = 'Довжина: ' . $esc(number_format($in['length'], 1, ',', '')) . ' м';
    $lines[] = 'Фасади: ' . $esc($labels['facade'][$in['facade']]);
    $lines[] = 'Стільниця: ' . $esc($labels['top'][$in['top']]);
    $lines[] = 'Фурнітура: ' . $esc($labels['hardware'][$in['hardware']]);
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
        error_log('[TM Calc] Telegram: ' . $response->get_error_message());
        return false;
    }
    if (200 !== wp_remote_retrieve_response_code($response)) {
        error_log('[TM Calc] Telegram HTTP ' . wp_remote_retrieve_response_code($response) . ': ' . wp_remote_retrieve_body($response));
        return false;
    }
    return true;
}

function tm_calc_rest_lead($request)
{
    if (! function_exists('tm_calc_compute')) {
        return new WP_Error('tm_calc_missing', 'Calculator is not available.', array('status' => 500));
    }

    // Ловушка для ботов: люди это поле не видят и не заполняют.
    // Боту отвечаем "успех", чтобы он не пробовал снова.
    if ('' !== trim((string) $request->get_param('lead_site'))) {
        return rest_ensure_response(array('ok' => true));
    }

    if (tm_calc_lead_limited()) {
        return new WP_Error('tm_calc_rate', 'Too many requests.', array('status' => 429));
    }

    $name  = trim(wp_strip_all_tags((string) $request->get_param('lead_name')));
    $name  = function_exists('mb_substr') ? mb_substr($name, 0, 60) : substr($name, 0, 60);
    $phone = preg_replace('/[^\d+\-\s()]/', '', (string) $request->get_param('lead_phone'));
    $phone = trim((string) $phone);
    $digits = strlen(preg_replace('/\D/', '', $phone));
    if ($digits < 9 || $digits > 15) {
        return new WP_Error('tm_calc_phone', 'Invalid phone.', array('status' => 400));
    }

    $input = array();
    foreach (array('shape', 'length', 'facade', 'top', 'hardware') as $key) {
        $val = $request->get_param($key);
        if (null !== $val) {
            $input[$key] = is_scalar($val) ? $val : '';
        }
    }

    $result = tm_calc_compute($input);
    $page   = esc_url_raw(substr((string) $request->get_param('page'), 0, 300));

    $ok = tm_calc_send_lead(
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
