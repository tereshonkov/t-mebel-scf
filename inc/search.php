<?php
/**
 * Поиск по сайту отключён.
 * Поля поиска в шапке нет, а штатная выдача WordPress показывает служебный текст страниц.
 * Запросы вида /?s=... отдают 404.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'parse_query', function ( $q ) {
	if ( is_admin() || ! $q->is_main_query() || empty( $q->is_search ) ) {
		return;
	}
	$q->is_search = false;
	$q->set( 's', '' );
	$q->set_404();
	status_header( 404 );
	nocache_headers();
} );

// Убираем из разметки сайта обещание поиска (SearchAction), которого больше нет.
add_filter( 'rank_math/json_ld', function ( $data ) {
	if ( isset( $data['WebSite']['potentialAction'] ) ) {
		unset( $data['WebSite']['potentialAction'] );
	}
	return $data;
}, 20 );