<?php
/**
 * Секция: большое фото на всю ширину контейнера.
 */
defined( 'ABSPATH' ) || exit;

$image = get_sub_field( 'image' );
if ( ! $image ) {
	return;
}
?>
<section class="wide-image">
	<div class="wide-image__box">
		<?php
		echo tm_image( $image, 'tm-hero', array(
			'class'   => 'wide-image__img',
			'sizes'   => '(max-width: 1240px) 100vw, 1200px',
			'loading' => 'eager',
		) );
		?>
	</div>
</section>