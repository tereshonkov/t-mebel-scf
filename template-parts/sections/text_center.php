<?php
/**
 * Секция "Текст по центру": заголовок H2 и несколько абзацев, узкая колонка.
 * Подходит для SEO-текста категорий и вводных блоков.
 */
defined( 'ABSPATH' ) || exit;

$title = get_sub_field( 'title' );
$text  = get_sub_field( 'text' );

if ( ! $title && ! $text ) {
	return;
}
?>
<section class="text-center">
	<div class="text-center__inner">
		<?php if ( $title ) : ?>
			<h2 class="text-center__title"><?php echo esc_html( $title ); ?></h2>
		<?php endif; ?>
		<?php if ( $text ) : ?>
			<div class="text-center__text"><?php echo wp_kses_post( wpautop( $text ) ); ?></div>
		<?php endif; ?>
	</div>
</section>