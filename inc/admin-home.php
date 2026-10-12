<?php
/**
 * Админка: вместо Консоли открываем статистику Koko Analytics.
 * Работает для администратора и для редактора (у кого есть право смотреть статистику).
 * Стандартную Консоль можно открыть по ссылке /wp-admin/index.php?tm_classic=1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Плагин статистики включён? Если нет, ничего не перенаправляем. */
function tm_stats_active() {
	return in_array( 'koko-analytics/koko-analytics.php', (array) get_option( 'active_plugins', array() ), true );
}

function tm_stats_url() {
	return admin_url( 'index.php?page=koko-analytics' );
}

/** Есть ли у пользователя доступ к статистике. */
function tm_user_sees_stats( $user = null ) {
	$user = $user ? $user : wp_get_current_user();
	return $user instanceof WP_User
		&& ( user_can( $user, 'manage_options' ) || user_can( $user, 'view_koko_analytics' ) );
}

// После входа в админку.
add_filter( 'login_redirect', function ( $redirect_to, $requested, $user ) {
	if ( tm_stats_active() && tm_user_sees_stats( $user ) ) {
		$plain = untrailingslashit( $redirect_to );
		$home  = array(
			untrailingslashit( admin_url() ),
			untrailingslashit( admin_url( 'index.php' ) ),
		);
		if ( in_array( $plain, $home, true ) ) {
			return tm_stats_url();
		}
	}
	return $redirect_to;
}, 10, 3 );

// При открытии «Консоли» (пункт меню или /wp-admin/).
add_action( 'load-index.php', function () {
	if ( ! empty( $_GET['page'] ) || ! empty( $_GET['tm_classic'] ) ) {
		return; // это уже страница статистики или явный запрос обычной Консоли
	}
	if ( tm_stats_active() && tm_user_sees_stats() ) {
		wp_safe_redirect( tm_stats_url() );
		exit;
	}
} );