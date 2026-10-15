<?php

/**
 * T-Mebel Classic: точка входу. Тут лише підключення модулів з inc/.
 * Правило: у functions.php немає логіки, кожен файл в inc/ відповідає за одну річ.
 */

if (! defined('ABSPATH')) {
	exit;
}

define('TM_VERSION', wp_get_theme()->get('Version'));
define('TM_DIR', get_template_directory());
define('TM_URI', get_template_directory_uri());

$tm_modules = array(
	'inc/setup.php',        // підтримка теми, меню, розміри зображень
	'inc/assets.php',       // підключення CSS/JS (по секціях, з filemtime)
	'inc/helpers.php',      // маленькі функції-помічники для шаблонів
	'inc/polylang.php',     // мови, переклад CPT, рядки
	'inc/scf.php',          // SCF: json, сторінка опцій, рендер секцій
	'inc/nav.php',          // меню шапки: walker і мега-меню
	'inc/cpt-project.php',  // тип запису "Проєкти" (перенесемо з блочної теми)
	'inc/cpt-guide.php',    // тип запису "Статті" (блог /guides/)
	'inc/popup.php', 	  // попапи (рендер форм)
	'inc/cf7.php',          // CF7: згода з політикою під усіма формами
	'inc/telegram.php',     // заявки з форм CF7 у Telegram
	'inc/seo.php',		  // SEO: заголовки і опис архівів /service/ і /guides/
	'inc/redirects.php',	  // редіректи для старих URL
	'inc/search.php',		// пошук по сайту відключений, щоб не показувати служебні тексти сторінок
	'inc/security.php',		// безпека: не віддаємо список користувачів (і їх логіни) стороннім
	'inc/admin-home.php',	// адмінка: замість Консолі відкриваємо статистику Koko Analytics
	'inc/editor-access.php', // права ролі «Редактор» для роботи з сайтом
	'inc/tracking.php',		// Google Ads и GA4: подключение тега и события (заявка, клик по телефону)
	'inc/css-bundle.php',    // Склеює всі стилі теми (tm-*) у один файл, щоб замість 16 запитів був 1.
	'inc/calculator-settings.php', // налаштування калькулятора (ціни, коефіцієнти, формули)
	'inc/calculator-engine.php',    // формула розрахунку калькулятора
	'inc/calculator-api.php',    // REST API для калькулятора
	'inc/calculator-lead.php',    // REST API для заявки з калькулятора
	'inc/wardrobe-settings.php',  // налаштування калькулятора шафи (ціни, коефіцієнти, формули)
	'inc/wardrobe-engine.php',    // формула розрахунку калькулятора шафи
);

foreach ($tm_modules as $tm_file) {
	$tm_path = TM_DIR . '/' . $tm_file;
	if (file_exists($tm_path)) {
		require_once $tm_path;
	}
}
