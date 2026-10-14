<?php
/**
 * Секция "Избранные проекты": слайдер с фото, подписью, описанием и миниатюрами.
 * Проекты выбираются в поле "projects" (Связь), данные берутся из самих проектов.
 */
defined( 'ABSPATH' ) || exit;

$eyebrow = get_sub_field( 'eyebrow' );
$title   = get_sub_field( 'title' );
$ids     = get_sub_field( 'projects' ) ?: array();

// Собираем данные каждого проекта в один массив, чтобы ниже выводить без лишних запросов.
$items = array();
foreach ( $ids as $id ) {
	if ( 'publish' !== get_post_status( $id ) ) {
		continue;
	}
	$thumb = get_post_thumbnail_id( $id );
	$label = get_field( 'project_label', $id );
	if ( ! $label ) {
		$cats  = get_the_terms( $id, 'project_category' );
		$label = ( $cats && ! is_wp_error( $cats ) ) ? $cats[0]->name : get_the_title( $id );
	}
	$items[] = array(
		'title' => get_the_title( $id ),
		'url'   => get_permalink( $id ),
		'tag'   => get_field( 'project_tag', $id ),
		'meta'  => implode( ' · ', array_filter( array(
			get_field( 'project_city', $id ),
			get_field( 'project_year', $id ),
			get_field( 'project_size', $id ),
		) ) ),
		'desc'  => has_excerpt( $id )
			? get_the_excerpt( $id )
			: wp_trim_words( wp_strip_all_tags( get_post_field( 'post_content', $id ) ), 30 ),
		'label' => $label,
		'thumb' => $thumb,
	);
}

if ( ! $items ) {
	return;
}

$total = count( $items );
$is_ru = function_exists( 'pll_current_language' ) && 'ru' === pll_current_language();
?>
<section class="fp">
	<div class="fp__inner">

		<div class="fp__head">
			<div>
				<?php if ( $eyebrow ) : ?>
					<div class="fp__eyebrow"><?php echo esc_html( $eyebrow ); ?></div>
				<?php endif; ?>
				<?php if ( $title ) : ?>
					<h2 class="fp__title"><?php echo esc_html( $title ); ?></h2>
				<?php endif; ?>
			</div>

			<?php if ( $total > 1 ) : ?>
				<div class="fp__nav">
					<div class="fp__counter">
						<span class="fp__counter-cur">01</span> / <?php echo esc_html( sprintf( '%02d', $total ) ); ?>
					</div>
					<button type="button" class="fp__arrow" data-dir="prev" aria-label="<?php echo esc_attr( $is_ru ? 'Предыдущий' : 'Попередній' ); ?>">←</button>
					<button type="button" class="fp__arrow" data-dir="next" aria-label="<?php echo esc_attr( $is_ru ? 'Следующий' : 'Наступний' ); ?>">→</button>
				</div>
			<?php endif; ?>
		</div>

		<div class="fp-stage">

			<div class="fp-media">
				<?php foreach ( $items as $i => $item ) : ?>
					<div class="fp-frame<?php echo 0 === $i ? ' is-active' : ''; ?>">
						<?php
						if ( $item['thumb'] ) {
							echo wp_get_attachment_image( $item['thumb'], 'large', false, array(
								'class'   => 'fp-frame__img',
								'sizes'   => '(max-width: 960px) 100vw, 740px',
								'loading' => 0 === $i ? 'eager' : 'lazy',
							) );
						}
						?>
					</div>
				<?php endforeach; ?>

				<div class="fp-media-scrim"></div>

				<div class="fp-caption">
					<?php foreach ( $items as $i => $item ) : ?>
						<div class="fp-caption__item<?php echo 0 === $i ? ' is-active' : ''; ?>">
							<?php if ( $item['tag'] ) : ?>
								<div class="fp-caption__tag"><?php echo esc_html( $item['tag'] ); ?></div>
							<?php endif; ?>
							<h3 class="fp-caption__title">
								<a href="<?php echo esc_url( $item['url'] ); ?>"><?php echo esc_html( $item['title'] ); ?></a>
							</h3>
							<?php if ( $item['meta'] ) : ?>
								<div class="fp-caption__meta"><?php echo esc_html( $item['meta'] ); ?></div>
							<?php endif; ?>
						</div>
					<?php endforeach; ?>
				</div>
			</div>

			<div class="fp-side">
				<?php foreach ( $items as $i => $item ) : ?>
					<p class="fp-desc<?php echo 0 === $i ? ' is-active' : ''; ?>"><?php echo esc_html( $item['desc'] ); ?></p>
				<?php endforeach; ?>

				<div class="fp-thumbs">
					<?php foreach ( $items as $i => $item ) : ?>
						<button type="button" class="fp-thumb<?php echo 0 === $i ? ' is-active' : ''; ?>" aria-label="<?php echo esc_attr( $item['title'] ); ?>">
							<?php
							if ( $item['thumb'] ) {
								echo wp_get_attachment_image( $item['thumb'], 'medium', false, array(
									'class'   => 'fp-thumb__img',
									'alt'     => '',
									'sizes'   => '300px',
									'loading' => 'lazy',
								) );
							}
							?>
							<span class="fp-thumb__label"><?php echo esc_html( $item['label'] ); ?></span>
						</button>
					<?php endforeach; ?>
				</div>
			</div>

		</div>
	</div>
</section>