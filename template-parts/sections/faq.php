<?php
/**
 * Секция "Вопросы и ответы" на нативном <details>: раскрытие без JS.
 */
defined( 'ABSPATH' ) || exit;

$title    = get_sub_field( 'title' );
$subtitle = get_sub_field( 'subtitle' );
$items    = get_sub_field( 'items' ) ?: array();

if ( ! $items ) {
	return;
}
?>
<section class="faq">
	<div class="faq__inner">

		<div class="faq__head">
			<?php if ( $title ) : ?>
				<h2 class="faq__title"><?php echo esc_html( $title ); ?></h2>
			<?php endif; ?>
			<?php if ( $subtitle ) : ?>
				<p class="faq__sub"><?php echo esc_html( $subtitle ); ?></p>
			<?php endif; ?>
		</div>

		<div class="faq__list">
			<?php foreach ( $items as $item ) : ?>
				<?php if ( empty( $item['question'] ) ) continue; ?>
				<details class="faq-item">
					<summary><?php echo esc_html( $item['question'] ); ?></summary>
					<div class="faq-item__answer">
						<?php echo nl2br( esc_html( $item['answer'] ?? '' ) ); ?>
					</div>
				</details>
			<?php endforeach; ?>
		</div>

	</div>
</section>