<?php
/**
 * Секция: заголовок + сетка карточек с автоматическими номерами.
 */

$eyebrow = get_sub_field( 'eyebrow' );
$title   = get_sub_field( 'title' );
$items   = get_sub_field( 'items' );
$light   = get_sub_field( 'light' );

if ( empty( $items ) ) {
	return;
}
?>
<section class="cards<?php echo $light ? ' cards--light' : ''; ?>">
	<div class="cards__inner">

		<?php if ( $eyebrow || $title ) : ?>
			<div class="cards__head">
				<?php if ( $eyebrow ) : ?>
					<div class="cards__eyebrow"><?php echo esc_html( $eyebrow ); ?></div>
				<?php endif; ?>
				<?php if ( $title ) : ?>
					<h2 class="cards__title"><?php echo esc_html( $title ); ?></h2>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<ul class="cards__list">
			<?php foreach ( $items as $n => $item ) : ?>
				<li class="cards__item">
					<span class="cards__num"><?php echo esc_html( sprintf( '%02d', $n + 1 ) ); ?></span>
					<?php if ( ! empty( $item['title'] ) ) : ?>
						<h3 class="cards__name"><?php echo esc_html( $item['title'] ); ?></h3>
					<?php endif; ?>
					<?php if ( ! empty( $item['desc'] ) ) : ?>
						<p class="cards__desc"><?php echo esc_html( $item['desc'] ); ?></p>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>

	</div>
</section>