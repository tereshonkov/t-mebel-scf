<?php

/**
 * Секция: полоса с крупными цифрами (20+, 100+...).
 */
defined('ABSPATH') || exit;

$items = get_sub_field('items');
if (empty($items)) {
	return;
}
?>
<section class="stats-band">
	<ul class="stats-band__list">
		<?php foreach ($items as $item) : ?>
			<li class="stats-band__item">
				<span class="stats-band__value"><?php echo esc_html($item['value'] ?? ''); ?></span>
				<span class="stats-band__label"><?php echo esc_html($item['label'] ?? ''); ?></span>
			</li>
		<?php endforeach; ?>
	</ul>
</section>