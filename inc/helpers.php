<?php
defined('ABSPATH') || exit;

/**
 * Список назв макетів (layout) секцій, які є на поточній сторінці.
 * Працює для сторінок/записів і для сторінок категорій (терміни таксономій).
 * Кешується на час запиту, щоб не читати поля двічі.
 */
function tm_page_layouts(): array {
	static $cache = null;
	if ( null !== $cache ) {
		return $cache;
	}
	$cache = array();
	if ( ! function_exists( 'get_field' ) ) {
		return $cache;
	}

	$source = tm_sections_source();
	if ( ! $source ) {
		return $cache;
	}

	$rows = get_field( 'sections', $source );
	if ( is_array( $rows ) ) {
		foreach ( $rows as $row ) {
			if ( ! empty( $row['acf_fc_layout'] ) ) {
				$cache[] = sanitize_key( $row['acf_fc_layout'] );
			}
		}
	}
	$cache = array_values( array_unique( $cache ) );
	return $cache;
}

/**
 * Откуда брать поле "sections" на текущей странице:
 * ID записи для страниц, "term_{ID}" для категорий, false если нигде.
 */
function tm_sections_source() {
	if ( is_singular() ) {
		return get_queried_object_id();
	}
	if ( is_tax() || is_category() || is_tag() ) {
		$term = get_queried_object();
		return $term ? 'term_' . $term->term_id : false;
	}
	return false;
}

/**
 * Виводить зображення з поля SCF (масив або ID) з правильними атрибутами.
 * alt береться з медіатеки, як ми домовились (SEO-альти задаються там).
 */
function tm_image($image, string $size = 'large', array $attr = array()): string
{
	$id = is_array($image) ? ($image['ID'] ?? 0) : (int) $image;
	return $id ? wp_get_attachment_image($id, $size, false, $attr) : '';
}

/**
 * Виводить посилання з поля типу Link (масив url/title/target).
 */
function tm_link($link, string $class = '', string $fallback = ''): string
{
	if (empty($link['url'])) {
		return '';
	}
	$title  = ( $link['title'] ?? '' ) ?: $fallback;
	$target = ! empty($link['target']) ? ' target="' . esc_attr($link['target']) . '" rel="noopener"' : '';
	$cls    = $class ? ' class="' . esc_attr($class) . '"' : '';
	return '<a href="' . esc_url($link['url']) . '"' . $cls . $target . '>' . esc_html($title) . '</a>';
}

/**
 * Значення з сторінки опцій SCF з урахуванням мови (Polylang).
 * Поля опцій на двох мовах зберігаємо з суфіксом _ua / _ru, наприклад tm_opt( 'address' ).
 */
function tm_opt(string $name, $default = '')
{
	if (! function_exists('get_field')) {
		return $default;
	}
	$lang = function_exists('pll_current_language') ? pll_current_language() : 'uk';
	$suffix = ('ru' === $lang) ? '_ru' : '_ua';
	$value = get_field($name . $suffix, 'option');
	if (empty($value)) {
		$value = get_field($name, 'option');
	}
	return (null === $value || '' === $value) ? $default : $value;
}

/**
 * Короткая подпись интерфейса на текущем языке: tm_t( 'Навігація', 'Навигация' ).
 * Для неизменяемых подписей в шаблонах. Тексты, которые правит редактор, берём из полей.
 */
function tm_t(string $ua, string $ru): string
{
	return (function_exists('pll_current_language') && 'ru' === pll_current_language()) ? $ru : $ua;
}

/**
 * ID главной страницы на текущем языке (UA или RU).
 */
function tm_front_page_id(): int {
	$id = (int) get_option( 'page_on_front' );
	if ( $id && function_exists( 'pll_get_post' ) ) {
		$id = (int) ( pll_get_post( $id ) ?: $id );
	}
	return $id;
}

/**
 * Вывести одну секцию с другой страницы, например форму с главной.
 * Внутри have_rows() работает get_sub_field(), поэтому шаблон секции менять не нужно.
 */
function tm_render_layout_from( string $layout, $post_id ): bool {
	if ( ! $post_id || ! function_exists( 'have_rows' ) ) {
		return false;
	}

	$found = false;

	if ( have_rows( 'sections', $post_id ) ) {
		while ( have_rows( 'sections', $post_id ) ) {
			the_row();
			if ( ! $found && get_row_layout() === $layout ) {
				get_template_part( 'template-parts/sections/' . $layout );
				$found = true;
			}
		}
	}

	return $found;
}
