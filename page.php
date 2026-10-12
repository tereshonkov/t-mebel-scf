<?php

/**
 * Звичайна сторінка: секції з поля "sections".
 * Якщо секцій немає, виводиться звичайний вміст сторінки.
 */
get_header();
if (function_exists('have_rows') && have_rows('sections')) {
	tm_render_sections('sections');
} else {
	while (have_posts()) {
		the_post();
		echo '<div class="entry-content">';
		the_content();
		echo '</div>';
	}
}
get_footer();
