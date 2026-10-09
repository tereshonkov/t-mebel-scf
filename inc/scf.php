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
 * Сторінка опцій: глобальні дані сайту (телефон, адреса, графік, соцмережі).
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
