<?php
/** Головна сторінка: тільки секції з поля "sections". */
get_header();
tm_render_sections( 'sections' );
get_footer();
