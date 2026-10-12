<?php

/**
 * Google Ads и GA4: подключение тега и события (заявка, клик по телефону).
 * Для залогиненных сотрудников (админ, редактор) тег не выводится, чтобы наши заходы не считались.
 */

if (! defined('ABSPATH')) {
    exit;
}

define('TM_AW_ID', 'AW-16643061743'); // Google Ads
define('TM_GA_ID', 'G-DSKK22XDCJ');   // GA4

// Метки конверсий Google Ads (часть после слэша в send_to). Пока пусто, события для Ads не отправляются.
define('TM_AW_LEAD_LABEL', '');  // заявка с формы
define('TM_AW_PHONE_LABEL', ''); // клик по телефону

function tm_tracking_enabled()
{
    return ! is_admin() && ! (is_user_logged_in() && current_user_can('edit_posts'));
}

add_action('wp_head', function () {
    if (! tm_tracking_enabled()) {
        return;
    }
?>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());
        gtag('config', '<?php echo esc_js(TM_AW_ID); ?>');
        gtag('config', '<?php echo esc_js(TM_GA_ID); ?>');

        // Скрипт Google подгружаем при первом действии посетителя или через 5 секунд после загрузки страницы.
        (function() {
            var loaded = false;

            function load() {
                if (loaded) {
                    return;
                }
                loaded = true;
                var s = document.createElement('script');
                s.async = true;
                s.src = 'https://www.googletagmanager.com/gtag/js?id=<?php echo esc_js(TM_AW_ID); ?>';
                document.head.appendChild(s);
            }
            ['scroll', 'pointerdown', 'keydown', 'touchstart'].forEach(function(ev) {
                window.addEventListener(ev, load, {
                    once: true,
                    passive: true
                });
            });
            window.addEventListener('load', function() {
                setTimeout(load, 5000);
            });
        })();
    </script>
<?php
}, 1);

// События: успешная отправка формы (Contact Form 7) и клик по номеру телефона.
add_action('wp_footer', function () {
    if (! tm_tracking_enabled()) {
        return;
    }
    $lead  = TM_AW_LEAD_LABEL ? TM_AW_ID . '/' . TM_AW_LEAD_LABEL : '';
    $phone = TM_AW_PHONE_LABEL ? TM_AW_ID . '/' . TM_AW_PHONE_LABEL : '';
?>
    <script>
        (function() {
            var leadTo = <?php echo wp_json_encode($lead); ?>;
            var phoneTo = <?php echo wp_json_encode($phone); ?>;

            function send(name, to) {
                if (typeof gtag !== 'function') {
                    return;
                }
                gtag('event', name);
                if (to) {
                    gtag('event', 'conversion', {
                        'send_to': to
                    });
                }
            }
            document.addEventListener('wpcf7mailsent', function() {
                send('generate_lead', leadTo);
            });
            document.addEventListener('click', function(e) {
                var a = e.target.closest ? e.target.closest('a[href^="tel:"]') : null;
                if (a) {
                    send('phone_click', phoneTo);
                }
            });
        })();
    </script>
<?php
}, 99);
