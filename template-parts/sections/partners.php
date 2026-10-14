<?php
/**
 * Секция "Партнёры": бесконечная лента логотипов.
 * JS (partners.js) дублирует логотипы, чтобы лента была бесшовной на любой ширине экрана.
 */
defined( 'ABSPATH' ) || exit;

$logos = get_sub_field( 'logos' ) ?: array();
if ( ! $logos ) {
	return;
}
?>
<section class="partners">
	<div class="marquee">
		<div class="marquee-track">
			<?php foreach ( $logos as $logo ) : ?>
				<div class="marquee-item">
					<?php echo tm_image( $logo, 'medium', array( 'class' => 'marquee-logo', 'loading' => 'lazy' ) ); ?>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>