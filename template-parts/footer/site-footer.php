<?php
/**
 * Футер сайту. ТИМЧАСОВА проста версія.
 * Наступний крок: перенести реальний футер, дані телефону/соцмереж брати через tm_opt().
 */
defined( 'ABSPATH' ) || exit;
?>
<footer class="site-footer">
	<div class="site-footer__inner">
		<p class="site-footer__copy">&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> T-MEBEL</p>
	</div>
</footer>
