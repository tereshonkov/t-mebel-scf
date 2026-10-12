<?php

/**
 * Карточка проекта в мозаике: фото на весь блок, затемнение, подписи снизу.
 * Вызывается внутри цикла. $args['size']: '' (обычная), 'tall' (2 ряда) или 'wide' (2 колонки).
 */
defined('ABSPATH') || exit;

$size = $args['size'] ?? '';
$id   = get_the_ID();
$cats = get_the_terms($id, 'project_category');
$tag  = ($cats && ! is_wp_error($cats)) ? $cats[0]->name : '';
$meta = implode(' · ', array_filter(array(
	get_field('project_city', $id),
	get_field('project_year', $id),
)));
?>
<a class="wk-card<?php echo $size ? ' ' . esc_attr($size) : ''; ?>" href="<?php the_permalink(); ?>">
	<?php
	if (has_post_thumbnail()) {
		the_post_thumbnail('large', array(
			'class'   => 'wk-img',
			'sizes'   => '(max-width: 560px) 100vw, (max-width: 900px) 50vw, 33vw',
			'loading' => 'lazy',
		));
	}
	?>
	<span class="wk-scrim" aria-hidden="true"></span>
	<span class="wk-body">
		<?php if ($tag) : ?>
			<span class="wk-tag"><?php echo esc_html($tag); ?></span>
		<?php endif; ?>
		<h2 class="wk-title"><?php the_title(); ?></h2>
		<?php if ($meta) : ?>
			<span class="wk-meta"><?php echo esc_html($meta); ?></span>
		<?php endif; ?>
	</span>
</a>