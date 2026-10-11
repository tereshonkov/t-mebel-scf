<?php
/**
 * Список статей: /guides/ и /guides/topic/{рубрика}/
 */
defined( 'ABSPATH' ) || exit;

get_header();

$home        = function_exists( 'pll_home_url' ) ? pll_home_url() : home_url( '/' );
$archive_url = get_post_type_archive_link( 'guide' );
$cur_term    = is_tax( 'guide_category' ) ? get_queried_object() : null;

$eyebrow = tm_opt( 'guides_eyebrow', 'Блог' );
$title   = tm_opt( 'guides_title', tm_t( 'Корисні статті', 'Полезные статьи' ) );
$intro   = tm_opt( 'guides_intro', '' );

// На странице рубрики: H1 = название рубрики, текст = описание рубрики (если есть)
if ( $cur_term ) {
	$title = tm_t( 'Статті: ', 'Статьи: ' ) . $cur_term->name;
	$intro = $cur_term->description ?: $intro;
}

$terms = get_terms( array(
	'taxonomy'   => 'guide_category',
	'hide_empty' => true,
) );
?>

<section class="bl-head">
	<div class="bl-container">

		<nav class="crumbs" aria-label="<?php echo esc_attr( tm_t( 'Навігація', 'Навигация' ) ); ?>">
			<a href="<?php echo esc_url( $home ); ?>"><?php echo esc_html( tm_t( 'Головна', 'Главная' ) ); ?></a>
			<span aria-hidden="true">/</span>
			<?php if ( $cur_term ) : ?>
				<a href="<?php echo esc_url( $archive_url ); ?>"><?php echo esc_html( tm_t( 'Корисні статті', 'Полезные статьи' ) ); ?></a>
				<span aria-hidden="true">/</span>
				<span aria-current="page"><?php echo esc_html( $cur_term->name ); ?></span>
			<?php else : ?>
				<span aria-current="page"><?php echo esc_html( tm_t( 'Корисні статті', 'Полезные статьи' ) ); ?></span>
			<?php endif; ?>
		</nav>

		<div class="bl-head__text">
			<?php if ( $eyebrow ) : ?>
				<div class="bl-eyebrow"><?php echo esc_html( $eyebrow ); ?></div>
			<?php endif; ?>
			<h1 class="bl-h1"><?php echo esc_html( $title ); ?></h1>
			<?php if ( $intro ) : ?>
				<p class="bl-intro"><?php echo esc_html( $intro ); ?></p>
			<?php endif; ?>
		</div>

		<?php if ( $terms && ! is_wp_error( $terms ) ) : ?>
			<div class="bl-chips">
				<a class="bl-chip<?php echo $cur_term ? '' : ' is-active'; ?>" href="<?php echo esc_url( $archive_url ); ?>"><?php echo esc_html( tm_t( 'Усі статті', 'Все статьи' ) ); ?></a>
				<?php foreach ( $terms as $t ) : ?>
					<a class="bl-chip<?php echo ( $cur_term && $cur_term->term_id === $t->term_id ) ? ' is-active' : ''; ?>" href="<?php echo esc_url( get_term_link( $t ) ); ?>"><?php echo esc_html( $t->name ); ?></a>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

	</div>
</section>

<section class="bl-list">
	<div class="bl-container">

		<?php if ( have_posts() ) : ?>
			<?php
			$i = 0;
			while ( have_posts() ) :
				the_post();
				$cat  = tm_guide_cat_name();
				$mins = tm_read_time();

				// Первая статья: большая карточка
				if ( 0 === $i ) :
					?>
					<a class="bl-feat" href="<?php the_permalink(); ?>">
						<div class="bl-feat__media bl-img-wrap">
							<?php the_post_thumbnail( 'large', array( 'class' => 'bl-img', 'sizes' => '(max-width: 960px) 100vw, 660px', 'loading' => 'eager' ) ); ?>
						</div>
						<div class="bl-feat__body">
							<div class="bl-meta">
								<?php if ( $cat ) : ?>
									<span class="bl-badge"><?php echo esc_html( $cat ); ?></span>
								<?php endif; ?>
								<span class="bl-read"><?php echo esc_html( $mins . ' ' . tm_t( 'хв читання', 'мин чтения' ) ); ?></span>
							</div>
							<h2 class="bl-title bl-title--lg"><?php the_title(); ?></h2>
							<p class="bl-excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 28 ) ); ?></p>
							<span class="bl-more"><?php echo esc_html( tm_t( 'Читати статтю →', 'Читать статью →' ) ); ?></span>
						</div>
					</a>
					<div class="bl-grid">
					<?php
				// Остальные: обычные карточки
				else :
					?>
					<a class="bl-card" href="<?php the_permalink(); ?>">
						<div class="bl-card__media bl-img-wrap">
							<?php the_post_thumbnail( 'tm-card', array( 'class' => 'bl-img', 'sizes' => '(max-width: 760px) 100vw, (max-width: 960px) 50vw, 380px', 'loading' => 'lazy' ) ); ?>
						</div>
						<div class="bl-meta bl-meta--sm">
							<?php if ( $cat ) : ?>
								<span class="bl-cat"><?php echo esc_html( $cat ); ?></span>
								<span class="bl-dot">·</span>
							<?php endif; ?>
							<span class="bl-read"><?php echo esc_html( $mins . ' ' . tm_t( 'хв', 'мин' ) ); ?></span>
						</div>
						<h2 class="bl-title"><?php the_title(); ?></h2>
						<p class="bl-excerpt bl-excerpt--sm"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 18 ) ); ?></p>
					</a>
					<?php
				endif;

				$i++;
			endwhile;
			?>
			</div><!-- /.bl-grid -->

			<?php
			$pages = paginate_links( array(
				'type'      => 'array',
				'prev_text' => '←',
				'next_text' => '→',
			) );
			if ( $pages ) :
				?>
				<nav class="bl-pages" aria-label="<?php echo esc_attr( tm_t( 'Сторінки', 'Страницы' ) ); ?>">
					<?php foreach ( $pages as $p ) { echo $p; } // phpcs:ignore ?>
				</nav>
			<?php endif; ?>

		<?php else : ?>
			<p class="bl-empty"><?php echo esc_html( tm_t( 'Статей поки немає.', 'Статей пока нет.' ) ); ?></p>
		<?php endif; ?>

	</div>
</section>

<?php tm_render_layout_from( 'lead', tm_front_page_id() ); ?>

<?php
get_footer();