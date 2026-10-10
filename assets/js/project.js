/**
 * Слайдер фото на странице проекта.
 * Переключает класс is-active у большого фото и у миниатюры.
 */
(function () {
	document.querySelectorAll('[data-pj-gallery]').forEach(function (root) {
		var frames = root.querySelectorAll('.pj-frame');
		var thumbs = root.querySelectorAll('[data-pj-thumb]');
		var count  = root.querySelector('[data-pj-count]');
		var main   = root.querySelector('.pj-main');
		var total  = frames.length;
		var current = 0;

		if (total < 2) {
			return; // одно фото: листать нечего
		}

		function pad(n) {
			return n < 10 ? '0' + n : String(n);
		}

		function go(i) {
			// "по кругу": после последнего первое, перед первым последнее
			i = ((i % total) + total) % total;

			frames[current].classList.remove('is-active');
			if (thumbs[current]) thumbs[current].classList.remove('is-active');

			current = i;

			frames[current].classList.add('is-active');
			if (thumbs[current]) thumbs[current].classList.add('is-active');
			if (count) count.textContent = pad(current + 1) + ' / ' + pad(total);
		}

		// Стрелки
		root.querySelector('[data-pj-prev]').addEventListener('click', function () {
			go(current - 1);
		});
		root.querySelector('[data-pj-next]').addEventListener('click', function () {
			go(current + 1);
		});

		// Миниатюры: номер фото лежит в data-pj-thumb
		thumbs.forEach(function (thumb) {
			thumb.addEventListener('click', function () {
				go(parseInt(thumb.dataset.pjThumb, 10));
			});
		});

		// Клавиши ← → когда фокус внутри галереи
		root.addEventListener('keydown', function (e) {
			if (e.key === 'ArrowLeft') go(current - 1);
			if (e.key === 'ArrowRight') go(current + 1);
		});

		// Свайп пальцем на телефоне
		var startX = null;
		main.addEventListener('touchstart', function (e) {
			startX = e.touches[0].clientX;
		}, { passive: true });

		main.addEventListener('touchend', function (e) {
			if (startX === null) return;
			var dx = e.changedTouches[0].clientX - startX;
			if (Math.abs(dx) > 40) {
				go(current + (dx < 0 ? 1 : -1));
			}
			startX = null;
		});
	});
})();