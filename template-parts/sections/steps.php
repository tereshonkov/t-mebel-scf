<?php

/**
 * Секция "Этапы со сроками": сетка карточек с номером, названием, описанием
 * и плашкой срока внизу. Под сеткой необязательная строка-итог.
 * В итоге разрешён только <strong>, чтобы выделить срок.
 */
defined('ABSPATH') || exit;

$eyebrow = get_sub_field('eyebrow');
$title   = get_sub_field('title');
$note    = get_sub_field('note');
$steps   = get_sub_field('steps') ?: array();

// Шаги без названия не выводим.
$steps = array_values(array_filter($steps, fn($step) => '' !== trim((string) ($step['title'] ?? ''))));

if (! $steps) {
    return;
}
?>
<section class="steps">
    <div class="steps__inner">

        <?php if ($eyebrow || $title) : ?>
            <div class="steps__head">
                <?php if ($eyebrow) : ?>
                    <div class="steps__eyebrow"><?php echo esc_html($eyebrow); ?></div>
                <?php endif; ?>
                <?php if ($title) : ?>
                    <h2 class="steps__title"><?php echo esc_html($title); ?></h2>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <ol class="steps__list">
            <?php foreach ($steps as $n => $step) : ?>
                <li class="steps__item">
                    <span class="steps__num" aria-hidden="true"><?php echo esc_html(sprintf('%02d', $n + 1)); ?></span>
                    <h3 class="steps__name"><?php echo esc_html($step['title']); ?></h3>
                    <?php if (! empty($step['desc'])) : ?>
                        <p class="steps__desc"><?php echo esc_html($step['desc']); ?></p>
                    <?php endif; ?>
                    <?php if (! empty($step['time'])) : ?>
                        <span class="steps__time"><?php echo esc_html($step['time']); ?></span>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ol>

        <?php if ($note) : ?>
            <p class="steps__note"><?php echo wp_kses($note, array('strong' => array())); ?></p>
        <?php endif; ?>

    </div>
</section>