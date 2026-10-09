<?php
defined( 'ABSPATH' ) || exit;

/**
 * Підключає файл CSS/JS з версією за часом зміни (кеш скидається при кожному деплої).
 */
function tm_asset_version( string $rel ): string {
	$path = TM_DIR . '/' . ltrim( $rel, '/' );
	return file_exists( $path ) ? (string) filemtime( $path ) : TM_VERSION;
}

add_action( 'wp_enqueue_scripts', function () {
	// Шрифти (тимчасово з Google Fonts; потім self-host у assets/fonts).
	wp_enqueue_style(
		'tm-fonts',
		'https://fonts.googleapis.com/css2?family=Jost:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;1,500&display=swap',
		array(),
		null
	);

	// Глобальні стилі: токени, база, шапка, футер.
	$globals = array( 'base', 'header', 'footer', 'buttons' );
	$prev    = array( 'tm-fonts' );
	foreach ( $globals as $name ) {
		$rel = "assets/css/{$name}.css";
		if ( file_exists( TM_DIR . '/' . $rel ) ) {
			wp_enqueue_style( "tm-{$name}", TM_URI . '/' . $rel, $prev, tm_asset_version( $rel ) );
			$prev = array( "tm-{$name}" );
		}
	}

	// CSS секцій: тільки тих, що реально є на поточній сторінці.
	foreach ( tm_page_layouts() as $layout ) {
		$rel = "assets/css/sections/{$layout}.css";
		if ( file_exists( TM_DIR . '/' . $rel ) ) {
			wp_enqueue_style( "tm-section-{$layout}", TM_URI . '/' . $rel, array( 'tm-base' ), tm_asset_version( $rel ) );
		}
		$js = "assets/js/sections/{$layout}.js";
		if ( file_exists( TM_DIR . '/' . $js ) ) {
			wp_enqueue_script( "tm-section-{$layout}", TM_URI . '/' . $js, array(), tm_asset_version( $js ), true );
		}
	}

	// Глобальний скрипт сайту.
	$main = 'assets/js/main.js';
	if ( file_exists( TM_DIR . '/' . $main ) ) {
		wp_enqueue_script( 'tm-main', TM_URI . '/' . $main, array(), tm_asset_version( $main ), true );
	}
} );
