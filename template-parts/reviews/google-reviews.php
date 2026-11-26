<?php
/**
 * Блок "Відгуки з Google". Дані з "Налаштування сайту" → вкладка "Відгуки Google".
 * Можна передати власний заголовок: get_template_part( 'template-parts/reviews/google-reviews', null, array( 'title' => '...' ) ).
 * Немає жодного відгуку = блок не виводиться.
 */
defined('ABSPATH') || exit;

if (! function_exists('get_field')) {
	return;
}

$items = get_field('reviews_items', 'option');
if (empty($items) || ! is_array($items)) {
	return;
}

$rating = (float) (get_field('reviews_rating', 'option') ?: 5);
$count  = (int) get_field('reviews_count', 'option') ?: count($items);
$url    = get_field('reviews_url', 'option');
$title  = ! empty($args['title']) ? $args['title'] : tm_t('Що пишуть про нас у Google', 'Что пишут о нас в Google');

// "6 відгуків" / "6 отзывов": правильне закінчення для числа
$n    = $count % 100;
$form = ($n >= 11 && $n <= 14) ? 2 : (($count % 10 === 1) ? 0 : (($count % 10 >= 2 && $count % 10 <= 4) ? 1 : 2));
$word = tm_t(
	array('відгуку', 'відгуків', 'відгуків')[$form],
	array('отзыва', 'отзывов', 'отзывов')[$form]
);

// Значок "G" Google (однаковий у підсумку і в картках)
$g_icon = '<svg class="gr__g" viewBox="0 0 48 48" width="20" height="20" aria-hidden="true" focusable="false"><path fill="#FFC107" d="M43.6 20.1H42V20H24v8h11.3C33.7 32.7 29.2 36 24 36c-6.6 0-12-5.4-12-12s5.4-12 12-12c3.1 0 5.8 1.2 7.9 3.1l5.7-5.7C34 6.1 29.3 4 24 4 12.9 4 4 12.9 4 24s8.9 20 20 20 20-8.9 20-20c0-1.3-.1-2.6-.4-3.9z"/><path fill="#FF3D00" d="M6.3 14.7l6.6 4.8C14.7 15.1 19 12 24 12c3.1 0 5.8 1.2 7.9 3.1l5.7-5.7C34 6.1 29.3 4 24 4 16.3 4 9.7 8.3 6.3 14.7z"/><path fill="#4CAF50" d="M24 44c5.2 0 9.9-2 13.4-5.2l-6.2-5.2C29.2 35.1 26.7 36 24 36c-5.2 0-9.6-3.3-11.3-7.9l-6.5 5C9.5 39.6 16.2 44 24 44z"/><path fill="#1976D2" d="M43.6 20.1H42V20H24v8h11.3c-.8 2.2-2.2 4.2-4.1 5.6l6.2 5.2C37 39.2 44 34 44 24c0-1.3-.1-2.6-.4-3.9z"/></svg>';
?>
<section class="gr">
	<div class="gr__inner">

		<div class="gr__head">
			<div class="gr__eyebrow"><?php echo esc_html(tm_t('Відгуки клієнтів', 'Отзывы клиентов')); ?></div>
			<h2 class="gr__title"><?php echo esc_html($title); ?></h2>
		</div>

		<div class="gr__wrap">

			<?php $sum_tag = $url ? 'a' : 'div'; ?>
			<<?php echo $sum_tag; ?> class="gr__sum" <?php if ($url) : ?>href="<?php echo esc_url($url); ?>" target="_blank" rel="noopener" <?php endif; ?>>
				<span class="gr__score"><?php echo esc_html(number_format($rating, 1, ',', '')); ?></span>
				<span class="gr__stars" aria-label="<?php echo esc_attr(sprintf(tm_t('Рейтинг %s з 5', 'Рейтинг %s из 5'), number_format($rating, 1, ',', ''))); ?>">★★★★★</span>
				<span class="gr__total"><?php echo esc_html(sprintf(tm_t('На основі %1$d %2$s', 'На основе %1$d %2$s'), $count, $word)); ?></span>
				<span class="gr__brand"><?php echo $g_icon; // phpcs:ignore — статичний SVG ?> Google</span>
			</<?php echo $sum_tag; ?>>

			<div class="gr__slider">
				<button class="gr__arrow gr__arrow--prev" type="button" aria-label="<?php echo esc_attr(tm_t('Попередні відгуки', 'Предыдущие отзывы')); ?>">‹</button>

				<ul class="gr__track">
					<?php foreach ($items as $item) :
						$name = trim($item['name'] ?? '');
						$text = trim($item['text'] ?? '');
						if (! $name || ! $text) {
							continue;
						}
						$meta = implode(' · ', array_filter(array(trim($item['product'] ?? ''), trim($item['date'] ?? ''))));
					?>
						<li class="gr__card">
							<div class="gr__top">
								<span class="gr__ava" aria-hidden="true"><?php echo esc_html(mb_substr($name, 0, 1)); ?></span>
								<span class="gr__who">
									<span class="gr__name"><?php echo esc_html($name); ?></span>
									<?php if ($meta) : ?><span class="gr__meta"><?php echo esc_html($meta); ?></span><?php endif; ?>
								</span>
								<?php echo $g_icon; // phpcs:ignore ?>
							</div>
							<span class="gr__stars gr__stars--sm" aria-label="<?php echo esc_attr(tm_t('5 з 5', '5 из 5')); ?>">★★★★★</span>
							<p class="gr__text"><?php echo esc_html($text); ?></p>
							<?php if ($url) : ?>
								<a class="gr__more" href="<?php echo esc_url($url); ?>" target="_blank" rel="noopener"><?php echo esc_html(tm_t('Читати в Google', 'Читать в Google')); ?></a>
							<?php endif; ?>
						</li>
					<?php endforeach; ?>
				</ul>

				<button class="gr__arrow gr__arrow--next" type="button" aria-label="<?php echo esc_attr(tm_t('Наступні відгуки', 'Следующие отзывы')); ?>">›</button>
			</div>

		</div>

		<?php if ($url) : ?>
			<div class="gr__foot">
				<a class="gr__all" href="<?php echo esc_url($url); ?>" target="_blank" rel="noopener"><?php echo esc_html(tm_t('Усі відгуки в Google', 'Все отзывы в Google')); ?></a>
			</div>
		<?php endif; ?>

	</div>
</section>