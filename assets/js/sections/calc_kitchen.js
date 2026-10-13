/* Калькулятор кухни (короткая версия).
   Цена считается на сервере: скрипт только отправляет выбор и показывает ответ.
   Без JS на странице остаётся цена по умолчанию, посчитанная PHP. */
(function () {
  "use strict";

  var form = document.querySelector("[data-calc]");
  if (!form || !window.fetch) {
    return;
  }

  var priceEl = document.querySelector("[data-calc-price]");
  var errEl = document.querySelector("[data-calc-error]");
  var lengthOut = document.querySelector("[data-calc-length-out]");
  var range = form.querySelector('input[name="length"]');

  var endpoint = form.getAttribute("data-endpoint");
  var errBusy = form.getAttribute("data-err-busy");
  var errFail = form.getAttribute("data-err-fail");

  var timer = null;
  var controller = null;
  var tracked = false;

  function fmt(n) {
    return String(n).replace(/\B(?=(\d{3})+(?!\d))/g, "\u00A0");
  }

  function showError(msg) {
    if (!errEl) {
      return;
    }
    errEl.textContent = msg || "";
    errEl.hidden = !msg;
  }

  function render(data) {
    priceEl.textContent = fmt(data.min) + " – " + fmt(data.max) + " грн";
    priceEl.classList.remove("is-loading");
  }

  function params() {
    var p = new URLSearchParams();
    ["shape", "facade"].forEach(function (name) {
      var checked = form.querySelector('input[name="' + name + '"]:checked');
      if (checked) {
        p.set(name, checked.value);
      }
    });
    p.set("length", range.value);
    return p;
  }

  function request() {
    if (controller) {
      controller.abort();
    }
    controller = window.AbortController ? new AbortController() : null;
    priceEl.classList.add("is-loading");
    showError("");

    fetch(endpoint + "?" + params().toString(), {
      headers: { Accept: "application/json" },
      signal: controller ? controller.signal : undefined,
    })
      .then(function (res) {
        if (res.status === 429) {
          throw new Error("busy");
        }
        if (!res.ok) {
          throw new Error("fail");
        }
        return res.json();
      })
      .then(render)
      .catch(function (err) {
        if (err && err.name === "AbortError") {
          return;
        }
        priceEl.classList.remove("is-loading");
        showError(err && err.message === "busy" ? errBusy : errFail);
      });
  }

  function schedule() {
    clearTimeout(timer);
    timer = setTimeout(request, 250);

    if (!tracked) {
      tracked = true;
      if (typeof window.gtag === "function") {
        window.gtag("event", "calculator_used", { calc_type: "kitchen" });
      }
    }
  }

  range.addEventListener("input", function () {
    lengthOut.textContent = parseFloat(range.value)
      .toFixed(1)
      .replace(".", ",");
    schedule();
  });

  form.addEventListener("change", function (e) {
    if (e.target && e.target.name !== "length") {
      schedule();
    }
  });
})();
