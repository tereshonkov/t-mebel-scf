<?php
defined('ABSPATH') || exit;

/**
 * Список назв макетів (layout) секцій, які є на поточній сторінці.
 * Кешується на час запиту, щоб не читати поля двічі.
 */
function tm_page_layouts(): array
{
	static $cache = null;
	if (null !== $cache) {
		return $cache;
	}
	$cache = array();
	if (! function_exists('get_field') || ! is_singular()) {
		return $cache;
	}
	$rows = get_field('sections', get_queried_object_id());
	if (is_array($rows)) {
		foreach ($rows as $row) {
			if (! empty($row['acf_fc_layout'])) {
				$cache[] = sanitize_key($row['acf_fc_layout']);
			}
		}
	}
	$cache = array_values(array_unique($cache));
	return $cache;
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
	$title  = $link['title'] ?: $fallback;
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
