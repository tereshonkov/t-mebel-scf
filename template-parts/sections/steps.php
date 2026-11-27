<?php

/**
 * Секция "Этапы со сроками": зигзаг вокруг линии, как секция "Как мы работаем" (process),
 * стили берутся из process.css. Срок выводится внизу карточки.
 * Под зигзагом необязательная строка-итог, в ней разрешён только <strong>.
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
<section class="how steps">
    <div class="how__inner">

        <?php if ($eyebrow || $title) : ?>
            <div class="how__head">
                <?php if ($eyebrow) : ?>
                    <div class="how__eyebrow"><?php echo esc_html($eyebrow); ?></div>
                <?php endif; ?>
                <?php if ($title) : ?>
                    <h2 class="how__title"><?php echo esc_html($title); ?></h2>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="zig">
            <div class="zig-line" aria-hidden="true"></div>

            <ol class="zig-rows">
                <?php foreach ($steps as $i => $step) :
                    $is_left = (0 === $i % 2);

                    // Карточка одна и та же, меняется только сторона
                    $card  = '<div class="zig-card zig-card--' . ($is_left ? 'left' : 'right') . '">';
                    $card .= '<h3 class="zig-card__title">' . esc_html($step['title']) . '</h3>';
                    if (! empty($step['desc'])) {
                        $card .= '<p class="zig-card__desc">' . esc_html($step['desc']) . '</p>';
                    }
                    if (! empty($step['time'])) {
                        $card .= '<p class="zig-card__foot">' . esc_html($step['time']) . '</p>';
                    }
                    $card .= '</div>';
                ?>
                    <li class="zig-row">
                        <div class="zig-side"><?php echo $is_left ? $card : ''; // phpcs:ignore — всё экранировано выше 
                                                ?></div>

                        <div class="zig-node" aria-hidden="true">
                            <span class="zig-num"><?php echo esc_html(sprintf('%02d', $i + 1)); ?></span>
                        </div>

                        <div class="zig-side"><?php echo $is_left ? '' : $card; // phpcs:ignore 
                                                ?></div>
                    </li>
                <?php endforeach; ?>
            </ol>
        </div>

        <?php if ($note) : ?>
            <p class="steps__note"><?php echo wp_kses($note, array('strong' => array())); ?></p>
        <?php endif; ?>

    </div>
</section>