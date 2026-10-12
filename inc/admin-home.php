<?php

/**
 * Админка: вместо Консоли открываем статистику Koko Analytics.
 * Стандартную Консоль можно открыть по ссылке /wp-admin/index.php?tm_classic=1
 */

if (! defined('ABSPATH')) {
    exit;
}

/** Плагин статистики включён? Если нет, ничего не перенаправляем. */
function tm_stats_active()
{
    return in_array('koko-analytics/koko-analytics.php', (array) get_option('active_plugins', array()), true);
}

function tm_stats_url()
{
    return admin_url('index.php?page=koko-analytics');
}

// После входа в админку.
add_filter('login_redirect', function ($redirect_to, $requested, $user) {
    if ($user instanceof WP_User && user_can($user, 'manage_options') && tm_stats_active()) {
        $plain = untrailingslashit($redirect_to);
        $home  = array(
            untrailingslashit(admin_url()),
            untrailingslashit(admin_url('index.php')),
        );
        if (in_array($plain, $home, true)) {
            return tm_stats_url();
        }
    }
    return $redirect_to;
}, 10, 3);

// При открытии «Консоли» (пункт меню или /wp-admin/).
add_action('load-index.php', function () {
    if (! empty($_GET['page']) || ! empty($_GET['tm_classic'])) {
        return; // это уже страница статистики или явный запрос обычной Консоли
    }
    if (current_user_can('manage_options') && tm_stats_active()) {
        wp_safe_redirect(tm_stats_url());
        exit;
    }
});
