<?php
defined( 'ABSPATH' ) || exit;

/**
 * Робить CPT "project" перекладним (Polylang Free).
 * Таксономія project_category теж перекладна.
 */
add_filter( 'pll_get_post_types', function ( $types ) {
	$types['project'] = 'project';
	return $types;
} );
add_filter( 'pll_get_taxonomies', function ( $taxonomies ) {
	$taxonomies['project_category'] = 'project_category';
	return $taxonomies;
} );

/**
 * Безпечний виклик перемикача мов для шапки.
 * Повертає масив мов або порожній масив, якщо Polylang вимкнено.
 */
function tm_languages(): array {
	if ( ! function_exists( 'pll_the_languages' ) ) {
		return array();
	}
	$langs = pll_the_languages( array( 'raw' => 1, 'hide_if_no_translation' => 0 ) );
	return is_array( $langs ) ? $langs : array();
}
