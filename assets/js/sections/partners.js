/* Бесшовная лента: дублирует логотипы, пока они не заполнят экран, потом ещё раз (для сдвига на -50%). */
(() => {
  const init = (track) => {
    const box = track.parentElement;
    const original = [...track.children];
    if (!original.length) return;

    const addCopies = (items) => {
      items.forEach((item) => {
        const copy = item.cloneNode(true);
        copy.setAttribute("aria-hidden", "true");
        const img = copy.querySelector("img");
        if (img) img.alt = "";
        track.appendChild(copy);
      });
    };

    // Логотипов мало (3), а экран широкий: дублируем, пока одна половина не станет шире экрана.
    let guard = 0;
    while (track.scrollWidth < box.offsetWidth && guard++ < 20)
      addCopies(original);

    // Вторая половина: точная копия первой.
    addCopies([...track.children]);
    track.classList.add("is-running");
  };

  const run = () => document.querySelectorAll(".marquee-track").forEach(init);

  // Ждём загрузки картинок: до неё ширина ленты неизвестна.
  if (document.readyState === "complete") run();
  else window.addEventListener("load", run);
})();
