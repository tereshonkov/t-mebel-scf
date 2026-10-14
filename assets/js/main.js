/* Глобальний скрипт сайту. Секційні скрипти лежать в assets/js/sections/. */

/* Мобільне меню: відкриття бургером, закриття хрестиком, Esc, кліком по пункту. */
(() => {
  const burger = document.querySelector('.burger');
  const menu = document.getElementById('mobile-menu');
  if (!burger || !menu) return;

  const closeBtn = menu.querySelector('.mobile-menu__close');

  const setOpen = (open) => {
    menu.classList.toggle('is-open', open);
    menu.setAttribute('aria-hidden', open ? 'false' : 'true');
    burger.setAttribute('aria-expanded', open ? 'true' : 'false');
    document.body.classList.toggle('menu-open', open);
    if (open) closeBtn?.focus();
  };

  burger.addEventListener('click', () => setOpen(true));
  closeBtn?.addEventListener('click', () => { setOpen(false); burger.focus(); });

  // Клік по будь-якому посиланню в меню закриває його (важливо для якорів типу #lead).
  menu.querySelectorAll('a').forEach((a) => a.addEventListener('click', () => setOpen(false)));

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && menu.classList.contains('is-open')) setOpen(false);
  });

  // Якщо екран розширили до десктопа з відкритим меню, закриваємо.
  window.matchMedia('(min-width: 1025px)').addEventListener('change', (e) => {
    if (e.matches) setOpen(false);
  });
})();