/* Калькулятор шкафа (полная страница).
   Цена считается на сервере: скрипт отправляет выбор и показывает ответ.
   Без JS на странице остаётся цена по умолчанию, посчитанная PHP. */
(function () {
  "use strict";

  var form = document.querySelector("[data-calc-wrd]");
  if (!form || !window.fetch) {
    return;
  }

  var PICKS = ["type", "height", "depth", "sys", "fill", "interior"];

  var priceEl = form.querySelector("[data-calc-price]");
  var perEl = form.querySelector("[data-calc-per]");
  var errEl = form.querySelector("[data-calc-error]");
  var doorsEl = form.querySelector("[data-calc-doors]");
  var widthOut = form.querySelector("[data-calc-width-out]");
  var range = form.querySelector('input[name="width"]');

  var endpoint = form.getAttribute("data-endpoint");
  var tPer = form.getAttribute("data-per");
  var errBusy = form.getAttribute("data-err-busy");
  var errFail = form.getAttribute("data-err-fail");

  var timer = null;
  var controller = null;
  var tracked = false;

  function fmt(n) {
    return String(n).replace(/\B(?=(\d{3})+(?!\d))/g, "\u00A0");
  }

  function sum(name, text) {
    var el = form.querySelector('[data-sum="' + name + '"]');
    if (el) {
      el.textContent = text;
    }
  }

  function checkedInput(name) {
    return form.querySelector('input[name="' + name + '"]:checked');
  }

  function widthText() {
    return parseFloat(range.value).toFixed(1).replace(".", ",");
  }

  function updateSummary() {
    PICKS.forEach(function (name) {
      if (name === "height" || name === "depth") {
        return;
      }
      var c = checkedInput(name);
      if (c) {
        sum(name, c.getAttribute("data-label"));
      }
    });
    var h = checkedInput("height");
    var d = checkedInput("depth");
    sum(
      "size",
      widthText() +
        " × " +
        (h ? parseFloat(h.value).toFixed(1).replace(".", ",") : "") +
        " × " +
        (d ? (parseInt(d.value, 10) / 100).toFixed(1).replace(".", ",") : "") +
        " м",
    );
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
    perEl.textContent = "≈ " + fmt(data.per_meter) + " " + tPer;
    if (doorsEl && data.doors) {
      doorsEl.textContent = data.doors;
    }
    priceEl.classList.remove("is-loading");
  }

  function params() {
    var p = new URLSearchParams();
    PICKS.forEach(function (name) {
      var c = checkedInput(name);
      if (c) {
        p.set(name, c.value);
      }
    });
    p.set("width", range.value);
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
    updateSummary();
    clearTimeout(timer);
    timer = setTimeout(request, 250);

    if (!tracked) {
      tracked = true;
      if (typeof window.gtag === "function") {
        window.gtag("event", "calculator_used", { calc_type: "wardrobe" });
      }
    }
  }

  range.addEventListener("input", function () {
    widthOut.textContent = widthText();
    schedule();
  });

  form.addEventListener("change", function (e) {
    var t = e.target;
    if (!t || t.name === "width" || t.name.indexOf("lead_") === 0) {
      return;
    }
    schedule();
  });

  /* ===== Заявка с параметрами ===== */
  var leadBox = form.querySelector("[data-calc-lead]");
  var sendBtn = form.querySelector("[data-calc-send]");
  var thanks = form.querySelector("[data-calc-thanks]");
  var leadUrl = form.getAttribute("data-lead");
  var errPhone = form.getAttribute("data-err-phone");
  var errSend = form.getAttribute("data-err-send");

  function field(name) {
    return form.querySelector('[name="' + name + '"]');
  }

  function sendLead() {
    var phone = field("lead_phone").value.trim();
    if (phone.replace(/\D/g, "").length < 9) {
      showError(errPhone);
      field("lead_phone").focus();
      return;
    }
    showError("");
    sendBtn.disabled = true;

    var body = {
      lead_name: field("lead_name").value.trim(),
      lead_phone: phone,
      lead_site: field("lead_site").value,
      width: range.value,
      page: window.location.href,
    };
    PICKS.forEach(function (name) {
      var c = checkedInput(name);
      if (c) {
        body[name] = c.value;
      }
    });

    fetch(leadUrl, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
      },
      body: JSON.stringify(body),
    })
      .then(function (res) {
        if (res.status === 400) {
          throw new Error("phone");
        }
        if (res.status === 429) {
          throw new Error("busy");
        }
        if (!res.ok) {
          throw new Error("fail");
        }
        return res.json();
      })
      .then(function () {
        leadBox.hidden = true;
        thanks.hidden = false;
        if (typeof window.gtag === "function") {
          window.gtag("event", "generate_lead", { calc_type: "wardrobe" });
        }
      })
      .catch(function (err) {
        sendBtn.disabled = false;
        var m = err && err.message;
        showError(m === "phone" ? errPhone : m === "busy" ? errBusy : errSend);
      });
  }

  if (leadBox && sendBtn && thanks) {
    sendBtn.addEventListener("click", sendLead);
    field("lead_phone").addEventListener("keydown", function (e) {
      if (e.key === "Enter") {
        e.preventDefault();
        sendLead();
      }
    });
  }

  updateSummary();
})();
