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
  screenshot.png                  превью теми в адмінці
  .gitignore
  functions.php                   тільки підключення модулів з inc/ ($tm_modules)
  header.php, footer.php          каркас сторінки
  front-page.php                  головна: секції
  page.php                        звичайна сторінка: секції або the_content()
  template-text.php               шаблон "Текстова сторінка" (політика та подібні)
  index.php, 404.php              запасні шаблони
  archive-project.php             архів проєктів (/service/)
  taxonomy-project_category.php   рубрики проєктів (/service/{рубрика}/)
  single-project.php              сторінка проєкту (/project/{slug}/), галерея
  archive-guide.php               архів статей (/guides/)
  taxonomy-guide_category.php     рубрики статей (/guides/topic/{рубрика}/)
  single-guide.php                сторінка статті

  inc/
    setup.php                     підтримка теми, меню, розміри зображень (tm-hero та менші 16:9 для srcset), якість WebP
    assets.php                    CSS/JS: глобальні + тільки для секцій на сторінці
    css-bundle.php                склеює й мінімізує CSS теми в один файл (uploads/tm-cache)
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
    tracking.php                  Google gtag (Ads + GA4), відкладене завантаження, події заявок і дзвінків

  template-parts/
    header/site-header.php        шапка
    footer/site-footer.php        футер
    popup/lead.php                форма заявки у спливаючому вікні
    projects/work-card.php        картка проєкту
    sections/{layout}.php         по одному файлу на макет секції

  assets/
    css/base.css                  токени (кольори, шрифти, відступи) і база
    css/fonts.css                 @font-face для локальних шрифтів
    css/header.css, footer.css, buttons.css, popup.css
    css/project.css, works.css    сторінка проєкту, архів і рубрики проєктів
    css/guides.css, guide-single.css, text-page.css, 404.css
    css/sections/{layout}.css     стилі секції (підключаються лише там, де секція є)
    fonts/                        Jost і Playfair Display (woff2, локально)
    js/main.js, popup.js, project.js
    js/sections/{layout}.js       featured, hero, partners

  acf-json/                       групи полів SCF у JSON (під git)
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

CSS нової секції підхопить `inc/css-bundle.php` автоматично, нічого окремо підключати не треба.

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

## Відстеження

- `inc/tracking.php`: Google Ads (AW-16643061743) і GA4 (G-DSKK22XDCJ). Ідентифікатори і мітки
  конверсій лежать у константах на початку файлу (`TM_AW_LEAD_LABEL`, `TM_AW_PHONE_LABEL`).
- Скрипт gtag.js вантажиться відкладено: при першій дії відвідувача (скрол, клік, клавіша, дотик)
  або через 5 секунд після повного завантаження сторінки. Це потрібно для швидкості в PageSpeed.
- Події: відправлена форма CF7 дає `generate_lead`, клік на `tel:` дає `phone_click`.
  Мітки конверсій Google Ads поки порожні, реклама не запущена.
- Для адміністраторів і залогінених редакторів відстеження вимкнене.
- Не ставити одночасно плагін Site Kit і цей модуль, інакше конверсії рахуватимуться двічі.
- Статистика відвідувань: Koko Analytics (без cookie), ролі Адміністратор і Редактор не рахуються.

## Швидкість

- `inc/css-bundle.php`: усі стилі теми (`tm-*`) склеюються й мінімізуються в один файл
  `uploads/tm-cache/bundle-{hash}.css`. Ім'я залежить від списку файлів і часу їх зміни, тому
  після правки CSS створюється новий файл. Старі файли можна видаляти вручну.
  Якщо щось поламалося, прибрати `inc/css-bundle.php` з `$tm_modules`.
- Зображення героя: розміри `tm-hero` (2400x1350) і менші `tm-hero-sm/md/lg` (800/1280/1600 за шириною)
  в одній пропорції 16:9, щоб WordPress формував `srcset`. Перший слайд має `loading="eager"` і
  `fetchpriority="high"`, решта `lazy`. Після зміни розмірів перегенерувати мініатюри потрібних файлів.
- Якість WebP 75 (фільтр `wp_editor_set_quality` у `setup.php`).
- Логотипи партнерів завантажувати у вигляді WebP шириною близько 120 px.
- Кеш статики налаштований в `.htaccess` (зображення рік, CSS і JS місяць).
- PageSpeed (мобільний) міняється від прогона до прогона, оцінювати за медіаною 3-5 замірів.

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
- На головній сторінці редактору можна змінювати тексти і фото всередині блоків, але не видаляти,
  не додавати і не переставляти самі блоки.

## Етапи (виконано)

- [x] Каркас, завантажувач секцій, помічники
- [x] Шапка, футер, мега-меню, перемикач мов
- [x] Секції головної, контакти, "Про нас", політика
- [x] Проєкти (архів, рубрики, сторінка з галереєю), 23 проєкти UA+RU
- [x] Корисні статті (архів, рубрики, сторінка), 22 статті UA+RU
- [x] Форма заявки (popup, CF7, Telegram)
- [x] SEO: заголовки, описи, схема, редиректи, карта сайту
- [x] Переїзд з wp-піддомену на основний домен, запуск 12.10.2026
- [x] Відстеження (GA4, події заявок і дзвінків) і оптимізація швидкості

## Далі

- [ ] Конверсії Google Ads: створити "Заявка з сайта" і "Дзвінок з сайта", внести мітки в `tracking.php`.
- [ ] Редиректи зі старих адрес: зберігати query string (потрібно для `gclid`).
- [ ] Прибрати домен з Vercel, очистити 404-лог Rank Math, повернути TTL DNS на 3600.