<?php
/**
 * Секция: галерея проектов текущей категории (masonry).
 * Фото берутся из "Изображения записи" каждого проекта.
 */

$title = get_sub_field( 'title' );
$limit = (int) get_sub_field( 'limit' );
if ( $limit < 1 ) {
	$limit = 6;
}

// Какая категория сейчас открыта. На обычной странице будет null.
$term = is_tax( 'project_category' ) ? get_queried_object() : null;

$args = array(
	'post_type'      => 'project',
	'posts_per_page' => $limit,
	'orderby'        => array( 'menu_order' => 'ASC', 'date' => 'DESC' ),
	'no_found_rows'  => true,                 // пагинация не нужна, запрос легче
	'meta_query'     => array(                // только проекты, у которых есть фото
		array( 'key' => '_thumbnail_id', 'compare' => 'EXISTS' ),
	),
);

if ( $term ) {
	$args['tax_query'] = array(
		array(
			'taxonomy' => 'project_category',
			'field'    => 'term_id',
			'terms'    => $term->term_id,
		),
	);
}

$q = new WP_Query( $args );

if ( ! $q->have_posts() ) {
	return; // нет проектов, секцию не выводим вообще
}

// Узор размеров: на 6 карточек ровно 3 полных ряда по 3 колонки.
$pattern = array( 'tall', '', 'wide', '', '', 'wide' );
$i       = 0;
?>
<section class="cat-gallery" id="gallery">
	<?php if ( $title ) : ?>
		<h2 class="cat-gallery__title"><?php echo esc_html( $title ); ?></h2>
	<?php endif; ?>

	<div class="cat-gallery__grid">
		<?php while ( $q->have_posts() ) : $q->the_post();
			$size = $pattern[ $i % count( $pattern ) ];
			$i++;
			?>
			<a class="kg-card <?php echo esc_attr( $size ); ?>"
			   href="<?php the_permalink(); ?>"
			   aria-label="<?php echo esc_attr( get_the_title() ); ?>">
				<?php the_post_thumbnail( 'large', array(
					'class' => 'kg-img',
					'sizes' => '(max-width: 900px) 50vw, 33vw',
					'alt'   => get_the_title(),
				) ); ?>
			</a>
		<?php endwhile; ?>
	</div>
</section>
<?php wp_reset_postdata(); ?>