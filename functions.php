<?php
/**
 * T-Mebel Classic: точка входу. Тут лише підключення модулів з inc/.
 * Правило: у functions.php немає логіки, кожен файл в inc/ відповідає за одну річ.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'TM_VERSION', wp_get_theme()->get( 'Version' ) );
define( 'TM_DIR', get_template_directory() );
define( 'TM_URI', get_template_directory_uri() );

$tm_modules = array(
	'inc/setup.php',        // підтримка теми, меню, розміри зображень
	'inc/assets.php',       // підключення CSS/JS (по секціях, з filemtime)
	'inc/helpers.php',      // маленькі функції-помічники для шаблонів
	'inc/polylang.php',     // мови, переклад CPT, рядки
	'inc/scf.php',          // SCF: json, сторінка опцій, рендер секцій
	'inc/nav.php',          // меню шапки: walker і мега-меню
	'inc/cpt-project.php',  // тип запису "Проєкти" (перенесемо з блочної теми)
	'inc/cpt-guide.php',    // тип запису "Статті" (блог /guides/)
);

foreach ( $tm_modules as $tm_file ) {
	$tm_path = TM_DIR . '/' . $tm_file;
	if ( file_exists( $tm_path ) ) {
		require_once $tm_path;
	}
}
