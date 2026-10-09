<?php
get_header();
?>
<section class="not-found">
	<h1><?php esc_html_e( 'Сторінку не знайдено', 'tmebel' ); ?></h1>
	<p><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'На головну', 'tmebel' ); ?></a></p>
</section>
<?php
get_footer();
