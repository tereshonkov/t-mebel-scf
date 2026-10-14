<?php
/**
 * Страница статьи: /guides/{slug}/
 */
defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();

	$pid         = get_the_ID();
	$home        = function_exists( 'pll_home_url' ) ? pll_home_url() : home_url( '/' );
	$archive_url = get_post_type_archive_link( 'guide' );

	$terms = get_the_terms( $pid, 'guide_category' );
	$term  = ( $terms && ! is_wp_error( $terms ) ) ? $terms[0] : null;
	$mins  = tm_read_time( $pid );

	// ---- Текст статьи + оглавление из заголовков H2 + плашка в середине ----
	$content = apply_filters( 'the_content', get_the_content() );
	$toc     = array();
	$n       = 0;

	// Плашку ставим перед "средним" H2: при 7 заголовках перед 5-м, при 3 перед 3-м
	$cta_html   = tm_guide_cta_html( $term );
	$h2_total   = preg_match_all( '/<h2[\s>]/u', $content );
	$cta_before = ( $cta_html && $h2_total >= 2 ) ? (int) ceil( $h2_total / 2 ) + 1 : 0;

	$content = preg_replace_callback(
		'/<h2([^>]*)>(.*?)<\/h2>/su',
		function ( $m ) use ( &$toc, &$n, $cta_html, $cta_before ) {
			$n++;
			$attrs = $m[1];

			// Если у заголовка уже есть id (задан в редакторе как «Якорь HTML»), берём его
			if ( preg_match( '/\sid=["\']([^"\']+)["\']/u', $attrs, $id_match ) ) {
				$id = $id_match[1];
			} else {
				$id     = 's' . $n;
				$attrs .= ' id="' . $id . '"';
			}

			// Текст для оглавления: без тегов и без номера "1. " в начале
			$label = trim( preg_replace( '/^\d+[.)]\s*/u', '', wp_strip_all_tags( $m[2] ) ) );
			$toc[] = array( 'id' => $id, 'label' => $label );

			$h2 = '<h2' . $attrs . '>' . $m[2] . '</h2>';

			// Перед нужным заголовком вставляем плашку
			return ( $n === $cta_before ) ? $cta_html . $h2 : $h2;
		},
		$content
	);

	// Заголовков меньше двух: плашку ставим в конец текста
	if ( $cta_html && ! $cta_before ) {
		$content .= $cta_html;
	}
	?>

	<section class="ar-head">
		<div class="ar-head__inner">

			<nav class="crumbs" aria-label="<?php echo esc_attr( tm_t( 'Навігація', 'Навигация' ) ); ?>">
				<a href="<?php echo esc_url( $home ); ?>"><?php echo esc_html( tm_t( 'Головна', 'Главная' ) ); ?></a>
				<span aria-hidden="true">/</span>
				<a href="<?php echo esc_url( $archive_url ); ?>"><?php echo esc_html( tm_t( 'Корисні статті', 'Полезные статьи' ) ); ?></a>
				<?php if ( $term ) : ?>
					<span aria-hidden="true">/</span>
					<a href="<?php echo esc_url( get_term_link( $term ) ); ?>"><?php echo esc_html( $term->name ); ?></a>
				<?php endif; ?>
			</nav>

			<?php if ( $term ) : ?>
				<a class="ar-badge" href="<?php echo esc_url( get_term_link( $term ) ); ?>"><?php echo esc_html( $term->name ); ?></a>
			<?php endif; ?>

			<h1 class="ar-h1"><?php the_title(); ?></h1>

			<div class="ar-meta">
				<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date( 'j F Y' ) ); ?></time>
				<span class="ar-dot">·</span>
				<span><?php echo esc_html( $mins . ' ' . tm_t( 'хв читання', 'мин чтения' ) ); ?></span>
				<span class="ar-dot">·</span>
				<span><?php echo esc_html( tm_t( 'Команда T-Mebel', 'Команда T-Mebel' ) ); ?></span>
			</div>

		</div>
	</section>

	<?php if ( has_post_thumbnail() ) : ?>
		<section class="ar-hero">
			<div class="ar-hero__box">
				<?php the_post_thumbnail( 'tm-hero', array(
					'class'         => 'ar-hero__img',
					'sizes'         => '(max-width: 1240px) 100vw, 1200px',
					'loading'       => 'eager',
					'fetchpriority' => 'high',
				) ); ?>
			</div>
		</section>
	<?php endif; ?>

	<section class="ar-main">
		<div class="ar-layout<?php echo $toc ? '' : ' ar-layout--no-toc'; ?>">

			<?php if ( $toc ) : ?>
				<aside class="ar-toc">
					<div class="ar-toc__title"><?php echo esc_html( tm_t( 'Зміст', 'Содержание' ) ); ?></div>
					<?php foreach ( $toc as $item ) : ?>
						<a href="#<?php echo esc_attr( $item['id'] ); ?>"><?php echo esc_html( $item['label'] ); ?></a>
					<?php endforeach; ?>
				</aside>
			<?php endif; ?>

			<article class="ar-body">
				<?php echo $content; // phpcs:ignore — текст из редактора, уже прошёл фильтры WordPress ?>

				<div class="ar-foot">
					<a class="ar-back" href="<?php echo esc_url( $archive_url ); ?>"><?php echo esc_html( tm_t( '← Усі статті', '← Все статьи' ) ); ?></a>
				</div>
			</article>

		</div>
	</section>

	<?php
	// ---- Читайте також: 3 статьи из той же рубрики, иначе любые ----
	$base_args = array(
		'post_type'      => 'guide',
		'posts_per_page' => 3,
		'post__not_in'   => array( $pid ),
		'fields'         => 'ids',
		'no_found_rows'  => true,
	);

	$related_ids = array();

	if ( $term ) {
		$q = new WP_Query( array_merge( $base_args, array(
			'tax_query' => array(
				array(
					'taxonomy' => 'guide_category',
					'field'    => 'term_id',
					'terms'    => $term->term_id,
				),
			),
		) ) );
		$related_ids = $q->posts;
	}

	if ( count( $related_ids ) < 3 ) {
		$q = new WP_Query( array_merge( $base_args, array(
			'posts_per_page' => 3 - count( $related_ids ),
			'post__not_in'   => array_merge( array( $pid ), $related_ids ),
		) ) );
		$related_ids = array_merge( $related_ids, $q->posts );
	}

	if ( $related_ids ) :
		?>
		<section class="ar-related-sec">
			<div class="ar-related-wrap">
				<div class="ar-related-head">
					<h2 class="ar-related-title"><?php echo esc_html( tm_t( 'Читайте також', 'Читайте также' ) ); ?></h2>
					<a class="ar-related-all" href="<?php echo esc_url( $archive_url ); ?>"><?php echo esc_html( tm_t( 'Усі статті →', 'Все статьи →' ) ); ?></a>
				</div>

				<div class="ar-related">
					<?php foreach ( $related_ids as $rid ) : ?>
						<a class="ar-card" href="<?php echo esc_url( get_permalink( $rid ) ); ?>">
							<div class="ar-card__media">
								<?php echo get_the_post_thumbnail( $rid, 'tm-card', array(
									'class'   => 'ar-card__img',
									'loading' => 'lazy',
									'sizes'   => '(max-width: 760px) 100vw, (max-width: 960px) 50vw, 380px',
								) ); ?>
							</div>
							<div class="ar-card__meta">
								<?php
								$r_cat = tm_guide_cat_name( $rid );
								echo esc_html( ( $r_cat ? $r_cat . ' · ' : '' ) . tm_read_time( $rid ) . ' ' . tm_t( 'хв', 'мин' ) );
								?>
							</div>
							<h3 class="ar-card__title"><?php echo esc_html( get_the_title( $rid ) ); ?></h3>
						</a>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
		<?php
	endif;

	tm_render_layout_from( 'lead', tm_front_page_id() );

endwhile;

get_footer();