<?php

/**
 * Разметка попапа-заявки. Выводится скрытой, открывается через popup.js.
 */
defined('ABSPATH') || exit;

$form_id = trim((string) tm_opt('popup_form'));
?>
<div class="pp-overlay" id="pp-lead" hidden>
	<div class="pp-modal" role="dialog" aria-modal="true" aria-labelledby="pp-title">

		<button type="button" class="pp-close" data-pp-close aria-label="<?php echo esc_attr(tm_t('Закрити', 'Закрыть')); ?>">✕</button>

		<div class="pp-view" data-pp-form>
			<div class="pp-badge"><?php echo esc_html(tm_t('Індивідуальний розрахунок', 'Индивидуальный расчёт')); ?></div>
			<h2 id="pp-title" class="pp-title"><?php echo esc_html(tm_t('Залиште контакти', 'Оставьте контакты')); ?></h2>
			<p class="pp-sub"><?php echo esc_html(tm_t('Розрахуємо вартість під ваш простір і безкоштовно проконсультуємо', 'Рассчитаем стоимость под ваше пространство и бесплатно проконсультируем')); ?></p>

			<?php echo do_shortcode('[contact-form-7 id="' . esc_attr($form_id) . '"]'); ?>

			<p class="pp-note"><?php echo esc_html(tm_t('Менеджер зателефонує, щоб узгодити деталі та варіанти матеріалів', 'Менеджер позвонит, чтобы согласовать детали и варианты материалов')); ?></p>
		</div>

		<div class="pp-view pp-done" data-pp-done hidden>
			<div class="pp-check" aria-hidden="true">✓</div>
			<h2 class="pp-title"><?php echo esc_html(tm_t('Дякуємо!', 'Спасибо!')); ?></h2>
			<p class="pp-sub"><?php echo esc_html(tm_t('Заявку отримано. Менеджер зателефонує протягом 15 хвилин.', 'Заявка получена. Менеджер позвонит в течение 15 минут.')); ?></p>
			<button type="button" class="pp-back" data-pp-close><?php echo esc_html(tm_t('Повернутись на сайт', 'Вернуться на сайт')); ?></button>
		</div>

	</div>
</div>