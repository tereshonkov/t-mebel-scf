<?php

/**
 * Секция: факторы цены слева, карточка с формой CF7 справа.
 */

$eyebrow    = get_sub_field('eyebrow');
$title      = get_sub_field('title');
$text       = get_sub_field('text');
$form_title = get_sub_field('form_title');
$form_text  = get_sub_field('form_text');
$form_id    = trim((string) get_sub_field('form_id'));

// Textarea -> массив строк: режем по переносам, убираем пробелы и пустые строки.
$factors = preg_split('/\R/u', (string) get_sub_field('factors'));
$factors = array_filter(array_map('trim', $factors));
?>
<section class="price-form" id="lead">
	<div class="price-form__inner">

		<div class="price-form__info">
			<?php if ($eyebrow) : ?>
				<div class="price-form__eyebrow"><?php echo esc_html($eyebrow); ?></div>
			<?php endif; ?>

			<?php if ($title) : ?>
				<h2 class="price-form__title"><?php echo esc_html($title); ?></h2>
			<?php endif; ?>

			<?php if ($text) : ?>
				<p class="price-form__text"><?php echo esc_html($text); ?></p>
			<?php endif; ?>

			<?php if ($factors) : ?>
				<ul class="price-form__list">
					<?php foreach ($factors as $factor) : ?>
						<li><?php echo esc_html($factor); ?></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>

		<?php if ($form_id) : ?>
			<div class="price-form__card">
				<?php if ($form_title) : ?>
					<h3 class="price-form__card-title"><?php echo esc_html($form_title); ?></h3>
				<?php endif; ?>

				<?php if ($form_text) : ?>
					<p class="price-form__card-text"><?php echo esc_html($form_text); ?></p>
				<?php endif; ?>

				<?php echo do_shortcode('[contact-form-7 id="' . esc_attr($form_id) . '"]'); ?>
			</div>
		<?php endif; ?>

	</div>
</section>