<?php

/**
 * Секція Hero: слайдер фото, плашка з H1 і кнопками, фраза внизу.
 * Поля макета "hero" з Flexible Content "sections".
 */
defined('ABSPATH') || exit;

$images    = get_sub_field('images') ?: array();
$interval  = (int) get_sub_field('interval') ?: 5;
$title     = get_sub_field('title');
$subtitle  = get_sub_field('subtitle');
$btn_1     = get_sub_field('btn_primary');
$btn_2     = get_sub_field('btn_secondary');
$statement = get_sub_field('statement');
$accent    = get_sub_field('statement_accent');
?>
<section class="hero">
    <div class="hero__box">

        <div class="hero-slider" data-interval="<?php echo esc_attr($interval * 1000); ?>">
            <?php foreach ($images as $i => $img) : ?>
                <div class="hero-slider__slide<?php echo 0 === $i ? ' is-active' : ''; ?>">
                    <?php
                    echo tm_image($img, 'tm-hero', array(
                        'class'         => 'hero-slider__img',
                        'sizes'         => '100vw',
                        'loading'       => 0 === $i ? 'eager' : 'lazy',
                        'fetchpriority' => 0 === $i ? 'high' : 'auto',
                    ));
                    ?>
                </div>
            <?php endforeach; ?>

            <?php if (count($images) > 1) : ?>
                <div class="hero-slider__dots">
                    <?php foreach ($images as $i => $img) : ?>
                        <button type="button" class="hero-slider__dot<?php echo 0 === $i ? ' is-active' : ''; ?>" aria-label="<?php echo esc_attr(sprintf('Слайд %d', $i + 1)); ?>"></button>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="hero__plaque">
            <div>
                <?php if ($title) : ?>
                    <h1 class="hero__title"><?php echo esc_html($title); ?></h1>
                <?php endif; ?>
                <?php if ($subtitle) : ?>
                    <p class="hero__sub"><?php echo esc_html($subtitle); ?></p>
                <?php endif; ?>
            </div>
            <?php if ($btn_1 || $btn_2) : ?>
                <div class="hero__actions">
                    <?php echo tm_link($btn_1, 'btn btn--cream'); ?>
                    <?php echo tm_link($btn_2, 'btn btn--ghost-light'); ?>
                </div>
            <?php endif; ?>
        </div>

        <?php if ($statement || $accent) : ?>
            <p class="hero__statement">
                <?php echo esc_html($statement); ?>
                <?php if ($accent) : ?><strong><?php echo esc_html($accent); ?></strong><?php endif; ?>
            </p>
        <?php endif; ?>

    </div>
</section>