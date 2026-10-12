<?php

/**
 * Секція "Кому підійдуть наші меблі": заголовок, картки з номерами, кнопка.
 */
defined('ABSPATH') || exit;

$title  = get_sub_field('title');
$tag    = get_sub_field('tag');
$cards  = get_sub_field('cards') ?: array();
$button = get_sub_field('button');
?>
<section class="who">
	<div class="who__inner">

		<?php if ($title) : ?>
			<h2 class="who__title"><?php echo esc_html($title); ?></h2>
		<?php endif; ?>

		<?php if ($cards) : ?>
			<div class="who-grid">
				<?php foreach ($cards as $i => $card) : ?>
					<article class="who-card">
						<div class="who-card__head">
							<span class="who-card__num"><?php echo esc_html(sprintf('%02d', $i + 1)); ?></span>
							<?php if ($tag) : ?>
								<span class="who-card__tag"><?php echo esc_html($tag); ?></span>
							<?php endif; ?>
						</div>
						<h3 class="who-card__title"><?php echo esc_html($card['title'] ?? ''); ?></h3>
						<p class="who-card__desc"><?php echo esc_html($card['desc'] ?? ''); ?></p>
					</article>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<?php if ($button) : ?>
			<div class="who__cta">
				<?php echo tm_link($button, 'btn btn--gold btn--lg'); ?>
			</div>
		<?php endif; ?>

	</div>
</section>