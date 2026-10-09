# T-Mebel Classic

Класична тема. Сторінки збираються з секцій (Flexible Content, плагін Secure Custom Fields).
Кожна секція це готовий "паттерн" у PHP: макет зафіксований, редактор змінює тільки поля і порядок.

## Структура

```
t-mebel-classic/
  style.css                 лише заголовок теми
  functions.php             тільки підключення модулів з inc/
  header.php, footer.php    каркас сторінки (wp_head, <main>, wp_footer)
  front-page.php            головна: tm_render_sections()
  page.php                  звичайна сторінка: секції, або the_content()
  index.php, 404.php        запасні шаблони
  (далі) single-project.php, archive-project.php, taxonomy-project_category.php

  inc/
    setup.php               підтримка теми, меню, розміри зображень
    assets.php              CSS/JS: глобальні + тільки для секцій на сторінці
    helpers.php             tm_page_layouts(), tm_image(), tm_link(), tm_opt()
    polylang.php            переклад CPT, tm_languages()
    scf.php                 acf-json, сторінка опцій, tm_render_sections()
    cpt-project.php         тип запису "Проєкти"

  template-parts/
    header/site-header.php  шапка
    footer/site-footer.php  футер
    sections/{layout}.php   по одному файлу на макет секції

  assets/
    css/base.css            токени (кольори, шрифти, відступи) і база
    css/header.css, footer.css, buttons.css
    css/sections/{layout}.css   стилі секції (підключаються лише там, де секція є)
    js/main.js, js/sections/{layout}.js
    img/, fonts/ (пізніше)

  acf-json/                 групи полів SCF у JSON (під git)
  languages/
```

## Правила

1. Одна секція = три файли з однаковою назвою: `template-parts/sections/{layout}.php`,
   `assets/css/sections/{layout}.css`, (за потреби) `assets/js/sections/{layout}.js`.
   Назва макета в SCF (`layout name`) має збігатися з назвою файлу.
2. Кольори і шрифти тільки через змінні з `base.css` (`var(--c-gold)`), без жорстких значень у секціях.
3. Класи за схемою БЕМ: `.who`, `.who__title`, `.who-card`. Ті, що були в блочній темі, лишаємо, щоб CSS переїхав без правок.
4. Екранування на виході: `esc_html()`, `esc_url()`, `esc_attr()`, `wp_kses_post()`. Зображення тільки через `tm_image()`.
5. Тексти з бази для двох мов: у кожної мовної версії сторінки свої поля. Глобальні дані (телефон, адреса, графік) в опціях з суфіксами `_ua`/`_ru`, читати через `tm_opt('phone')`.
6. Поля SCF зберігаються в `acf-json/`; після змін в адмінці просто комітимо нові JSON.
7. Логіки у шаблонах мінімум: запити, фільтри і помічники живуть в `inc/`.

## Як додати нову секцію

1. В адмінці: група "Секції" → Flexible Content → додати макет `my_layout` з полями.
2. `template-parts/sections/my_layout.php`: читати поля через `get_sub_field()`.
3. `assets/css/sections/my_layout.css` (за потреби `assets/js/sections/my_layout.js`).
4. Закомітити `acf-json/`.

## Етапи

- [x] 1. Каркас, завантажувач секцій, помічники (цей архів)
- [ ] 2. Шапка і футер у PHP (логотип, меню, перемикач мов, CTA, опції)
- [ ] 3. Група полів "Секції" + пілотна секція (audience)
- [ ] 4. Решта секцій головної
- [ ] 5. Контакти, архів проєктів, сторінка проєкту
