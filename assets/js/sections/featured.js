/* Слайдер "Избранные проекты": стрелки, миниатюры, клавиши ← → */
document.querySelectorAll('.fp').forEach((root) => {
  const groups = [
    root.querySelectorAll('.fp-frame'),
    root.querySelectorAll('.fp-caption__item'),
    root.querySelectorAll('.fp-desc'),
    root.querySelectorAll('.fp-thumb'),
  ];
  const total = groups[0].length;
  if (total < 2) return;

  const cur = root.querySelector('.fp__counter-cur');
  let index = 0;

  const show = (i) => {
    index = ((i % total) + total) % total;
    groups.forEach((list) => list.forEach((el, k) => el.classList.toggle('is-active', k === index)));
    if (cur) cur.textContent = String(index + 1).padStart(2, '0');
  };

  root.querySelectorAll('.fp__arrow').forEach((btn) =>
    btn.addEventListener('click', () => show(index + (btn.dataset.dir === 'prev' ? -1 : 1)))
  );
  groups[3].forEach((thumb, k) => thumb.addEventListener('click', () => show(k)));

  root.addEventListener('keydown', (e) => {
    if (e.key === 'ArrowLeft') show(index - 1);
    if (e.key === 'ArrowRight') show(index + 1);
  });
});