<?php
/**
 * Страница одного проекта: /project/...
 */
defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();

	$pid = get_the_ID();

	// Категория проекта: берём первую
	$terms = get_the_terms( $pid, 'project_category' );
	$term  = ( $terms && ! is_wp_error( $terms ) ) ? $terms[0] : null;

	$archive_url = get_post_type_archive_link( 'project' );
	$cat_url     = $term ? get_term_link( $term ) : $archive_url;
	$cat_name    = $term ? $term->name : '';

	// Текст кнопки "назад": из поля категории, иначе "Усі кухні"
	$back_label = '';
	if ( $term ) {
		$back_label = get_field( 'cat_back', 'term_' . $term->term_id );
		if ( ! $back_label ) {
			$back_label = tm_t( 'Усі ', 'Все ' ) . mb_strtolower( $cat_name );
		}
	}

	// Надзаголовок: тег проекта, например "Кухня · МДФ фарбований"
	$eyebrow = get_field( 'project_tag', $pid );

	// Фото: галерея, а если она пустая, то изображение записи
	$photos = get_field( 'gallery', $pid );
	$photos = is_array( $photos ) ? $photos : array();
	$photos = array_map(
		function ( $p ) {
			return is_array( $p ) ? (int) ( $p['ID'] ?? $p['id'] ?? 0 ) : (int) $p;
		},
		$photos
	);
	$photos = array_values( array_filter( $photos ) );

	if ( ! $photos && has_post_thumbnail() ) {
		$photos = array( get_post_thumbnail_id() );
	}

	$total = count( $photos );
	?>

	<section class="pj-head">
		<div class="pj-container">

			<nav class="pj-crumbs" aria-label="<?php echo esc_attr( tm_t( 'Навігація', 'Навигация' ) ); ?>">
				<a href="<?php echo esc_url( function_exists( 'pll_home_url' ) ? pll_home_url() : home_url( '/' ) ); ?>"><?php echo esc_html( tm_t( 'Головна', 'Главная' ) ); ?></a>
				<span>/</span>
				<a href="<?php echo esc_url( $archive_url ); ?>"><?php echo esc_html( tm_t( 'Наші роботи', 'Наши работы' ) ); ?></a>
				<?php if ( $term ) : ?>
                    <a href="<?php echo esc_url( $cat_url ); ?>"><?php echo esc_html( $cat_name ); ?></a>
				<?php endif; ?>
				<span>/</span>
				<span class="pj-crumbs__current"><?php the_title(); ?></span>
			</nav>

			<div class="pj-head__row">
				<div class="pj-head__text">
					<?php if ( $eyebrow ) : ?>
						<div class="pj-eyebrow"><?php echo esc_html( $eyebrow ); ?></div>
					<?php endif; ?>
					<h1 class="pj-title"><?php the_title(); ?></h1>
				</div>

				<?php if ( $term ) : ?>
                    <a class="pj-back" href="<?php echo esc_url( $cat_url ); ?>">← <?php echo esc_html( $back_label ); ?></a>
				<?php endif; ?>
			</div>

		</div>
	</section>

	<section class="pj-body">
		<div class="pj-container pj-grid">

			<div class="pj-gallery" data-pj-gallery>
				<?php if ( $total ) : ?>
					<div class="pj-main">
						<?php foreach ( $photos as $k => $img_id ) : ?>
							<?php
							echo tm_image(
								$img_id,
								'large',
								array(
									'class'         => 'pj-frame' . ( 0 === $k ? ' is-active' : '' ),
									'alt'           => get_the_title() . ' ' . ( $k + 1 ),
									'sizes'         => '(max-width: 960px) 100vw, 680px',
									'loading'       => 0 === $k ? 'eager' : 'lazy',
									'fetchpriority' => 0 === $k ? 'high' : 'auto',
								)
							);
							?>
						<?php endforeach; ?>

						<?php if ( $total > 1 ) : ?>
							<button type="button" class="pj-arrow pj-prev" data-pj-prev aria-label="<?php echo esc_attr( tm_t( 'Попереднє фото', 'Предыдущее фото' ) ); ?>">←</button>
							<button type="button" class="pj-arrow pj-next" data-pj-next aria-label="<?php echo esc_attr( tm_t( 'Наступне фото', 'Следующее фото' ) ); ?>">→</button>
							<div class="pj-count" data-pj-count>01 / <?php echo esc_html( sprintf( '%02d', $total ) ); ?></div>
						<?php endif; ?>
					</div>

					<?php if ( $total > 1 ) : ?>
						<div class="pj-thumbs">
							<?php foreach ( $photos as $k => $img_id ) : ?>
								<button type="button" class="pj-thumb<?php echo 0 === $k ? ' is-active' : ''; ?>" data-pj-thumb="<?php echo esc_attr( $k ); ?>" aria-label="<?php echo esc_attr( tm_t( 'Фото ', 'Фото ' ) . ( $k + 1 ) ); ?>">
									<?php echo tm_image( $img_id, 'thumbnail', array( 'alt' => '', 'loading' => 'lazy' ) ); ?>
								</button>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				<?php endif; ?>
			</div>

			<div class="pj-info">
				<div class="pj-desc"><?php the_content(); ?></div>

				<a class="pj-cta" href="#popup"><?php echo esc_html( tm_t( 'Замовити подібний проєкт', 'Заказать похожий проект' ) ); ?></a>
				<div class="pj-cta-note"><?php echo esc_html( tm_t( 'Безкоштовна консультація та розрахунок вартості', 'Бесплатная консультация и расчёт стоимости' ) ); ?></div>
			</div>

		</div>
	</section>
	<?php
	// ---- Схожі проєкти ----
	$base_args = array(
		'post_type'      => 'project',
		'posts_per_page' => 3,
		'post__not_in'   => array( $pid ),
		'orderby'        => 'rand',
		'fields'         => 'ids',
		'no_found_rows'  => true,
		'meta_query'     => array(
			array( 'key' => '_thumbnail_id', 'compare' => 'EXISTS' ),
		),
	);

	$related_ids = array();

	// 1) Из той же категории
	if ( $term ) {
		$q = new WP_Query( array_merge( $base_args, array(
			'tax_query' => array(
				array(
					'taxonomy' => 'project_category',
					'field'    => 'term_id',
					'terms'    => $term->term_id,
				),
			),
		) ) );
		$related_ids = $q->posts;
	}

	// 2) Если не хватило, добираем из любых проектов
	if ( count( $related_ids ) < 3 ) {
		$q = new WP_Query( array_merge( $base_args, array(
			'posts_per_page' => 3 - count( $related_ids ),
			'post__not_in'   => array_merge( array( $pid ), $related_ids ),
		) ) );
		$related_ids = array_merge( $related_ids, $q->posts );
	}

	if ( $related_ids ) :
		?>
		<section class="pj-related-sec">
			<div class="pj-container">

				<div class="pj-related-head">
					<h2 class="pj-related-title"><?php echo esc_html( tm_t( 'Схожі проєкти', 'Похожие проекты' ) ); ?></h2>
					<a class="pj-related-all" href="<?php echo esc_url( $archive_url ); ?>"><?php echo esc_html( tm_t( 'Усі роботи →', 'Все работы →' ) ); ?></a>
				</div>

				<div class="pj-related">
					<?php foreach ( $related_ids as $rid ) :
						$r_terms = get_the_terms( $rid, 'project_category' );
						$r_tag   = ( $r_terms && ! is_wp_error( $r_terms ) ) ? $r_terms[0]->name : '';
						?>
						<a class="pj-card" href="<?php echo esc_url( get_permalink( $rid ) ); ?>">
							<?php
							echo get_the_post_thumbnail( $rid, 'tm-card', array(
								'class'   => 'pj-card__img',
								'loading' => 'lazy',
								'sizes'   => '(max-width: 760px) 100vw, (max-width: 960px) 50vw, 400px',
							) );
							?>
							<span class="pj-card__scrim"></span>
							<span class="pj-card__body">
								<?php if ( $r_tag ) : ?>
									<span class="pj-card__tag"><?php echo esc_html( $r_tag ); ?></span>
								<?php endif; ?>
								<span class="pj-card__title"><?php echo esc_html( get_the_title( $rid ) ); ?></span>
							</span>
						</a>
					<?php endforeach; ?>
				</div>

			</div>
		</section>
		<?php
	endif;
	?>
    	<?php tm_render_layout_from( 'lead', tm_front_page_id() ); ?>
	<?php
endwhile;

get_footer();