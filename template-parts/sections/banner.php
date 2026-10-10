<?php
/**
 * Секция "Баннер с фото": фото на всю ширину, затемнение слева, крошки, H1, текст, две кнопки.
 * Для страниц категорий и внутренних страниц.
 */
defined( 'ABSPATH' ) || exit;

$image  = get_sub_field( 'image' );
$title  = get_sub_field( 'title' ) ?: ( is_tax() ? single_term_title( '', false ) : get_the_title() );
$text   = get_sub_field( 'text' );
$btn_1  = get_sub_field( 'btn_primary' );
$btn_2  = get_sub_field( 'btn_secondary' );
$home   = function_exists( 'pll_home_url' ) ? pll_home_url() : home_url( '/' );
?>
<section class="banner">
	<div class="banner__box">

		<?php
		// tm_image() понимает и ID, и массив, так что формат возврата поля не важен.
		echo tm_image( $image, 'tm-hero', array(
			'class'         => 'banner__img',
			'sizes'         => '100vw',
			'loading'       => 'eager',
			'fetchpriority' => 'high',
		) );
		?>
		<span class="banner__shade" aria-hidden="true"></span>

		<div class="banner__content">
			<?php if ( function_exists( 'rank_math_the_breadcrumbs' ) ) : ?>
				<?php rank_math_the_breadcrumbs(); ?>
			<?php else : ?>
				<nav class="crumbs" aria-label="<?php echo esc_attr( tm_t( 'Навігаційний ланцюжок', 'Навигационная цепочка' ) ); ?>">
					<a href="<?php echo esc_url( $home ); ?>"><?php echo esc_html( tm_t( 'Головна', 'Главная' ) ); ?></a>
					<span aria-hidden="true">/</span>
					<?php if ( is_tax( 'project_category' ) ) : ?>
						<a href="<?php echo esc_url( get_post_type_archive_link( 'project' ) ); ?>"><?php echo esc_html( tm_t( 'Наші роботи', 'Наши работы' ) ); ?></a>
						<span aria-hidden="true">/</span>
						<span aria-current="page"><?php echo esc_html( single_term_title( '', false ) ); ?></span>
					<?php else : ?>
						<span aria-current="page"><?php echo esc_html( get_the_title() ); ?></span>
					<?php endif; ?>
				</nav>
			<?php endif; ?>

			<h1 class="banner__title"><?php echo esc_html( $title ); ?></h1>

			<?php if ( $text ) : ?>
				<p class="banner__text"><?php echo esc_html( $text ); ?></p>
			<?php endif; ?>

			<?php if ( $btn_1 || $btn_2 ) : ?>
				<div class="banner__actions">
					<?php echo tm_link( $btn_1, 'btn btn--gold btn--lg' ); ?>
					<?php echo tm_link( $btn_2, 'btn btn--ghost-light btn--lg' ); ?>
				</div>
			<?php endif; ?>
		</div>

	</div>
</section>