<?php

/**
 * Страница 404: адрес не найден.
 */
defined('ABSPATH') || exit;

get_header();

$home = function_exists('pll_home_url') ? pll_home_url() : home_url('/');

// Страница "Контакты" на текущем языке
$contacts_url = '';
$contacts     = get_page_by_path('contacts');
if ($contacts) {
	$cid = function_exists('pll_get_post') ? (pll_get_post($contacts->ID) ?: $contacts->ID) : $contacts->ID;
	$contacts_url = get_permalink($cid);
}

// Категории работ (Polylang сам отдаст нужный язык)
$cats = get_terms(array(
	'taxonomy'   => 'project_category',
	'hide_empty' => true,
	'parent'     => 0,
));
?>

<section class="nf">
	<div class="nf__inner">

		<div class="nf__num" aria-hidden="true">4<em>0</em>4</div>

		<h1 class="nf__title"><?php echo esc_html(tm_t('Схоже, цієї сторінки немає', 'Похоже, этой страницы нет')); ?></h1>

		<p class="nf__text"><?php echo esc_html(tm_t(
								'Можливо, її перенесли або в адресі є помилка. Зате ми точно знаємо, де знайти гарні меблі.',
								'Возможно, её перенесли или в адресе есть ошибка. Зато мы точно знаем, где найти красивую мебель.'
							)); ?></p>

		<div class="nf__actions">
			<a class="nf__btn nf__btn--gold" href="<?php echo esc_url($home); ?>"><?php echo esc_html(tm_t('На головну', 'На главную')); ?></a>
			<?php if ($contacts_url) : ?>
				<a class="nf__btn nf__btn--ghost" href="<?php echo esc_url($contacts_url); ?>"><?php echo esc_html(tm_t('Зв’язатися з нами', 'Связаться с нами')); ?></a>
			<?php endif; ?>
		</div>

		<?php if ($cats && ! is_wp_error($cats)) : ?>
			<div class="nf__cats">
				<div class="nf__label"><?php echo esc_html(tm_t('Або оберіть категорію', 'Или выберите категорию')); ?></div>
				<div class="nf__grid">
					<?php foreach ($cats as $c) :
						$img = get_field('cat_image', 'term_' . $c->term_id);
					?>
						<a class="nf__card" href="<?php echo esc_url(get_term_link($c)); ?>">
							<?php
							if ($img) {
								echo tm_image($img, 'tm-card', array(
									'class'   => 'nf__img',
									'alt'     => '',
									'loading' => 'lazy',
									'sizes'   => '(max-width: 960px) 50vw, 240px',
								));
							}
							?>
							<span class="nf__scrim"></span>
							<span class="nf__name"><?php echo esc_html($c->name); ?></span>
						</a>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endif; ?>

	</div>
</section>

<?php
get_footer();
