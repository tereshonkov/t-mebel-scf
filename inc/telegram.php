<?php
/**
 * Заявки с форм CF7 → Telegram. Письмо на почту при этом тоже уходит (резерв).
 * Токен и chat_id: константы TM_TG_TOKEN и TM_TG_CHAT в wp-config.php.
 */
defined( 'ABSPATH' ) || exit;

add_action( 'wpcf7_before_send_mail', function ( $contact_form ) {

	if ( ! defined( 'TM_TG_TOKEN' ) || ! defined( 'TM_TG_CHAT' ) ) {
		return;
	}

	$submission = WPCF7_Submission::get_instance();
	if ( ! $submission ) {
		return;
	}

	$data = $submission->get_posted_data();
	$esc  = function ( $s ) {
		return htmlspecialchars( (string) $s, ENT_QUOTES, 'UTF-8' );
	};

	$name  = $data['your-name'] ?? '';
	$phone = $data['your-phone'] ?? '';
	$url   = $submission->get_meta( 'url' );

	// Основные строки
	$lines   = array();
	$lines[] = '📩 <b>Нова заявка з сайту</b>';
	$lines[] = '';
	$lines[] = '<b>Форма:</b> ' . $esc( $contact_form->title() );
	if ( $name ) {
		$lines[] = '<b>Ім\'я:</b> ' . $esc( $name );
	}
	if ( $phone ) {
		$lines[] = '<b>Телефон:</b> ' . $esc( $phone );
	}

	// Любые другие поля формы (если папа добавит, например, "Що цікавить")
	foreach ( $data as $key => $value ) {
		if ( in_array( $key, array( 'your-name', 'your-phone' ), true ) || '_' === $key[0] ) {
			continue;
		}
		$value = is_array( $value ) ? implode( ', ', $value ) : (string) $value;
		if ( '' !== trim( $value ) ) {
			$lines[] = '<b>' . $esc( $key ) . ':</b> ' . $esc( $value );
		}
	}

	if ( $url ) {
		$lines[] = '<b>Сторінка:</b> ' . $esc( $url );
	}
	$lines[] = '';
	$lines[] = '🕐 ' . wp_date( 'd.m.Y H:i' );

	$response = wp_remote_post(
		'https://api.telegram.org/bot' . TM_TG_TOKEN . '/sendMessage',
		array(
			'timeout' => 8,
			'body'    => array(
				'chat_id'                  => TM_TG_CHAT,
				'text'                     => implode( "\n", $lines ),
				'parse_mode'               => 'HTML',
				'disable_web_page_preview' => 'true',
			),
		)
	);

	// Если Telegram не ответил, пишем в debug.log. Письмо на почту уйдёт в любом случае.
	if ( is_wp_error( $response ) ) {
		error_log( '[TM Telegram] ' . $response->get_error_message() );
	} elseif ( 200 !== wp_remote_retrieve_response_code( $response ) ) {
		error_log( '[TM Telegram] HTTP ' . wp_remote_retrieve_response_code( $response ) . ': ' . wp_remote_retrieve_body( $response ) );
	}
} );