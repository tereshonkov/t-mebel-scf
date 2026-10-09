<?php
defined( 'ABSPATH' ) || exit;

/**
 * Тип записи "Проекты" и таксономия "Категории проектов".
 * Имена project / project_category такие же, как в блочной теме, чтобы старые записи подхватились.
 * URL: архив /service/, категории /service/{slug}/ (как на старом сайте), проект /project/{slug}/.
 */
add_action( 'init', function () {

	register_post_type( 'project', array(
		'labels'        => array(
			'name'          => 'Проекты',
			'singular_name' => 'Проект',
			'menu_name'     => 'Проекты',
			'all_items'     => 'Все проекты',
			'add_new'       => 'Добавить проект',
			'add_new_item'  => 'Новый проект',
			'edit_item'     => 'Редактировать проект',
			'search_items'  => 'Искать проекты',
			'not_found'     => 'Проекты не найдены',
		),
		'public'        => true,
		'has_archive'   => 'service',
		'rewrite'       => array( 'slug' => 'project', 'with_front' => false ),
		'menu_icon'     => 'dashicons-portfolio',
		'menu_position' => 20,
		'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt', 'page-attributes' ),
		'show_in_rest'  => true,
	) );

	register_taxonomy( 'project_category', 'project', array(
		'labels'            => array(
			'name'          => 'Категории проектов',
			'singular_name' => 'Категория',
			'menu_name'     => 'Категории',
			'all_items'     => 'Все категории',
			'add_new_item'  => 'Новая категория',
			'edit_item'     => 'Редактировать категорию',
		),
		'hierarchical'      => true,
		'public'            => true,
		'show_in_rest'      => true,
		'show_admin_column' => true,
		'rewrite'           => array( 'slug' => 'service', 'with_front' => false ),
	) );
} );

/**
 * Архив и категории проектов: 12 штук на страницу, по полю "Порядок", потом новые.
 */
add_action( 'pre_get_posts', function ( $query ) {
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}
	if ( $query->is_post_type_archive( 'project' ) || $query->is_tax( 'project_category' ) ) {
		$query->set( 'posts_per_page', 11 );
		$query->set( 'orderby', array( 'menu_order' => 'ASC', 'date' => 'DESC' ) );
	}
} );