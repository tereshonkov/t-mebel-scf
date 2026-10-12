<?php

/**
 * Contact Form 7: общие доработки для всех форм сайта.
 */
defined('ABSPATH') || exit;

/**
 * Под каждой формой: "Натискаючи кнопку, ви погоджуєтесь з політикою конфіденційності".
 * Добавляется автоматически, ни одну форму CF7 править не нужно.
 */
add_filter('wpcf7_form_elements', function ($html) {
	$url = tm_privacy_url();
	if (! $url) {
		return $html;
	}

	$note = sprintf(
		'<p class="form-consent">%s <a href="%s" target="_blank" rel="noopener">%s</a></p>',
		esc_html(tm_t('Натискаючи кнопку, ви погоджуєтесь з', 'Нажимая кнопку, вы соглашаетесь с')),
		esc_url($url),
		esc_html(tm_t('політикою конфіденційності', 'политикой конфиденциальности'))
	);

	return $html . $note;
});
