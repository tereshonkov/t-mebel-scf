<?php

/**
 * Тип записей "Статьи" (блог) и его рубрики.
 * Адреса: /guides/ (список), /guides/{slug}/ (статья), /guides/topic/{рубрика}/
 *
 * ВАЖНО: рубрики регистрируются ДО типа записей. Иначе правило вложений
 * guides/{x}/{y}/ перехватывает адреса рубрик guides/topic/{y}/.
 */
defined('ABSPATH') || exit;

add_action('init', function () {

	// 1) Сначала рубрики
	register_taxonomy('guide_category', 'guide', array(
		'labels' => array(
			'name'          => 'Рубрики статей',
			'singular_name' => 'Рубрика',
			'add_new_item'  => 'Добавить рубрику',
			'edit_item'     => 'Редактировать рубрику',
			'all_items'     => 'Все рубрики',
			'menu_name'     => 'Рубрики',
		),
		'public'            => true,
		'hierarchical'      => true,
		'show_admin_column' => true,
		'rewrite'           => array('slug' => 'guides/topic', 'with_front' => false),
		'show_in_rest'      => true,
	));

	// 2) Потом сам тип записей
	register_post_type('guide', array(
		'labels' => array(
			'name'               => 'Статьи',
			'singular_name'      => 'Статья',
			'add_new'            => 'Добавить статью',
			'add_new_item'       => 'Новая статья',
			'edit_item'          => 'Редактировать статью',
			'new_item'           => 'Новая статья',
			'view_item'          => 'Смотреть статью',
			'search_items'       => 'Искать статьи',
			'not_found'          => 'Статей не найдено',
			'not_found_in_trash' => 'В корзине статей нет',
			'all_items'          => 'Все статьи',
			'menu_name'          => 'Статьи',
		),
		'public'        => true,
		'has_archive'   => 'guides',
		'rewrite'       => array('slug' => 'guides', 'with_front' => false),
		'menu_icon'     => 'dashicons-media-text',
		'menu_position' => 6,
		'supports'      => array('title', 'editor', 'thumbnail', 'excerpt', 'revisions'),
		'show_in_rest'  => true,
		'taxonomies'    => array('guide_category'),
	));
});

// 13 статей на странице списка и рубрики: 1 большая + 12 в сетке
add_action('pre_get_posts', function ($query) {
	if (is_admin() || ! $query->is_main_query()) {
		return;
	}
	if ($query->is_post_type_archive('guide') || $query->is_tax('guide_category')) {
		$query->set('posts_per_page', 13);
	}
});
