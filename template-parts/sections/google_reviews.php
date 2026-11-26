<?php

/**
 * Секция "Відгуки Google". Сами отзывы берутся из "Налаштування сайту" → "Відгуки Google",
 * здесь только необязательный свой заголовок.
 */
defined('ABSPATH') || exit;

get_template_part('template-parts/reviews/google-reviews', null, array(
    'title' => get_sub_field('title'),
));
