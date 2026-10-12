<?php
/**
 * Безопасность: не отдаём список пользователей (и их логины) посторонним.
 * Залогиненным (в админке) всё работает как обычно.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter( 'rest_endpoints', function ( $endpoints ) {
	if ( ! is_user_logged_in() ) {
		unset( $endpoints['/wp/v2/users'] );
		unset( $endpoints['/wp/v2/users/(?P<id>[\d]+)'] );
	}
	return $endpoints;
} );