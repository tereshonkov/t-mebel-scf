<?php
defined( 'ABSPATH' ) || exit;

/**
 * Група полів зберігається в acf-json/ (під git). SCF/ACF читає і пише
 * цю папку сам, поки в ній лежать JSON-файли груп.
 */
add_filter( 'acf/settings/save_json', fn() => TM_DIR . '/acf-json' );
add_filter( 'acf/settings/load_json', function ( $paths ) {
	$paths[] = TM_DIR . '/acf-json';
	return $paths;
} );

/**
 * Сторінки опцій:
 * - "Налаштування сайту": глобальні дані сайту (телефон, адреса, графік, соцмережі);
 * - "Наши работы": тексти для архіву проєктів /service/.
 */
add_action( 'acf/init', function () {
	if ( ! function_exists( 'acf_add_options_page' ) ) {
		return;
	}
	acf_add_options_page( array(
		'page_title' => __( 'Налаштування сайту', 'tmebel' ),
		'menu_title' => __( 'Налаштування сайту', 'tmebel' ),
		'menu_slug'  => 'tm-site-options',
		'capability' => 'edit_posts',
		'icon_url'   => 'dashicons-admin-generic',
		'redirect'   => false,
	) );

	// Подстраница с текстами для архива проектов /service/ ("Наши работы").
	acf_add_options_sub_page( array(
		'page_title'  => 'Страница «Наши работы»',
		'menu_title'  => 'Наши работы',
		'parent_slug' => 'tm-site-options',
		'menu_slug'   => 'tm-works-options',
		'capability'  => 'edit_posts',
	) );

	acf_add_options_sub_page( array(
		'page_title'  => 'Страница «Статьи»',
		'menu_title'  => 'Статьи',
		'parent_slug' => 'tm-site-options',
		'menu_slug'   => 'tm-guides-options',
		'capability'  => 'edit_posts',
	) );
} );

/**
 * Рендер секцій сторінки: цикл по полю "sections" (Flexible Content).
 * Для кожного макета підключається template-parts/sections/{layout}.php.
 * Дані макета доступні в шаблоні через get_row() / get_sub_field().
 */
function tm_render_sections( string $field = 'sections', $post_id = false ): void {
	if ( ! function_exists( 'have_rows' ) || ! have_rows( $field, $post_id ) ) {
		return;
	}
	while ( have_rows( $field, $post_id ) ) {
		the_row();
		$layout = sanitize_key( get_row_layout() );
		$file   = "template-parts/sections/{$layout}";
		if ( locate_template( $file . '.php' ) ) {
			get_template_part( $file );
		} elseif ( current_user_can( 'edit_theme_options' ) ) {
			echo '<!-- tm: немає шаблону для секції ' . esc_html( $layout ) . ' -->';
		}
	}
}