<?php
/**
 * Категория проектов (/service/{slug}/).
 * Если у категории есть секции, выводим их. Если нет, простая страница: заголовок и мозаика проектов.
 */
get_header();

$term   = get_queried_object();
$source = tm_sections_source();

if ( $source && function_exists( 'have_rows' ) && have_rows( 'sections', $source ) ) {

	tm_render_sections( 'sections', $source );

} else {
	// Запасной вариант, пока секции не собраны.
	$pattern = array( 'tall', '', '', 'wide', '', 'tall', '', '', '', '', 'wide', '' );
	?>
	<section class="wk-hero">
		<div class="wk__inner">
			<h1 class="wk-hero__title"><?php echo esc_html( single_term_title( '', false ) ); ?></h1>
		</div>
	</section>

	<section class="wk-gallery">
		<div class="wk__inner">
			<?php if ( have_posts() ) : ?>
				<div class="wk-grid">
					<?php
					$i = 0;
					while ( have_posts() ) {
						the_post();
						get_template_part( 'template-parts/projects/work-card', null, array(
							'size' => $pattern[ $i % count( $pattern ) ],
						) );
						$i++;
					}
					?>
				</div>
				<?php the_posts_pagination( array( 'mid_size' => 1, 'prev_text' => '←', 'next_text' => '→', 'class' => 'wk-pages' ) ); ?>
			<?php else : ?>
				<p class="wk-empty"><?php echo esc_html( tm_t( 'Проєктів поки немає.', 'Проектов пока нет.' ) ); ?></p>
			<?php endif; ?>
		</div>
	</section>
	<?php
}

get_footer();