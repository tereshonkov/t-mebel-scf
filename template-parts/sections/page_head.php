<?php

/**
 * Секция "Заголовок страницы": хлебные крошки, надзаголовок, H1, вводный текст.
 * Для внутренних страниц (Контакты, О нас и т.д.).
 */
defined('ABSPATH') || exit;

$eyebrow = get_sub_field('eyebrow');
$title   = get_sub_field('title') ?: get_the_title();
$lead    = get_sub_field('lead');
$home    = function_exists('pll_home_url') ? pll_home_url() : home_url('/');
$split   = get_sub_field('split');
?>
<section class="page-head<?php echo $split ? ' page-head--split' : ''; ?>">
	<div class="page-head__inner">

		<?php if (function_exists('rank_math_the_breadcrumbs')) : ?>
			<?php rank_math_the_breadcrumbs(); ?>
		<?php else : ?>
			<nav class="crumbs" aria-label="<?php echo esc_attr(tm_t('Навігаційний ланцюжок', 'Навигационная цепочка')); ?>">
				<a href="<?php echo esc_url($home); ?>"><?php echo esc_html(tm_t('Головна', 'Главная')); ?></a>
				<span aria-hidden="true">/</span>
				<span aria-current="page"><?php echo esc_html(get_the_title()); ?></span>
			</nav>
		<?php endif; ?>

		<div class="page-head__text">
			<?php if ($eyebrow) : ?>
				<div class="page-head__eyebrow"><?php echo esc_html($eyebrow); ?></div>
			<?php endif; ?>
			<h1 class="page-head__title"><?php echo esc_html($title); ?></h1>
			<?php if ($lead) : ?>
				<p class="page-head__lead"><?php echo esc_html($lead); ?></p>
			<?php endif; ?>
		</div>

	</div>
</section>