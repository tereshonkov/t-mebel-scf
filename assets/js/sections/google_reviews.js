/**
 * Стрелки слайдера отзывов: листают на ширину видимой части.
 * Стрелка прячется, когда листать в её сторону уже некуда.
 */
document.querySelectorAll(".gr__slider").forEach(function (slider) {
  var track = slider.querySelector(".gr__track");
  var prev = slider.querySelector(".gr__arrow--prev");
  var next = slider.querySelector(".gr__arrow--next");
  if (!track || !prev || !next) {
    return;
  }

  function update() {
    var max = track.scrollWidth - track.clientWidth - 2;
    prev.disabled = track.scrollLeft <= 2;
    next.disabled = track.scrollLeft >= max;
  }

  prev.addEventListener("click", function () {
    track.scrollBy({ left: -track.clientWidth * 0.9 });
  });
  next.addEventListener("click", function () {
    track.scrollBy({ left: track.clientWidth * 0.9 });
  });

  track.addEventListener("scroll", update, { passive: true });
  window.addEventListener("resize", update);
  update();
});
