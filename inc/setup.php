<?php
defined( 'ABSPATH' ) || exit;

add_action( 'after_setup_theme', function () {
	load_theme_textdomain( 'tmebel', TM_DIR . '/languages' );

	add_theme_support( 'title-tag' );               // <title> формує Rank Math
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'custom-logo', array(
		'height'      => 64,
		'width'       => 64,
		'flex-height' => true,
		'flex-width'  => true,
	) );
	add_theme_support( 'responsive-embeds' );

	register_nav_menus( array(
		'primary' => __( 'Головне меню', 'tmebel' ),
		'footer'  => __( 'Меню футера', 'tmebel' ),
		'social'  => __( 'Соцмережі', 'tmebel' ),
	) );

	// Розміри під слайдери та картки (додаємо за потреби).
	add_image_size( 'tm-hero', 2400, 1350, true );
	add_image_size( 'tm-card', 800, 600, true );
} );

// Прибираємо зайве з <head>.
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'wp_head', 'wp_generator' );
