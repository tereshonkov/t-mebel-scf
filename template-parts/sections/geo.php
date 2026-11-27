<?php

/**
 * Секция "География": слева заголовок, текст, цифры и названия районов/городов текстом,
 * справа карта (Leaflet + OpenStreetMap, грузится лениво в assets/js/sections/geo.js).
 *
 * Списки задаются textarea, по одному месту в строке:
 *   Пісочин | 49.953, 36.087
 * Координаты необязательны: место без них выводится текстом, но не ставится на карту.
 * Список 1 = районы города, список 2 = населённые пункты области.
 * Если координат нет ни у одного места, справа вместо карты выводятся прежние "чипсы".
 */
defined('ABSPATH') || exit;

$eyebrow = get_sub_field('eyebrow');
$title   = get_sub_field('title');
$text    = get_sub_field('text');

$groups = array();
$points = array();

for ($i = 1; $i <= 2; $i++) {
    $lines = preg_split('/\R/u', (string) get_sub_field('items_' . $i));
    $names = array();

    foreach ($lines as $line) {
        // "Название | 49.95, 36.08" -> название и координаты (если есть)
        $parts = array_map('trim', explode('|', $line, 2));
        $name  = $parts[0];
        if ('' === $name) {
            continue;
        }
        $names[] = $name;

        if (! empty($parts[1]) && preg_match('/^(-?\d+(?:\.\d+)?)\s*,\s*(-?\d+(?:\.\d+)?)$/', $parts[1], $m)) {
            $points[] = array(
                'n' => $name,
                'a' => (float) $m[1],
                'o' => (float) $m[2],
                'g' => $i, // 1 = район города, 2 = область
            );
        }
    }

    if ($names) {
        $groups[] = array(
            'label' => (string) get_sub_field('label_' . $i),
            'items' => $names,
        );
    }
}

if (! $title && ! $groups) {
    return;
}

$has_map = (bool) $points;

// Подписи карты на текущем языке
$map_labels = array(
    'city'    => tm_t('Харків', 'Харьков'),
    'oblast'  => tm_t('Область', 'Область'),
    'suburbs' => tm_t('Передмістя', 'Пригород'),
    'areas'   => tm_t('Райони', 'Районы'),
);
?>
<section class="geo<?php echo $has_map ? ' geo--map' : ''; ?>">
    <div class="geo__inner">

        <div class="geo__info">
            <?php if ($eyebrow) : ?>
                <div class="geo__eyebrow"><?php echo esc_html($eyebrow); ?></div>
            <?php endif; ?>

            <?php if ($title) : ?>
                <h2 class="geo__title"><?php echo esc_html($title); ?></h2>
            <?php endif; ?>

            <?php if ($text) : ?>
                <div class="geo__text"><?php echo wp_kses_post(wpautop($text)); ?></div>
            <?php endif; ?>

            <?php if ($has_map && $groups) : ?>
                <div class="geo__stats">
                    <?php foreach ($groups as $group) : ?>
                        <div class="geo__stat">
                            <div class="geo__stat-num"><?php echo esc_html(count($group['items'])); ?></div>
                            <?php if ($group['label']) : ?>
                                <div class="geo__stat-label"><?php echo esc_html($group['label']); ?></div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php // Названия текстом: Google не читает карту, а слова "Салтівський", "Пісочин" нужны для местного поиска 
                ?>
                <div class="geo__places">
                    <?php foreach ($groups as $group) : ?>
                        <p class="geo__places-line">
                            <?php if ($group['label']) : ?>
                                <strong><?php echo esc_html($group['label']); ?>:</strong>
                            <?php endif; ?>
                            <?php echo esc_html(implode(', ', $group['items'])); ?>
                        </p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <?php if ($has_map) : ?>
            <div class="geo__map"
                data-points="<?php echo esc_attr(wp_json_encode($points)); ?>"
                data-labels="<?php echo esc_attr(wp_json_encode($map_labels)); ?>"
                role="img"
                aria-label="<?php echo esc_attr(tm_t('Карта: райони Харкова та населені пункти області, де працює T-Mebel', 'Карта: районы Харькова и населённые пункты области, где работает T-Mebel')); ?>">
            </div>
        <?php elseif ($groups) : ?>
            <div class="geo__groups">
                <?php foreach ($groups as $group) : ?>
                    <div class="geo__group">
                        <?php if ($group['label']) : ?>
                            <h3 class="geo__label"><?php echo esc_html($group['label']); ?></h3>
                        <?php endif; ?>
                        <ul class="geo__chips">
                            <?php foreach ($group['items'] as $item) : ?>
                                <li class="geo__chip"><?php echo esc_html($item); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>
</section>