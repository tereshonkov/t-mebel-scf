<?php
defined('ABSPATH') || exit;

/**
 * Склеює всі стилі теми (tm-*) у один файл, щоб замість 16 запитів був 1.
 * Файл кешується в uploads/tm-cache, ім'я залежить від списку файлів і часу їх зміни.
 * Якщо щось не вдалось, залишаються звичайні окремі файли.
 */
add_action('wp_enqueue_scripts', function () {
    if (is_admin() || is_customize_preview()) {
        return;
    }

    $styles = wp_styles();
    $items  = array(); // handle => array(path, url)

    foreach ($styles->queue as $handle) {
        if (strpos($handle, 'tm-') !== 0 || !isset($styles->registered[$handle])) {
            continue;
        }
        $obj = $styles->registered[$handle];
        $src = (string) $obj->src;

        // Якщо до стилю додано inline CSS, не чіпаємо цей набір взагалі.
        if (!empty($obj->extra['after']) || !empty($obj->extra['before'])) {
            return;
        }
        if (strpos($src, TM_URI) !== 0) {
            continue;
        }
        $path = TM_DIR . substr($src, strlen(TM_URI));
        if (!file_exists($path)) {
            continue;
        }
        $items[$handle] = array($path, $src);
    }

    if (count($items) < 2) {
        return;
    }

    // 'v2' змінюємо, коли міняється сам код збірки (щоб створився новий файл).
    $sig = 'v2|';
    foreach ($items as $handle => $it) {
        $sig .= $handle . filemtime($it[0]) . '|';
    }
    $hash = substr(md5($sig), 0, 12);

    $upload = wp_upload_dir();
    $dir    = $upload['basedir'] . '/tm-cache';
    $file   = $dir . '/bundle-' . $hash . '.css';
    $url    = $upload['baseurl'] . '/tm-cache/bundle-' . $hash . '.css';

    if (!file_exists($file)) {
        $css = '';
        foreach ($items as $it) {
            $c = file_get_contents($it[0]);
            if (false === $c) {
                return;
            }
            $base = trailingslashit(dirname($it[1]));

            // Відносні url(...) (шрифти, картинки) перетворюємо на повні адреси.
            $c = preg_replace_callback(
                '~url\(\s*([\'"]?)(?!data:|https?:|//|/|#)([^)\'"]+)\1\s*\)~i',
                function ($m) use ($base) {
                    $u = $base . $m[2];
                    while (preg_match('~/[^/]+/\.\./~', $u)) {
                        $u = preg_replace('~/[^/]+/\.\./~', '/', $u, 1);
                    }
                    return 'url(' . $m[1] . $u . $m[1] . ')';
                },
                $c
            );
            $c = preg_replace('~@charset[^;]+;~i', '', $c);

            // Мінімізація: прибираємо коментарі та зайві пробіли.
            $c = preg_replace('~/\*.*?\*/~s', '', $c);
            $c = preg_replace('~\s+~', ' ', $c);
            $c = preg_replace('~\s*([{};])\s*~', '$1', $c);
            $c = str_replace(';}', '}', $c);

            $css .= trim($c) . "\n";
        }

        if (!wp_mkdir_p($dir) || false === file_put_contents($file, $css)) {
            return; // не вийшло записати, лишаємо окремі файли
        }
    }

    foreach (array_keys($items) as $handle) {
        wp_dequeue_style($handle);
    }
    wp_enqueue_style('tm-bundle', $url, array(), null);
}, 999);