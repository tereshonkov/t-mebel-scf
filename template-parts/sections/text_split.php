<?php
/**
 * Секция: заголовок в левой колонке, текст в правой.
 */
defined( 'ABSPATH' ) || exit;

$title = get_sub_field( 'title' );
$text  = get_sub_field( 'text' );

if ( ! $title && ! $text ) {
	return;
}
?>
<section class="text-split">
	<div class="text-split__inner">
		<?php if ( $title ) : ?>
			<h2 class="text-split__title"><?php echo esc_html( $title ); ?></h2>
		<?php endif; ?>

		<?php if ( $text ) : ?>
			<div class="text-split__text"><?php echo wp_kses_post( wpautop( $text ) ); ?></div>
		<?php endif; ?>
	</div>
</section>