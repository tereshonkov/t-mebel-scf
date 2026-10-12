<?php

/**
 * "Наши работы" (/service/): архив всех проектов.
 * Тексты со страницы опций "Наши работы" (поля works_*_ua / works_*_ru).
 */
get_header();

$eyebrow = tm_opt('works_eyebrow');
$title   = tm_opt('works_title', tm_t('Наші роботи', 'Наши работы'));
$intro   = tm_opt('works_intro');
$stats   = tm_opt('works_stats', array());
$home    = function_exists('pll_home_url') ? pll_home_url() : home_url('/');
$all_url = get_post_type_archive_link('project');
$terms   = get_terms(array('taxonomy' => 'project_category', 'hide_empty' => true));

// Узор мозаики на 12 карточек, как в макете: какие высокие (tall), какие широкие (wide).
$pattern = array('tall', '', '', 'wide', '', 'tall', '', '', '', '', 'wide', '');
?>

<section class="wk-hero">
	<div class="wk__inner">

		<?php if (function_exists('rank_math_the_breadcrumbs')) : ?>
			<?php rank_math_the_breadcrumbs(); ?>
		<?php else : ?>
			<nav class="crumbs" aria-label="<?php echo esc_attr(tm_t('Навігаційний ланцюжок', 'Навигационная цепочка')); ?>">
				<a href="<?php echo esc_url($home); ?>"><?php echo esc_html(tm_t('Головна', 'Главная')); ?></a>
				<span aria-hidden="true">/</span>
				<span aria-current="page"><?php echo esc_html(tm_t('Наші роботи', 'Наши работы')); ?></span>
			</nav>
		<?php endif; ?>

		<div class="wk-hero__grid">
			<div>
				<?php if ($eyebrow) : ?>
					<div class="wk-eyebrow"><?php echo esc_html($eyebrow); ?></div>
				<?php endif; ?>
				<h1 class="wk-hero__title"><?php echo esc_html($title); ?></h1>
			</div>
			<?php if ($intro) : ?>
				<p class="wk-hero__intro"><?php echo esc_html($intro); ?></p>
			<?php endif; ?>
		</div>

	</div>
</section>

<?php if ($stats && is_array($stats)) : ?>
	<section class="wk-stats-wrap">
		<div class="wk__inner">
			<div class="wk-stats">
				<?php foreach ($stats as $st) : ?>
					<div class="wk-stat">
						<div class="wk-stat__value"><?php echo esc_html($st['value'] ?? ''); ?></div>
						<div class="wk-stat__label"><?php echo esc_html($st['label'] ?? ''); ?></div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
<?php endif; ?>

<section class="wk-gallery">
	<div class="wk__inner">

		<?php if ($terms && ! is_wp_error($terms)) : ?>
			<nav class="wk-filters" aria-label="<?php echo esc_attr(tm_t('Категорії', 'Категории')); ?>">
				<a class="wk-filter is-active" href="<?php echo esc_url($all_url); ?>" aria-current="page"><?php echo esc_html(tm_t('Усі', 'Все')); ?></a>
				<?php foreach ($terms as $t) : ?>
					<a class="wk-filter" href="<?php echo esc_url(get_term_link($t)); ?>"><?php echo esc_html($t->name); ?></a>
				<?php endforeach; ?>
			</nav>
		<?php endif; ?>

		<?php if (have_posts()) : ?>
			<div class="wk-grid">
				<?php
				$i = 0;
				while (have_posts()) {
					the_post();
					get_template_part('template-parts/projects/work-card', null, array(
						'size' => $pattern[$i % count($pattern)],
					));
					$i++;
				}
				?>
			</div>

			<?php
			the_posts_pagination(array(
				'mid_size'           => 1,
				'prev_text'          => '←',
				'next_text'          => '→',
				'screen_reader_text' => tm_t('Сторінки', 'Страницы'),
				'class'              => 'wk-pages',
			));
			?>
		<?php else : ?>
			<p class="wk-empty"><?php echo esc_html(tm_t('Проєктів поки немає.', 'Проектов пока нет.')); ?></p>
		<?php endif; ?>

	</div>
</section>

<?php
$seo_title = tm_opt('works_seo_title');
$seo_text  = tm_opt('works_seo_text');
$cta_title = tm_opt('works_cta_title');
$cta_text  = tm_opt('works_cta_text');
$cta_form  = tm_opt('works_cta_form');
?>

<?php if ($seo_title || $seo_text) : ?>
	<section class="wk-seo">
		<div class="wk-seo__inner">
			<?php if ($seo_title) : ?>
				<h2 class="wk-seo__title"><?php echo esc_html($seo_title); ?></h2>
			<?php endif; ?>
			<?php if ($seo_text) : ?>
				<div class="wk-seo__text"><?php echo wp_kses_post(wpautop($seo_text)); ?></div>
			<?php endif; ?>

			<?php if ($terms && ! is_wp_error($terms)) : ?>
				<div class="wk-seo__links">
					<?php foreach ($terms as $t) : ?>
						<a class="wk-pill" href="<?php echo esc_url(get_term_link($t)); ?>">
							<?php echo esc_html(get_field('cat_full', $t) ?: $t->name); ?>
						</a>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
	</section>
<?php endif; ?>

<?php if ($cta_form) : ?>
	<section class="cform cform--light" id="lead">
		<div class="cform__card">
			<div class="cform__text">
				<?php if ($cta_title) : ?>
					<h2 class="cform__title"><?php echo esc_html($cta_title); ?></h2>
				<?php endif; ?>
				<?php if ($cta_text) : ?>
					<p class="cform__p"><?php echo esc_html($cta_text); ?></p>
				<?php endif; ?>
			</div>
			<div class="cform__form"><?php echo do_shortcode($cta_form); // phpcs:ignore 
										?></div>
		</div>
	</section>
<?php endif; ?>

<?php
get_footer();
