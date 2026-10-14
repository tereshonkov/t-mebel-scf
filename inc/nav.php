<?php
defined( 'ABSPATH' ) || exit;

/**
 * Мега-меню: панель з картинками для пункту меню з CSS-класом "menu-item-mega".
 * Картки беруться зі сторінки опцій (поле mega_items, окремо для _ua і _ru).
 */
function tm_mega_panel( string $all_url ): string {
	$items = tm_opt( 'mega_items', array() );
	if ( empty( $items ) || ! is_array( $items ) ) {
		return '';
	}

	$html = '<div class="mm-panel"><div class="mm-grid">';
	foreach ( $items as $item ) {
		if ( empty( $item['link']['url'] ) ) {
			continue;
		}
		$thumb  = tm_image( $item['image'] ?? 0, 'thumbnail', array( 'loading' => 'lazy' ) );
		$html  .= '<a class="mm-item" href="' . esc_url( $item['link']['url'] ) . '">';
		$html  .= '<span class="mm-thumb">' . $thumb . '</span>';
		$html  .= '<span class="mm-text">';
		$html  .= '<span class="mm-title">' . esc_html( $item['title'] ?? '' ) . '</span>';
		$html  .= '<span class="mm-desc">' . esc_html( $item['desc'] ?? '' ) . '</span>';
		$html  .= '</span></a>';
	}
	$html .= '</div>';
	$html .= '<a class="mm-all" href="' . esc_url( $all_url ) . '">' . esc_html( tm_opt( 'mega_all_label', 'Дивитись усі роботи →' ) ) . '</a>';
	$html .= '</div>';
	return $html;
}

/**
 * Walker для головного меню: виводить прості <a> без <ul>/<li>, плоский список (depth = 1).
 * Пункт з CSS-класом "menu-item-mega" отримує випадаючу панель з картками.
 */
class TM_Nav_Walker extends Walker_Nav_Menu {

	public function start_lvl( &$output, $depth = 0, $args = null ) {}
	public function end_lvl( &$output, $depth = 0, $args = null ) {}
	public function end_el( &$output, $item, $depth = 0, $args = null ) {}

	public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
		if ( $depth > 0 ) {
			return;
		}
		$classes    = (array) $item->classes;
		$is_current = in_array( 'current-menu-item', $classes, true ) || in_array( 'current-menu-ancestor', $classes, true );
		$is_mega    = in_array( 'menu-item-mega', $classes, true );

		$link  = '<a class="nav-link' . ( $is_current ? ' is-current' : '' ) . '" href="' . esc_url( $item->url ) . '"';
		$link .= $is_current ? ' aria-current="page"' : '';
		$link .= '>' . esc_html( $item->title );
		$link .= $is_mega ? ' <span class="nav-caret" aria-hidden="true">▾</span>' : '';
		$link .= '</a>';

		if ( $is_mega ) {
			$output .= '<div class="mm-wrap">' . $link . tm_mega_panel( $item->url ) . '</div>';
		} else {
			$output .= $link;
		}
	}
}
