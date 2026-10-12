<?php

/**
 * Секция: фото слева, заголовок + текст + кнопка справа, в белой карточке.
 */
defined('ABSPATH') || exit;

$image     = get_sub_field('image');
$title     = get_sub_field('title');
$text      = get_sub_field('text');
$btn_label = get_sub_field('btn_label');
$btn_url   = trim((string) get_sub_field('btn_url'));

// Пустая ссылка: ведём на архив проектов (Polylang сам добавит /ru/)
if (! $btn_url) {
	$btn_url = get_post_type_archive_link('project');
}
?>
<section class="media-text">
	<div class="media-text__card">

		<div class="media-text__media">
			<?php
			if ($image) {
				echo tm_image($image, 'large', array(
					'class'   => 'media-text__img',
					'sizes'   => '(max-width: 900px) 100vw, 600px',
					'loading' => 'lazy',
				));
			}
			?>
		</div>

		<div class="media-text__body">
			<?php if ($title) : ?>
				<h2 class="media-text__title"><?php echo esc_html($title); ?></h2>
			<?php endif; ?>

			<?php if ($text) : ?>
				<div class="media-text__text"><?php echo wp_kses_post(wpautop($text)); ?></div>
			<?php endif; ?>

			<?php if ($btn_label) : ?>
				<a class="media-text__btn" href="<?php echo esc_url($btn_url); ?>"><?php echo esc_html($btn_label); ?></a>
			<?php endif; ?>
		</div>

	</div>
</section>