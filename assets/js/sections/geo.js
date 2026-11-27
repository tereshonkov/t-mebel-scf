/**
 * Карта в секции "География".
 * Leaflet (assets/vendor/leaflet) грузится только когда карта подъезжает к экрану,
 * до этого на странице нет ни скрипта карты, ни плиток.
 * Места берутся из data-points: {n: название, a: широта, o: долгота, g: 1 район / 2 область}.
 */
(function () {
  var maps = document.querySelectorAll(".geo__map[data-points]");
  if (!maps.length) {
    return;
  }

  // Папка Leaflet рядом с этим файлом: .../assets/js/sections/geo.js -> .../assets/vendor/leaflet/
  var me = document.currentScript && document.currentScript.src;
  var base = me
    ? me.replace(/js\/sections\/geo\.js.*$/, "vendor/leaflet/")
    : "";
  var KH = [49.9935, 36.2304]; // центр Харкова
  var NEAR_KM = 30; // ближе этого = передмістя
  var loading = null;

  function loadLeaflet() {
    if (window.L) {
      return Promise.resolve();
    }
    if (loading) {
      return loading;
    }
    loading = new Promise(function (resolve, reject) {
      var css = document.createElement("link");
      css.rel = "stylesheet";
      css.href = base + "leaflet.css";
      document.head.appendChild(css);

      var js = document.createElement("script");
      js.src = base + "leaflet.js";
      js.onload = resolve;
      js.onerror = reject;
      document.head.appendChild(js);
    });
    return loading;
  }

  // Расстояние в км между двумя точками
  function km(a1, o1, a2, o2) {
    var r = Math.PI / 180;
    var x = Math.sin(((a2 - a1) * r) / 2);
    var y = Math.sin(((o2 - o1) * r) / 2);
    var h = x * x + Math.cos(a1 * r) * Math.cos(a2 * r) * y * y;
    return 12742 * Math.asin(Math.sqrt(h));
  }

  function esc(s) {
    var d = document.createElement("div");
    d.textContent = s;
    return d.innerHTML;
  }

  function build(el) {
    var points, labels;
    try {
      points = JSON.parse(el.getAttribute("data-points")) || [];
      labels = JSON.parse(el.getAttribute("data-labels")) || {};
    } catch (e) {
      return;
    }

    // Делим места: районы города, передмістя (до 30 км), дальняя область
    var areas = [],
      near = [],
      far = [];
    points.forEach(function (p) {
      if (p.g === 1) {
        areas.push(p);
      } else if (km(KH[0], KH[1], p.a, p.o) <= NEAR_KM) {
        near.push(p);
      } else {
        far.push(p);
      }
    });

    var map = L.map(el, { scrollWheelZoom: false, zoomSnap: 0.25 });
    map.zoomControl.setPosition("bottomright");
    L.tileLayer("https://tile.openstreetmap.org/{z}/{x}/{y}.png", {
      maxZoom: 16,
      attribution:
        '© <a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noopener">OpenStreetMap</a>',
    }).addTo(map);

    // Подпись слева или справа от точки: западнее центра группы = слева
    function pin(p, centerLng, dark) {
      var side = p.o < centerLng ? "l" : "r";
      return L.marker([p.a, p.o], {
        title: p.n,
        alt: p.n,
        icon: L.divIcon({
          className: "",
          iconSize: [0, 0],
          html:
            '<span class="geo-dot' +
            (dark ? " geo-dot--dark" : "") +
            '"></span>' +
            '<span class="geo-pin geo-pin--' +
            side +
            '">' +
            esc(p.n) +
            "</span>",
        }),
      });
    }

    function dot(p) {
      return L.marker([p.a, p.o], {
        title: p.n,
        alt: p.n,
        icon: L.divIcon({
          className: "",
          iconSize: [0, 0],
          html: '<span class="geo-dot"></span>',
        }),
      });
    }

    function cityLabel(small) {
      return L.marker(KH, {
        zIndexOffset: 1000,
        alt: labels.city,
        icon: L.divIcon({
          className: "",
          iconSize: [0, 0],
          html: small
            ? '<span class="geo-dot geo-dot--dark"></span><span class="geo-pin geo-pin--r geo-pin--main">' +
              esc(labels.city) +
              "</span>"
            : '<span class="geo-pin geo-pin--c geo-pin--main">' +
              esc(labels.city) +
              "</span>",
        }),
      });
    }

    function centerLng(list) {
      if (!list.length) {
        return KH[1];
      }
      return (
        list.reduce(function (s, p) {
          return s + p.o;
        }, 0) / list.length
      );
    }

    function bounds(list, withCity) {
      var b = list.map(function (p) {
        return [p.a, p.o];
      });
      if (withCity) {
        b.push(KH);
      }
      return L.latLngBounds(b);
    }

    // Три вида, как в макете. Вид без мест не показываем.
    var views = [];
    var bigCity = cityLabel(false);
    var smallCity = cityLabel(true);

    if (far.length) {
      views.push({
        key: "oblast",
        label: labels.oblast,
        layer: L.layerGroup(
          far
            .map(function (p) {
              return pin(p, KH[1]);
            })
            .concat(near.map(dot), [bigCity]),
        ),
        bounds: bounds(far.concat(near), true),
        pad: [40, 70],
      });
    }
    if (near.length) {
      views.push({
        key: "suburbs",
        label: labels.suburbs,
        layer: L.layerGroup(
          near
            .map(function (p) {
              return pin(p, KH[1]);
            })
            .concat([smallCity]),
        ),
        bounds: bounds(near, true),
        pad: [50, 110],
      });
    }
    if (areas.length) {
      var mid = centerLng(areas);
      views.push({
        key: "areas",
        label: labels.areas,
        layer: L.layerGroup(
          areas.map(function (p) {
            return pin(p, mid, true);
          }),
        ),
        bounds: bounds(areas, false),
        pad: [40, 110],
      });
    }

    if (!views.length) {
      return;
    }

    // Переключатель видов
    var bar = document.createElement("div");
    bar.className = "geo-switch";
    var buttons = views.map(function (v, i) {
      var b = document.createElement("button");
      b.type = "button";
      b.textContent = v.label;
      b.addEventListener("click", function () {
        show(i, true);
      });
      bar.appendChild(b);
      return b;
    });
    if (views.length > 1) {
      el.appendChild(bar);
      L.DomEvent.disableClickPropagation(bar);
    }

    var cur = -1;
    function show(i, anim) {
      if (cur >= 0) {
        map.removeLayer(views[cur].layer);
      }
      cur = i;
      var v = views[i];
      v.layer.addTo(map);
      buttons.forEach(function (b, k) {
        b.classList.toggle("is-on", k === i);
      });
      var opt = {
        paddingTopLeft: [v.pad[1], v.pad[0] + 40],
        paddingBottomRight: [v.pad[1], v.pad[0]],
      };
      if (anim) {
        map.flyToBounds(v.bounds, Object.assign({ duration: 0.8 }, opt));
      } else {
        map.fitBounds(v.bounds, opt);
      }
    }

    // Клик по "Харків": область -> передмістя -> райони
    function next(key) {
      for (var k = 0; k < views.length; k++) {
        if (views[k].key === key) {
          show(k, true);
          return;
        }
      }
    }
    bigCity.on("click", function () {
      next("suburbs");
    });
    smallCity.on("click", function () {
      next("areas");
    });

    show(0, false);
    window.addEventListener("resize", function () {
      map.invalidateSize();
    });
  }

  // Ленивый старт: за 300 px до появления карты на экране
  function start(el) {
    loadLeaflet()
      .then(function () {
        build(el);
      })
      .catch(function () {});
  }

  if ("IntersectionObserver" in window) {
    var io = new IntersectionObserver(
      function (entries) {
        entries.forEach(function (e) {
          if (e.isIntersecting) {
            io.unobserve(e.target);
            start(e.target);
          }
        });
      },
      { rootMargin: "300px" },
    );
    maps.forEach(function (el) {
      io.observe(el);
    });
  } else {
    maps.forEach(start);
  }
})();
