<?php
/**
 * Шапка сайту: логотип, меню (з мега-панеллю), перемикач мов, кнопка.
 */
defined( 'ABSPATH' ) || exit;

$tm_langs = tm_languages();
$tm_cur   = '';
foreach ( $tm_langs as $tm_l ) {
	if ( ! empty( $tm_l['current_lang'] ) ) {
		$tm_cur = $tm_l['name'];
		break;
	}
}
?>
<header class="site-header">

	<div class="site-header__brand">
		<a class="site-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php bloginfo( 'name' ); ?>">
			<?php
			if ( has_custom_logo() ) {
				echo wp_get_attachment_image( get_theme_mod( 'custom_logo' ), 'medium', false, array( 'class' => 'site-logo__img' ) );
			} else {
				bloginfo( 'name' );
			}
			?>
		</a>
	</div>

	<nav class="site-nav" aria-label="<?php esc_attr_e( 'Головне меню', 'tmebel' ); ?>">
		<?php
		wp_nav_menu( array(
			'theme_location' => 'primary',
			'container'      => false,
			'items_wrap'     => '%3$s',
			'depth'          => 1,
			'walker'         => new TM_Nav_Walker(),
			'fallback_cb'    => false,
		) );
		?>
	</nav>

	<div class="site-header__actions">
		<?php if ( $tm_langs ) : ?>
			<div class="hdr-lang">
				<button type="button" class="hdr-lang__btn" aria-haspopup="true">
					<?php echo esc_html( $tm_cur ); ?> <span class="hdr-lang__caret" aria-hidden="true">▾</span>
				</button>
				<ul class="hdr-lang__list">
					<?php foreach ( $tm_langs as $tm_l ) : ?>
						<li>
							<a href="<?php echo esc_url( $tm_l['url'] ); ?>" hreflang="<?php echo esc_attr( $tm_l['locale'] ?? $tm_l['slug'] ); ?>" lang="<?php echo esc_attr( $tm_l['slug'] ); ?>"<?php echo ! empty( $tm_l['current_lang'] ) ? ' class="is-current"' : ''; ?>>
								<?php echo esc_html( $tm_l['name'] ); ?>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endif; ?>

		<a class="btn btn--gold hdr-cta" href="<?php echo esc_url( tm_opt( 'header_cta_url', '#lead' ) ); ?>">
			<?php echo esc_html( tm_opt( 'header_cta_label', 'Замовити дзвінок' ) ); ?>
		</a>

		<button type="button" class="burger" aria-label="<?php esc_attr_e( 'Відкрити меню', 'tmebel' ); ?>" aria-expanded="false" aria-controls="mobile-menu">
			<span></span><span></span><span></span>
		</button>
	</div>

</header>

<?php // Мобільне меню. Стоїть ПОЗА <header>, бо backdrop-filter шапки ламає position:fixed у дочірніх елементів. ?>
<div class="mobile-menu" id="mobile-menu" aria-hidden="true">
	<div class="mobile-menu__top">
		<a class="site-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php bloginfo( 'name' ); ?>">
			<?php
			if ( has_custom_logo() ) {
				echo wp_get_attachment_image( get_theme_mod( 'custom_logo' ), 'medium', false, array( 'class' => 'site-logo__img' ) );
			} else {
				bloginfo( 'name' );
			}
			?>
		</a>
		<button type="button" class="mobile-menu__close" aria-label="<?php esc_attr_e( 'Закрити меню', 'tmebel' ); ?>"></button>
	</div>

	<nav class="mobile-nav" aria-label="<?php esc_attr_e( 'Мобільне меню', 'tmebel' ); ?>">
		<?php
		wp_nav_menu( array(
			'theme_location' => 'primary',
			'container'      => false,
			'items_wrap'     => '%3$s',
			'depth'          => 1,
			'walker'         => new TM_Nav_Walker(),
			'fallback_cb'    => false,
		) );
		?>
	</nav>

	<?php if ( $tm_langs ) : ?>
		<ul class="mobile-menu__langs">
			<?php foreach ( $tm_langs as $tm_l ) : ?>
				<li>
					<a href="<?php echo esc_url( $tm_l['url'] ); ?>" lang="<?php echo esc_attr( $tm_l['slug'] ); ?>"<?php echo ! empty( $tm_l['current_lang'] ) ? ' class="is-current"' : ''; ?>>
						<?php echo esc_html( $tm_l['name'] ); ?>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>

	<a class="btn btn--gold mobile-menu__cta" href="<?php echo esc_url( tm_opt( 'header_cta_url', '#lead' ) ); ?>">
		<?php echo esc_html( tm_opt( 'header_cta_label', 'Замовити дзвінок' ) ); ?>
	</a>
</div>