<?php

/**
 * Попап-заявка: один на весь сайт.
 * Открывается ссылкой href="#popup" или элементом с data-popup="lead".
 */
defined('ABSPATH') || exit;

add_action('wp_enqueue_scripts', function () {
	if (! tm_opt('popup_form')) {
		return;
	}

	$css = 'assets/css/popup.css';
	if (file_exists(TM_DIR . '/' . $css)) {
		wp_enqueue_style('tm-popup', TM_URI . '/' . $css, array('tm-base'), tm_asset_version($css));
	}

	$js = 'assets/js/popup.js';
	if (file_exists(TM_DIR . '/' . $js)) {
		wp_enqueue_script('tm-popup', TM_URI . '/' . $js, array(), tm_asset_version($js), true);
	}
}, 20);

add_action('wp_footer', function () {
	if (tm_opt('popup_form')) {
		get_template_part('template-parts/popup/lead');
	}
}, 5);
