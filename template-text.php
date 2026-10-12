<?php

/**
 * Template Name: Текстовая страница
 * Для политики конфиденциальности и других длинных текстов:
 * крошки, H1, дата редакции, оглавление из заголовков H2.
 */
defined('ABSPATH') || exit;

get_header();

while (have_posts()) :
	the_post();

	$home = function_exists('pll_home_url') ? pll_home_url() : home_url('/');

	// Текст + оглавление из H2 (тот же приём, что в статьях)
	$content = apply_filters('the_content', get_the_content());
	$toc     = array();
	$n       = 0;

	$content = preg_replace_callback(
		'/<h2([^>]*)>(.*?)<\/h2>/su',
		function ($m) use (&$toc, &$n) {
			$n++;
			$attrs = $m[1];

			if (preg_match('/\sid=["\']([^"\']+)["\']/u', $attrs, $id_match)) {
				$id = $id_match[1];
			} else {
				$id     = 'p' . $n;
				$attrs .= ' id="' . $id . '"';
			}

			$label = trim(preg_replace('/^\d+[.)]\s*/u', '', wp_strip_all_tags($m[2])));
			$toc[] = array('id' => $id, 'label' => $label);

			return '<h2' . $attrs . '>' . $m[2] . '</h2>';
		},
		$content
	);
?>

	<section class="tp-head">
		<div class="tp-wrap">
			<nav class="crumbs" aria-label="<?php echo esc_attr(tm_t('Навігація', 'Навигация')); ?>">
				<a href="<?php echo esc_url($home); ?>"><?php echo esc_html(tm_t('Головна', 'Главная')); ?></a>
				<span aria-hidden="true">/</span>
				<span aria-current="page"><?php the_title(); ?></span>
			</nav>

			<h1 class="tp-h1"><?php the_title(); ?></h1>

			<div class="tp-date">
				<?php echo esc_html(tm_t('Редакція від ', 'Редакция от ') . get_the_modified_date('j F Y')); ?>
			</div>
		</div>
	</section>

	<section class="tp-main">
		<div class="tp-layout<?php echo $toc ? '' : ' tp-layout--no-toc'; ?>">

			<?php if ($toc) : ?>
				<aside class="tp-toc">
					<div class="tp-toc__title"><?php echo esc_html(tm_t('Зміст', 'Содержание')); ?></div>
					<?php foreach ($toc as $item) : ?>
						<a href="#<?php echo esc_attr($item['id']); ?>"><?php echo esc_html($item['label']); ?></a>
					<?php endforeach; ?>
				</aside>
			<?php endif; ?>

			<article class="tp-body">
				<?php echo $content; // phpcs:ignore — текст из редактора 
				?>
			</article>

		</div>
	</section>

<?php
endwhile;

get_footer();
