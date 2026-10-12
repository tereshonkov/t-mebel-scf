<?php

/**
 * Секция "Форма (карточка)": заголовок и текст слева, форма CF7 справа, на бежевом фоне.
 */
defined('ABSPATH') || exit;

$title = get_sub_field('title');
$text  = get_sub_field('text');
$form  = get_sub_field('form');

if (! $form) {
	return;
}
?>
<section class="cform" id="lead">
	<div class="cform__card">
		<div class="cform__text">
			<?php if ($title) : ?>
				<h2 class="cform__title"><?php echo esc_html($title); ?></h2>
			<?php endif; ?>
			<?php if ($text) : ?>
				<p class="cform__p"><?php echo esc_html($text); ?></p>
			<?php endif; ?>
		</div>
		<div class="cform__form">
			<?php echo do_shortcode($form); // phpcs:ignore 
			?>
		</div>
	</div>
</section>