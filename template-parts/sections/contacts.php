<?php

/**
 * Секция "Контакты и карта": карточки адреса, телефона и соцсетей + карта Google.
 * Все данные со страницы опций, полей у макета нет.
 */
defined('ABSPATH') || exit;

$address = tm_opt('address');
$phone   = tm_opt('phone');
$display = tm_opt('phone_display') ?: $phone;
$tel     = $phone ? 'tel:' . preg_replace('/[^+\d]/', '', $phone) : '';
$hours   = tm_opt('hours');
$socials = tm_opt('socials', array());

// Карта строится по адресу на украинском: Google лучше находит именно его.
$map_q   = get_field('address_ua', 'option') ?: $address;
$map_src = $map_q ? 'https://maps.google.com/maps?q=' . rawurlencode($map_q) . '&z=15&output=embed' : '';
?>
<section class="ct">
	<div class="ct__inner">
		<div class="ct-two">

			<div class="ct-cards">
				<?php if ($address) : ?>
					<div class="ct-card">
						<p class="ct-label"><?php echo esc_html(tm_t('Адреса', 'Адрес')); ?></p>
						<p class="ct-addr"><?php echo esc_html($address); ?></p>
					</div>
				<?php endif; ?>

				<?php if ($tel) : ?>
					<div class="ct-card">
						<p class="ct-label"><?php echo esc_html(tm_t('Телефон', 'Телефон')); ?></p>
						<p class="ct-phone"><a href="<?php echo esc_url($tel); ?>"><?php echo esc_html($display); ?></a></p>
						<?php if ($hours) : ?>
							<p class="ct-hours"><?php echo esc_html($hours); ?></p>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<?php if ($socials && is_array($socials)) : ?>
					<div class="ct-card">
						<p class="ct-label"><?php echo esc_html(tm_t('Соціальні мережі', 'Социальные сети')); ?></p>
						<ul class="ct-soc">
							<?php foreach ($socials as $s) : ?>
								<?php
								$s_url = is_array($s['url'] ?? null) ? ($s['url']['url'] ?? '') : ($s['url'] ?? '');
								if (! $s_url || empty($s['label'])) {
									continue;
								}
								?>
								<li><a href="<?php echo esc_url($s_url); ?>" target="_blank" rel="noopener"><?php echo esc_html($s['label']); ?></a></li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endif; ?>
			</div>

			<?php if ($map_src) : ?>
				<div class="ct-map">
					<iframe title="<?php echo esc_attr(tm_t('Мапа T-Mebel', 'Карта T-Mebel')); ?>" src="<?php echo esc_url($map_src); ?>" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
				</div>
			<?php endif; ?>

		</div>
	</div>
</section>