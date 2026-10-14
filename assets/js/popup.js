/**
 * Попап-заявка: открыть, закрыть, показать "Дякуємо" после отправки CF7.
 */
(function () {
	var pop = document.getElementById('pp-lead');
	if (!pop) return;

	var formView  = pop.querySelector('[data-pp-form]');
	var doneView  = pop.querySelector('[data-pp-done]');
	var lastFocus = null;

	function open() {
		lastFocus = document.activeElement;
		formView.hidden = false;
		doneView.hidden = true;
		pop.hidden = false;
		document.body.classList.add('pp-open');

		var first = pop.querySelector('input:not([type="hidden"])');
		if (first) setTimeout(function () { first.focus(); }, 60);
	}

	function close() {
		pop.hidden = true;
		document.body.classList.remove('pp-open');
		if (lastFocus) lastFocus.focus();
	}

	// Открыть: ссылка #popup или элемент data-popup="lead"
	document.addEventListener('click', function (e) {
		var trigger = e.target.closest('a[href="#popup"], [data-popup="lead"]');
		if (trigger) {
			e.preventDefault();
			open();
		}
	});

	// Закрыть: клик по фону или по кнопке data-pp-close
	pop.addEventListener('click', function (e) {
		if (e.target === pop || e.target.closest('[data-pp-close]')) {
			close();
		}
	});

	// Закрыть: Esc
	document.addEventListener('keydown', function (e) {
		if (e.key === 'Escape' && !pop.hidden) close();
	});

	// CF7 отправил: показываем "Дякуємо"
	pop.addEventListener('wpcf7mailsent', function () {
		formView.hidden = true;
		doneView.hidden = false;
	});
})();