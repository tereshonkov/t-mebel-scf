<?php
/**
 * Секция "Как мы работаем": зигзаг из карточек вокруг вертикальной линии.
 * Чётные шаги (01, 03) слева, нечётные (02, 04) справа.
 */
defined( 'ABSPATH' ) || exit;

$eyebrow = get_sub_field( 'eyebrow' );
$title   = get_sub_field( 'title' );
$steps   = get_sub_field( 'steps' ) ?: array();

if ( ! $steps ) {
	return;
}
?>
<section class="how">
	<div class="how__inner">

		<div class="how__head">
			<?php if ( $eyebrow ) : ?>
				<div class="how__eyebrow"><?php echo esc_html( $eyebrow ); ?></div>
			<?php endif; ?>
			<?php if ( $title ) : ?>
				<h2 class="how__title"><?php echo esc_html( $title ); ?></h2>
			<?php endif; ?>
		</div>

		<div class="zig">
			<div class="zig-line" aria-hidden="true"></div>

			<ol class="zig-rows">
				<?php foreach ( $steps as $i => $step ) : ?>
					<?php $is_left = ( 0 === $i % 2 ); ?>
					<li class="zig-row">
						<div class="zig-side">
							<?php if ( $is_left ) : ?>
								<div class="zig-card zig-card--left">
									<h3 class="zig-card__title"><?php echo esc_html( $step['title'] ?? '' ); ?></h3>
									<p class="zig-card__desc"><?php echo esc_html( $step['desc'] ?? '' ); ?></p>
									<?php if ( ! empty( $step['foot'] ) ) : ?>
										<p class="zig-card__foot"><?php echo esc_html( $step['foot'] ); ?></p>
									<?php endif; ?>
								</div>
							<?php endif; ?>
						</div>

						<div class="zig-node" aria-hidden="true">
							<span class="zig-num"><?php echo esc_html( sprintf( '%02d', $i + 1 ) ); ?></span>
						</div>

						<div class="zig-side">
							<?php if ( ! $is_left ) : ?>
								<div class="zig-card zig-card--right">
									<h3 class="zig-card__title"><?php echo esc_html( $step['title'] ?? '' ); ?></h3>
									<p class="zig-card__desc"><?php echo esc_html( $step['desc'] ?? '' ); ?></p>
									<?php if ( ! empty( $step['foot'] ) ) : ?>
										<p class="zig-card__foot"><?php echo esc_html( $step['foot'] ); ?></p>
									<?php endif; ?>
								</div>
							<?php endif; ?>
						</div>
					</li>
				<?php endforeach; ?>
			</ol>
		</div>

	</div>
</section>