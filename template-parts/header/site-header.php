<?php
/**
 * Шапка сайту. ТИМЧАСОВА проста версія, щоб тема запускалась.
 * Наступний крок: перенести сюди реальну шапку (логотип, меню, перемикач мов, CTA).
 */
defined( 'ABSPATH' ) || exit;
?>
<header class="site-header">
	<div class="site-header__inner">
		<a class="site-header__logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<?php echo has_custom_logo() ? wp_kses_post( get_custom_logo() ) : esc_html( get_bloginfo( 'name' ) ); ?>
		</a>
		<?php
		wp_nav_menu( array(
			'theme_location' => 'primary',
			'container'      => 'nav',
			'container_class' => 'site-nav',
			'menu_class'     => 'site-nav__list',
			'fallback_cb'    => false,
		) );
		?>
	</div>
</header>
