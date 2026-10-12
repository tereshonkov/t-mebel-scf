# T-Mebel (WordPress, класична тема)

Сайт меблів на замовлення в Харкові, t-mebel.com.ua. Двомовний (UA за замовчуванням, RU у `/ru/`).
Сторінки збираються з секцій (Flexible Content, плагін Secure Custom Fields).
Кожна секція це готовий "паттерн" у PHP: макет зафіксований, редактор змінює тільки поля і порядок.

Стек: WordPress, Secure Custom Fields, Polylang (Free), Rank Math (Free), Contact Form 7, Koko Analytics.
Запущено на основному домені 12.10.2026 (раніше був Next.js на Vercel).

## Структура

```
t-mebel-scf/
  style.css                       лише заголовок теми
  functions.php                   тільки підключення модулів з inc/ ($tm_modules)
  header.php, footer.php          каркас сторінки
  front-page.php                  головна: секції
  page.php                        звичайна сторінка: секції або the_content()
  index.php, 404.php              запасні шаблони
  archive-project.php             архів проєктів (/service/)
  taxonomy-project_category.php   рубрики проєктів (/service/{рубрика}/)
  single-project.php              сторінка проєкту (/project/{slug}/), галерея
  archive-guide.php               архів статей (/guides/)
  taxonomy-guide_category.php     рубрики статей (/guides/topic/{рубрика}/)
  single-guide.php                сторінка статті

  inc/
    setup.php                     підтримка теми, меню, розміри зображень
    assets.php                    CSS/JS: глобальні + тільки для секцій на сторінці
    helpers.php                   tm_t(), tm_opt(), tm_image(), tm_link(), tm_page_layouts()
    polylang.php                  переклад CPT, tm_languages()
    scf.php                       acf-json, сторінка опцій, tm_render_sections()
    nav.php                       меню шапки: walker і мега-меню
    cpt-project.php               тип запису "Проєкти" і рубрики
    cpt-guide.php                 тип запису "Корисні статті" і рубрики
    popup.php                     модальна форма заявки (підключення, дані)
    cf7.php                       інтеграція Contact Form 7
    telegram.php                  відправка заявок у Telegram (токен береться з конфігу сервера)
    seo.php                       title/description архівів /service/ і /guides/ для двох мов
    redirects.php                 редиректи зі старих адрес Next.js (parse_request)
    search.php                    пошук віддає 404, без SearchAction у розмітці
    security.php                  закриває список користувачів у REST API
    admin-home.php                після входу відкриває "Аналітику" (Koko Analytics)
    editor-access.php             роль Редактор: право дивитися статистику

  template-parts/
    header/site-header.php        шапка
    footer/site-footer.php        футер
    popup/lead.php                форма заявки у спливаючому вікні
    projects/work-card.php        картка проєкту
    sections/{layout}.php         по одному файлу на макет секції

  assets/
    css/base.css                  токени (кольори, шрифти, відступи) і база
    css/header.css, footer.css, buttons.css, popup.css
    css/project.css, guides.css, guide-single.css, text-page.css, 404.css
    css/sections/{layout}.css     стилі секції (підключаються лише там, де секція є)
    js/main.js, popup.js, project.js
    js/sections/{layout}.js

  acf-json/                       групи полів SCF у JSON (під git)
  languages/
```

## Правила

1. Одна секція = три файли з однаковою назвою: `template-parts/sections/{layout}.php`,
   `assets/css/sections/{layout}.css`, (за потреби) `assets/js/sections/{layout}.js`.
   Назва макета в SCF (`layout name`) має збігатися з назвою файлу.
2. Кольори і шрифти тільки через змінні з `base.css` (`var(--c-gold)`), без жорстких значень у секціях.
3. Класи за схемою БЕМ: `.who`, `.who__title`, `.who-card`.
4. Екранування на виході: `esc_html()`, `esc_url()`, `esc_attr()`, `wp_kses_post()`.
   Зображення тільки через `tm_image()`.
5. Тексти двома мовами: у кожної мовної версії сторінки свої поля. Тексти в коді шаблонів
   через `tm_t( 'українською', 'по-русски' )`. Глобальні дані (телефон, адреса, графік) в опціях
   з суфіксами `_ua`/`_ru`, читати через `tm_opt('phone')`.
6. Поля SCF зберігаються в `acf-json/`. Після змін в адмінці забираємо JSON із сервера і комітимо.
7. Логіки у шаблонах мінімум: запити, фільтри і помічники живуть в `inc/`.
8. Заголовки: один H1 на сторінці, далі H2, H3 по порядку. Декоративні картинки всередині
   посилань із текстом мають `alt=""`.
9. Секретів у репозиторії немає (токен Telegram, паролі БД лишаються в конфігу сервера).

## Як додати нову секцію

1. В адмінці: група "Секції" → Flexible Content → додати макет `my_layout` з полями.
2. `template-parts/sections/my_layout.php`: читати поля через `get_sub_field()`.
3. `assets/css/sections/my_layout.css` (за потреби `assets/js/sections/my_layout.js`).
4. Забрати `acf-json/` із сервера і закомітити.

## Як додати новий модуль

1. Створити `inc/my-module.php` (на початку `if ( ! defined( 'ABSPATH' ) ) { exit; }`).
2. Додати рядок `'inc/my-module.php',` у масив `$tm_modules` у `functions.php`.
3. Якщо сайт перестав відкриватися після правки, прибрати цей рядок із `$tm_modules`.

## SEO

- Rank Math Free: шаблон title архіву єдиний для двох мов, тому для `/service/` і `/guides/`
  заголовки й описи підміняє `inc/seo.php`.
- Фокусні ключі, SEO title і description статей зберігаються в мета-полях Rank Math (задавалися разово).
- Для сторінок Primary taxonomy у Rank Math стоїть "Немає" (інакше мова потрапляє в хлібні крихти).
- Локальне SEO: тип організації Furniture Store, автор у розмітці "Команда T-Mebel".
- Карта сайту `sitemap_index.xml`: сторінки, проєкти, статті, рубрики проєктів. Порожні терміни вимкнено.
- Старі адреси Next.js перенаправляються однією ланкою (`inc/redirects.php`):
  `/product/{id}` та `/service/{id}` на проєкт, `/blog` на `/guides/`, `/uk/...` без префікса.
- Пошук `/?s=` віддає 404 (`inc/search.php`). Сторінки авторів вимкнені в Rank Math.

## Сервер і обслуговування

- Хостинг UAHosting (DirectAdmin, без SSH). Файли в `domains/t-mebel.com.ua/public_html/`.
- В `.htaccess` кеш статики лежить ВИЩЕ блоку `# BEGIN WordPress`. Усередині блоку WordPress нічого свого не тримати.
- Якщо карта сайту або сторінки раптом віддають 404: Налаштування, Постійні посилання, "Зберегти".
- Перед оновленням плагінів і теми робити резервну копію на стороні хостингу.
- У `wp-config.php`: `WP_DEBUG`, `WP_DEBUG_LOG`, `WP_DEBUG_DISPLAY` мають бути `false`.
- Одноразові скрипти (імпорт, правки в базі) кладемо як mu-plugin, запускаємо і одразу видаляємо.
- DNS: `@` і `www` ведуть на сервер хостингу, пошта (MX, TXT) налаштована окремо і не змінюється.

## Ролі

- Адміністратор і Редактор після входу потрапляють на "Аналітику" (Koko Analytics).
  Звичайна Консоль: `/wp-admin/index.php?tm_classic=1`.
- Редактор редагує проєкти, статті і сторінки, налаштувань і плагінів не бачить.

## Етапи (виконано)

- [x] Каркас, завантажувач секцій, помічники
- [x] Шапка, футер, мега-меню, перемикач мов
- [x] Секції головної, контакти, "Про нас", політика
- [x] Проєкти (архів, рубрики, сторінка з галереєю), 23 проєкти UA+RU
- [x] Корисні статті (архів, рубрики, сторінка), 22 статті UA+RU
- [x] Форма заявки (popup, CF7, Telegram)
- [x] SEO: заголовки, описи, схема, редиректи, карта сайту
- [x] Переїзд з wp-піддомену на основний домен, запуск 12.10.2026