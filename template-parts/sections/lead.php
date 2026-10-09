<?php
/**
 * Секция "Форма заявки": текст и форма Contact Form 7 слева, фото с плашкой справа.
 * id="lead" нужен для кнопок с адресом #lead (hero, цены, шапка).
 */
defined( 'ABSPATH' ) || exit;

$eyebrow    = get_sub_field( 'eyebrow' );
$title      = get_sub_field( 'title' );
$text       = get_sub_field( 'text' );
$form       = get_sub_field( 'form' );
$image      = get_sub_field( 'image' );
$badge_num  = get_sub_field( 'badge_num' );
$badge_text = get_sub_field( 'badge_text' );
?>
<section class="lead" id="lead">
	<div class="lead__card">

		<div class="lead__content">
			<?php if ( $eyebrow ) : ?>
				<div class="lead__eyebrow"><?php echo esc_html( $eyebrow ); ?></div>
			<?php endif; ?>
			<?php if ( $title ) : ?>
				<h2 class="lead__title"><?php echo esc_html( $title ); ?></h2>
			<?php endif; ?>
			<?php if ( $text ) : ?>
				<p class="lead__text"><?php echo esc_html( $text ); ?></p>
			<?php endif; ?>

			<?php
			if ( $form ) {
				echo do_shortcode( $form ); // phpcs:ignore -- форма от CF7, уже экранирована плагином
			}
			?>
		</div>

		<div class="lead__media">
			<?php
			if ( $image ) {
				echo wp_get_attachment_image( $image, 'large', false, array(
					'class'   => 'lead__img',
					'sizes'   => '(max-width: 1024px) 100vw, 600px',
					'loading' => 'lazy',
				) );
			}
			?>
			<?php if ( $badge_num || $badge_text ) : ?>
				<div class="lead__badge">
					<?php if ( $badge_num ) : ?>
						<span class="lead__badge-num"><?php echo esc_html( $badge_num ); ?></span>
					<?php endif; ?>
					<?php if ( $badge_text ) : ?>
						<span class="lead__badge-text"><?php echo nl2br( esc_html( $badge_text ) ); ?></span>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>

	</div>
</section>