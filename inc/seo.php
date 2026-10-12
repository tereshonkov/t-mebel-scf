<?php

/**
 * SEO: заголовки и описания архивов /service/ и /guides/ на двух языках.
 * Rank Math Free хранит один шаблон архива на оба языка, поэтому подменяем здесь.
 */

if (! defined('ABSPATH')) {
    exit;
}

/**
 * Тексты для архива текущей страницы или null, если это не наш архив.
 */
function tm_seo_archive_meta()
{
    if (is_post_type_archive('project')) {
        return array(
            'title' => tm_t('Наші роботи: меблі на замовлення в Харкові', 'Наши работы: мебель на заказ в Харькове'),
            'desc'  => tm_t(
                'Фото реальних проєктів T-Mebel: кухні, шафи-купе, спальні та інші меблі на замовлення в Харкові. Оберіть ідею та отримайте розрахунок вартості.',
                'Фото реальных проектов T-Mebel: кухни, шкафы-купе, спальни и другая мебель на заказ в Харькове. Выберите идею и получите расчёт стоимости.'
            ),
        );
    }
    if (is_post_type_archive('guide')) {
        return array(
            'title' => tm_t('Корисні статті про меблі на замовлення', 'Полезные статьи о мебели на заказ'),
            'desc'  => tm_t(
                'Поради від T-Mebel: як обрати матеріали, з чого складається ціна кухні чи шафи-купе та як замовити меблі без переплат.',
                'Советы от T-Mebel: как выбрать материалы, из чего складывается цена кухни или шкафа-купе и как заказать мебель без переплат.'
            ),
        );
    }
    return null;
}

function tm_seo_archive_title($title)
{
    $m = tm_seo_archive_meta();
    if (! $m) {
        return $title;
    }
    $paged = max(1, (int) get_query_var('paged'));
    $page  = $paged > 1 ? ' (' . tm_t('сторінка', 'страница') . ' ' . $paged . ')' : '';
    return $m['title'] . $page . ' | T-Mebel';
}

function tm_seo_archive_desc($desc)
{
    $m = tm_seo_archive_meta();
    return $m ? $m['desc'] : $desc;
}

// Тег <title> и meta description.
add_filter('rank_math/frontend/title', 'tm_seo_archive_title', 20);
add_filter('rank_math/frontend/description', 'tm_seo_archive_desc', 20);

// То же для превью ссылок в соцсетях и мессенджерах.
add_filter('rank_math/opengraph/facebook/og_title', 'tm_seo_archive_title', 20);
add_filter('rank_math/opengraph/twitter/twitter_title', 'tm_seo_archive_title', 20);
add_filter('rank_math/opengraph/facebook/og_description', 'tm_seo_archive_desc', 20);
add_filter('rank_math/opengraph/twitter/twitter_description', 'tm_seo_archive_desc', 20);
