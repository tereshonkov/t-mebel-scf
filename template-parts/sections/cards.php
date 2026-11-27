<?php
/**
 * Секция: заголовок + сетка карточек с автоматическими номерами.
 * Вигляд (поле view):
 * - plain:  обычные белые карточки;
 * - swatch: картинка-образец сверху карточки (например, фасады);
 * - photo:  фото на всю карточку, текст поверх (например, планировки).
 * Карточка без картинки в любом виде выводится как обычная.
 */

$eyebrow = get_sub_field( 'eyebrow' );
$title   = get_sub_field( 'title' );
$items   = get_sub_field( 'items' );
$view    = get_sub_field( 'view' ) ?: 'plain';

if ( empty( $items ) ) {
	return;
}

if ( ! in_array( $view, array( 'plain', 'swatch', 'photo' ), true ) ) {
	$view = 'plain';
}
?>
<section class="cards cards--<?php echo esc_attr( $view ); ?>">
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
			<?php foreach ( $items as $n => $item ) :
				$img      = ! empty( $item['image'] ) ? $item['image'] : 0;
				$is_photo = ( 'photo' === $view && $img );
				$is_swatch = ( 'swatch' === $view && $img );
			?>
				<li class="cards__item<?php echo $is_photo ? ' cards__item--photo' : ''; ?><?php echo $is_swatch ? ' cards__item--swatch' : ''; ?>">

					<?php if ( $is_photo ) : ?>
						<?php echo tm_image( $img, 'large', array( 'class' => 'cards__bg', 'loading' => 'lazy', 'sizes' => '(max-width: 560px) 100vw, (max-width: 1024px) 50vw, 300px' ) ); ?>
						<span class="cards__scrim" aria-hidden="true"></span>
					<?php elseif ( $is_swatch ) : ?>
						<?php echo tm_image( $img, 'medium', array( 'class' => 'cards__swatch', 'loading' => 'lazy' ) ); ?>
					<?php endif; ?>

					<div class="cards__body">
						<span class="cards__num"><?php echo esc_html( sprintf( '%02d', $n + 1 ) ); ?></span>
						<?php if ( ! empty( $item['title'] ) ) : ?>
							<h3 class="cards__name"><?php echo esc_html( $item['title'] ); ?></h3>
						<?php endif; ?>
						<?php if ( ! empty( $item['desc'] ) ) : ?>
							<p class="cards__desc"><?php echo esc_html( $item['desc'] ); ?></p>
						<?php endif; ?>
					</div>

				</li>
			<?php endforeach; ?>
		</ul>

	</div>
</section>