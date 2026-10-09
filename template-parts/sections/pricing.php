<?php

/**
 * Секция "Прозрачное ценообразование": полоса на всю ширину с фоном-параллаксом.
 */
defined('ABSPATH') || exit;

$image   = get_sub_field('image');
$eyebrow = get_sub_field('eyebrow');
$title   = get_sub_field('title');
$text    = get_sub_field('text');
$button  = get_sub_field('button');

$bg = $image ? wp_get_attachment_image_url($image, 'tm-hero') : '';
?>
<section class="pricing" <?php echo $bg ? ' style="background-image:url(' . esc_url($bg) . ')"' : ''; ?>>
    <div class="pricing__box">
        <?php if ($eyebrow) : ?>
            <div class="pricing__eyebrow"><?php echo esc_html($eyebrow); ?></div>
        <?php endif; ?>
        <?php if ($title) : ?>
            <h2 class="pricing__title"><?php echo esc_html($title); ?></h2>
        <?php endif; ?>
        <?php if ($text) : ?>
            <p class="pricing__text"><?php echo esc_html($text); ?></p>
        <?php endif; ?>
        <?php echo tm_link($button, 'btn btn--gold btn--lg'); ?>
    </div>
</section>