<?php

/**
 * Секция "Преимущества": строка из 4 коротких пунктов с золотой чертой слева.
 */
defined('ABSPATH') || exit;

$items = get_sub_field('items') ?: array();
if (! $items) {
	return;
}
?>
<section class="benefits">
	<ul class="benefits__list">
		<?php foreach ($items as $item) : ?>
			<li class="benefits__item">
				<span class="benefits__title"><?php echo esc_html($item['title'] ?? ''); ?></span>
				<span class="benefits__desc"><?php echo esc_html($item['desc'] ?? ''); ?></span>
			</li>
		<?php endforeach; ?>
	</ul>
</section>