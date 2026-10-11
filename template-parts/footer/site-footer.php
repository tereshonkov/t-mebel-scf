<?php
/**
 * Подвал: логотип, слоган, кнопка звонка, меню, соцсети, копирайт.
 * На телефоне снизу экрана липкая панель "Позвонить / Написать".
 * Данные: страница опций (tm_opt) и меню "Меню футера".
 */
defined( 'ABSPATH' ) || exit;

$f_title   = tm_opt( 'footer_title' );
$f_accent  = tm_opt( 'footer_accent' );
$f_text    = tm_opt( 'footer_text' );
$phone     = tm_opt( 'phone' );
$tel       = $phone ? 'tel:' . preg_replace( '/[^+\d]/', '', $phone ) : '';
$messenger = tm_opt( 'messenger_url' );
$socials   = tm_opt( 'socials', array() );
?>
<footer class="site-footer">
	<div class="site-footer__inner">

		<div class="site-footer__top">

			<div class="foot-brand">
				<a class="site-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php bloginfo( 'name' ); ?>">
					<?php
					if ( has_custom_logo() ) {
						echo wp_get_attachment_image( get_theme_mod( 'custom_logo' ), 'medium', false, array( 'class' => 'site-logo__img', 'loading' => 'lazy' ) );
					} else {
						bloginfo( 'name' );
					}
					?>
				</a>

				<?php if ( $f_title || $f_accent ) : ?>
					<p class="foot-title">
						<?php echo esc_html( $f_title ); ?>
						<?php if ( $f_accent ) : ?><em><?php echo esc_html( $f_accent ); ?></em><?php endif; ?>
					</p>
				<?php endif; ?>

				<?php if ( $f_text ) : ?>
					<p class="foot-text"><?php echo esc_html( $f_text ); ?></p>
				<?php endif; ?>

				<?php if ( $tel ) : ?>
					<a class="btn btn--gold" href="<?php echo esc_url( $tel ); ?>"><?php echo esc_html( tm_t( '+380671496741', '+380671496741' ) ); ?></a>
				<?php endif; ?>
			</div>

			<div class="foot-cols">
				<?php if ( has_nav_menu( 'footer' ) ) : ?>
					<nav class="foot-col" aria-label="<?php echo esc_attr( tm_t( 'Навігація', 'Навигация' ) ); ?>">
						<p class="foot-label"><?php echo esc_html( tm_t( 'Навігація', 'Навигация' ) ); ?></p>
						<?php
						wp_nav_menu( array(
							'theme_location' => 'footer',
							'container'      => false,
							'menu_class'     => 'foot-links',
							'depth'          => 1,
							'fallback_cb'    => false,
						) );
						?>
					</nav>
				<?php endif; ?>

				<?php if ( $socials && is_array( $socials ) ) : ?>
					<div class="foot-col">
						<p class="foot-label"><?php echo esc_html( tm_t( 'Ми в мережах', 'Мы в сетях' ) ); ?></p>
						<ul class="foot-links">
							<?php foreach ( $socials as $s ) : ?>
								<?php
								// Поле url может быть типа "URL" (строка) или "Ссылка" (массив): поддерживаем оба.
								$s_url = is_array( $s['url'] ?? null ) ? ( $s['url']['url'] ?? '' ) : ( $s['url'] ?? '' );
								if ( ! $s_url || empty( $s['label'] ) ) {
									continue;
								}
								?>
								<li><a href="<?php echo esc_url( $s_url ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $s['label'] ); ?></a></li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endif; ?>
			</div>

		</div>

		<p class="foot-copy">
			&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> T-MEBEL. <?php echo esc_html( tm_t( 'Всі права захищені', 'Все права защищены' ) ); ?>
		</p>

	</div>
</footer>

<?php if ( $tel || $messenger ) : ?>
	<div class="sticky-cta">
		<?php if ( $tel ) : ?>
			<a class="sticky-cta__call" href="<?php echo esc_url( $tel ); ?>"><?php echo esc_html( tm_t( 'Зателефонувати', 'Позвонить' ) ); ?></a>
		<?php endif; ?>
		<?php if ( $messenger ) : ?>
			<a class="sticky-cta__msg" href="<?php echo esc_url( $messenger ); ?>" target="_blank" rel="noopener"><?php echo esc_html( tm_t( 'Написати', 'Написать' ) ); ?></a>
		<?php endif; ?>
	</div>
<?php endif; ?>