/* Hero-слайдер: перемикає фото за інтервалом, точки керують вручну. */
document.querySelectorAll(".hero-slider").forEach((root) => {
  const slides = root.querySelectorAll(".hero-slider__slide");
  const dots = root.querySelectorAll(".hero-slider__dot");
  if (slides.length < 2) return;

  const delay = parseInt(root.dataset.interval, 10) || 5000;
  const reduce = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
  let current = 0;
  let timer = null;

  const show = (index) => {
    slides[current].classList.remove("is-active");
    dots[current]?.classList.remove("is-active");
    current = (index + slides.length) % slides.length;
    slides[current].classList.add("is-active");
    dots[current]?.classList.add("is-active");
  };

  const start = () => {
    if (reduce) return;
    clearInterval(timer);
    timer = setInterval(() => show(current + 1), delay);
  };

  dots.forEach((dot, i) =>
    dot.addEventListener("click", () => {
      show(i);
      start();
    }),
  );

  start();
});
