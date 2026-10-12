<?php
/**
 * Права роли «Редактор» для работы с сайтом.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Редактору разрешаем смотреть статистику Koko Analytics.
// Право записывается в роль один раз, повторно не добавляется.
add_action( 'init', function () {
	$role = get_role( 'editor' );
	if ( $role && ! $role->has_cap( 'view_koko_analytics' ) ) {
		$role->add_cap( 'view_koko_analytics' );
	}
} );