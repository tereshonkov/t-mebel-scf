<?php
defined('ABSPATH') || exit;

/**
 * Повертає версію файлу CSS/JS за часом його зміни (кеш скидається при кожному деплої).
 */
function tm_asset_version(string $rel): string
{
	$path = TM_DIR . '/' . ltrim($rel, '/');
	return file_exists($path) ? (string) filemtime($path) : TM_VERSION;
}

add_action('wp_enqueue_scripts', function () {
	// Шрифти (self-host, assets/fonts)
	wp_enqueue_style('tm-fonts', TM_URI . '/assets/css/fonts.css', array(), tm_asset_version('assets/css/fonts.css'));

	// Глобальні стилі: токени, база, шапка, футер.
	$globals = array('base', 'header', 'footer', 'buttons');
	$prev    = array('tm-fonts');
	foreach ($globals as $name) {
		$rel = "assets/css/{$name}.css";
		if (file_exists(TM_DIR . '/' . $rel)) {
			wp_enqueue_style("tm-{$name}", TM_URI . '/' . $rel, $prev, tm_asset_version($rel));
			$prev = array("tm-{$name}");
		}
	}

	// CSS секцій: тільки тих, що реально є на поточній сторінці.
	foreach (tm_page_layouts() as $layout) {
		$rel = "assets/css/sections/{$layout}.css";
		if (file_exists(TM_DIR . '/' . $rel)) {
			wp_enqueue_style("tm-section-{$layout}", TM_URI . '/' . $rel, array('tm-base'), tm_asset_version($rel));
		}
		$js = "assets/js/sections/{$layout}.js";
		if (file_exists(TM_DIR . '/' . $js)) {
			wp_enqueue_script("tm-section-{$layout}", TM_URI . '/' . $js, array(), tm_asset_version($js), true);
		}
	}

	// Калькулятор шафи користується стилями кухонного, підключаємо їх першими.
	if (in_array('calc_wardrobe', tm_page_layouts(), true)) {
		$rel = 'assets/css/sections/calc_full.css';
		if (file_exists(TM_DIR . '/' . $rel)) {
			wp_enqueue_style('tm-section-calc_full', TM_URI . '/' . $rel, array('tm-base'), tm_asset_version($rel));
		}
	}

	// Сторінка "Наші роботи" (/service/) і сторінки категорій: у них немає секцій, тому підключаємо свої файли.
	if (is_post_type_archive('project') || is_tax('project_category')) {
		foreach (array('works', 'sections/form_card') as $name) {
			$rel = "assets/css/{$name}.css";
			if (file_exists(TM_DIR . '/' . $rel)) {
				wp_enqueue_style('tm-' . str_replace('/', '-', $name), TM_URI . '/' . $rel, array('tm-base'), tm_asset_version($rel));
			}
		}
	}

	if (is_singular('project')) {
		$rel = 'assets/css/project.css';
		if (file_exists(TM_DIR . '/' . $rel)) {
			wp_enqueue_style('tm-project', TM_URI . '/' . $rel, array('tm-base'), tm_asset_version($rel));
		}

		$rel_js = 'assets/js/project.js';
		if (file_exists(TM_DIR . '/' . $rel_js)) {
			wp_enqueue_script('tm-project-js', TM_URI . '/' . $rel_js, array(), tm_asset_version($rel_js), true);
		}

		$rel_lead = 'assets/css/sections/lead.css';
		if (file_exists(TM_DIR . '/' . $rel_lead)) {
			wp_enqueue_style('tm-section-lead', TM_URI . '/' . $rel_lead, array('tm-base'), tm_asset_version($rel_lead));
		}
	}

	if (is_post_type_archive('guide') || is_tax('guide_category')) {
		foreach (array('guides', 'sections/lead') as $name) {
			$rel = "assets/css/{$name}.css";
			if (file_exists(TM_DIR . '/' . $rel)) {
				wp_enqueue_style('tm-' . str_replace('/', '-', $name), TM_URI . '/' . $rel, array('tm-base'), tm_asset_version($rel));
			}
		}
	}

	if (is_singular('guide')) {
		foreach (array('guide-single', 'sections/lead') as $name) {
			$rel = "assets/css/{$name}.css";
			if (file_exists(TM_DIR . '/' . $rel)) {
				wp_enqueue_style('tm-' . str_replace('/', '-', $name), TM_URI . '/' . $rel, array('tm-base'), tm_asset_version($rel));
			}
		}
	}

	if (is_404()) {
		$rel = 'assets/css/404.css';
		if (file_exists(TM_DIR . '/' . $rel)) {
			wp_enqueue_style('tm-404', TM_URI . '/' . $rel, array('tm-base'), tm_asset_version($rel));
		}
	}

	if (is_page_template('template-text.php')) {
		$rel = 'assets/css/text-page.css';
		if (file_exists(TM_DIR . '/' . $rel)) {
			wp_enqueue_style('tm-text-page', TM_URI . '/' . $rel, array('tm-base'), tm_asset_version($rel));
		}
	}

	// Глобальний скрипт сайту.
	$main = 'assets/js/main.js';
	if (file_exists(TM_DIR . '/' . $main)) {
		wp_enqueue_script('tm-main', TM_URI . '/' . $main, array(), tm_asset_version($main), true);
	}
});
