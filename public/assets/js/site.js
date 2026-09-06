/*!
 * Arcates Web Site — site.js
 * Animasyon motoru.
 *
 * DOCS.md 7 — degismez kurallar:
 *   1. Yalnizca transform ve opacity animasyonu yapilir.
 *   2. Tum gizli baslangic durumlari `html.js` altinda tanimlidir; bu dosya
 *      calismazsa hicbir icerik gizli kalmaz.
 *   3. prefers-reduced-motion: reduce tanimliysa tum animasyonlar kapanir.
 *   4. Scroll dinleyicileri {passive:true} ile baglanir ve
 *      requestAnimationFrame ile sinirlandirilir.
 *   5. Gorunurluk tespiti IntersectionObserver ile yapilir; desteklenmiyorsa
 *      tum ogeler gorunur duruma alinir.
 *   6. Bir oge bir kez gorundukten sonra unobserve edilir.
 *   7. Sonsuz dongu yalnizca dekoratif sekillerde ve sektor seridindedir.
 *
 * Tek dosya, harici bagimlilik yok.  DOCS.md 2
 */
(function () {
  'use strict';

  var doc = document;
  var root = doc.documentElement;

  /* --- Hareket belirtecleri. DOCS.md 7.2 -------------------------------- */
  var THRESHOLD = 0.15;
  var ROOT_MARGIN = '0px 0px -8% 0px';
  var HEAD_STUCK_AT = 24;          // px — ust menu bu esikten sonra kucur
  var PARALLAX_LIMIT = 1.3;        // ilk ekran yuksekliginin 1.3 kati
  var HERO_SETTLE = 1400;          // ms — acilis toplam suresi

  /* Kural 3: azaltilmis hareket istegi tum motoru kapatir. */
  var reduceQuery = window.matchMedia
    ? window.matchMedia('(prefers-reduced-motion: reduce)')
    : { matches: false, addEventListener: null };

  function prefersReducedMotion() {
    return reduceQuery.matches === true;
  }

  /* --- rAF ile sinirlandirilmis scroll isleyicisi. Kural 4 -------------- */

  var scrollTasks = [];
  var ticking = false;

  function onScrollFrame() {
    ticking = false;
    var y = window.pageYOffset || root.scrollTop || 0;
    for (var i = 0; i < scrollTasks.length; i++) {
      scrollTasks[i](y);
    }
  }

  function requestScrollFrame() {
    if (ticking) return;
    ticking = true;
    window.requestAnimationFrame(onScrollFrame);
  }

  function addScrollTask(task) {
    scrollTasks.push(task);
  }

  /* --- Gorunurluk tespiti. Kurallar 5 ve 6 ------------------------------- */

  function revealAll() {
    var nodes = doc.querySelectorAll('[data-reveal]');
    for (var i = 0; i < nodes.length; i++) {
      nodes[i].classList.add('is-visible');
    }
  }

  function initReveal() {
    var nodes = doc.querySelectorAll('[data-reveal]');
    if (!nodes.length) return;

    // Kural 5: IntersectionObserver yoksa her sey gorunur olur.
    if (!('IntersectionObserver' in window)) {
      revealAll();
      return;
    }

    if (prefersReducedMotion()) {
      revealAll();
      return;
    }

    // Kademe gecikmeleri: kartlar 80 ms, adimlar 140 ms. DOCS.md 7.2
    var groups = doc.querySelectorAll('[data-reveal-group]');
    for (var g = 0; g < groups.length; g++) {
      var items = groups[g].querySelectorAll('[data-reveal]');
      var step = groups[g].hasAttribute('data-reveal-steps');
      for (var i = 0; i < items.length && i < 6; i++) {
        if (i === 0) continue;
        items[i].classList.add((step ? 's' : 'd') + Math.min(i, step ? 3 : 6));
      }
    }

    var observer = new IntersectionObserver(function (entries) {
      for (var i = 0; i < entries.length; i++) {
        if (!entries[i].isIntersecting) continue;
        entries[i].target.classList.add('is-visible');
        // Kural 6: bir kez gorundukten sonra izleme birakilir.
        observer.unobserve(entries[i].target);
      }
    }, { threshold: THRESHOLD, rootMargin: ROOT_MARGIN });

    for (var n = 0; n < nodes.length; n++) {
      // Ilk ekranda zaten gorunen ogeler beklemeden acilir.
      var rect = nodes[n].getBoundingClientRect();
      if (rect.top < window.innerHeight * 0.9) {
        nodes[n].classList.add('is-visible');
        continue;
      }
      observer.observe(nodes[n]);
    }
  }

  /* --- Sayfa ilerleme cubugu ve sabit ust menu. DOCS.md 7.4 -------------- */

  function initHead() {
    var head = doc.querySelector('[data-site-head]');
    var bar = doc.querySelector('[data-progress] .progress__bar');

    if (!head && !bar) return;

    addScrollTask(function (y) {
      if (head) {
        head.classList.toggle('is-stuck', y > HEAD_STUCK_AT);
      }

      if (bar) {
        var height = doc.body.scrollHeight - window.innerHeight;
        var ratio = height > 0 ? Math.min(1, Math.max(0, y / height)) : 0;
        bar.style.transform = 'scaleX(' + ratio.toFixed(4) + ')';
      }
    });
  }

  /* --- Mobil menu -------------------------------------------------------- */

  function initNav() {
    var toggle = doc.querySelector('[data-nav-toggle]');
    if (!toggle) return;

    var panel = doc.getElementById(toggle.getAttribute('aria-controls') || '');
    if (!panel) return;

    toggle.addEventListener('click', function () {
      var open = toggle.getAttribute('aria-expanded') === 'true';
      toggle.setAttribute('aria-expanded', open ? 'false' : 'true');
      panel.classList.toggle('is-open', !open);
    });

    // Escape ile kapan.
    doc.addEventListener('keydown', function (event) {
      if (event.key !== 'Escape') return;
      if (toggle.getAttribute('aria-expanded') !== 'true') return;
      toggle.setAttribute('aria-expanded', 'false');
      panel.classList.remove('is-open');
      toggle.focus();
    });
  }

  /* --- Kahraman: grafik cizgisi ve suzulme donguleri --------------------- */

  function initHero() {
    // Grafik cizgisinin uzunlugu SVG'den olculur; CSS degiskeni olarak yazilir.
    var line = doc.querySelector('.shape--line path');
    if (line && typeof line.getTotalLength === 'function') {
      var length = Math.ceil(line.getTotalLength());
      line.style.setProperty('--draw-length', length);
    }

    if (prefersReducedMotion()) return;

    // Kural 7: dongu animasyonu yalnizca dekoratif sekillerde.
    // Acilis bittikten sonra baslar ki iki animasyon carpismasin.
    window.setTimeout(function () {
      var shapes = doc.querySelectorAll('.shapes .shape');
      for (var i = 0; i < shapes.length; i++) {
        shapes[i].classList.add('is-floating');
      }
    }, HERO_SETTLE);
  }

  /* --- Sekil suruklenmesi. DOCS.md 7.4 -----------------------------------
   * data-depth degerine gore farkli hizda kayar; yalnizca ilk ekran
   * yuksekliginin 1.3 kati icinde calisir.
   * -------------------------------------------------------------------- */

  function initParallax() {
    if (prefersReducedMotion()) return;

    var nodes = doc.querySelectorAll('[data-depth]');
    if (!nodes.length) return;

    // Mobilde suzulme ve suruklenme kapalidir (pil ve akicilik).
    // DOCS.md 5.3
    if (window.innerWidth <= 720) return;

    addScrollTask(function (y) {
      var limit = window.innerHeight * PARALLAX_LIMIT;
      if (y > limit) return;

      for (var i = 0; i < nodes.length; i++) {
        var depth = parseFloat(nodes[i].getAttribute('data-depth')) || 0;
        nodes[i].style.setProperty('--drift', (y * depth * -0.06).toFixed(2) + 'px');
      }
    });
  }

  /* --- Bolge haritasi. DOCS.md 5.2, 7.4 ---------------------------------
   * Figurun konumuna gore 0-1 arasi ilerleme hesaplanir, stroke-dashoffset
   * buna gore ayarlanir, ilce noktalari cizgi gectikce yanar.
   * -------------------------------------------------------------------- */

  function initCoast() {
    var figure = doc.querySelector('[data-coast]');
    if (!figure) return;

    var path = figure.querySelector('.coast__path');
    var dots = figure.querySelectorAll('.coast__dot');

    if (!path || typeof path.getTotalLength !== 'function') {
      // Cizgi olculemiyorsa noktalar dogrudan yakilir.
      for (var i = 0; i < dots.length; i++) dots[i].classList.add('is-lit');
      return;
    }

    if (prefersReducedMotion()) {
      for (var r = 0; r < dots.length; r++) dots[r].classList.add('is-lit');
      return;
    }

    var length = 0;

    function measure() {
      length = Math.ceil(path.getTotalLength());
      path.style.setProperty('--coast-length', length);
    }

    measure();

    // Pencere yeniden boyutlandirilinca cizgi uzunlugu yeniden hesaplanir.
    // Test A-07
    var resizeTimer = null;
    window.addEventListener('resize', function () {
      window.clearTimeout(resizeTimer);
      resizeTimer = window.setTimeout(function () {
        measure();
        requestScrollFrame();
      }, 150);
    }, { passive: true });

    addScrollTask(function () {
      var rect = figure.getBoundingClientRect();
      var viewport = window.innerHeight;

      // Figur ekrana girerken 0, ortasina gelince 1.
      var start = viewport * 0.92;
      var end = viewport * 0.28;
      var progress = (start - rect.top) / (start - end);

      if (progress < 0) progress = 0;
      if (progress > 1) progress = 1;

      path.style.setProperty('--coast-offset', (length * (1 - progress)).toFixed(1));

      for (var i = 0; i < dots.length; i++) {
        var at = parseFloat(dots[i].getAttribute('data-at')) || 0;
        dots[i].classList.toggle('is-lit', progress >= at);
      }
    });
  }

  /* --- Sektor seridi. DOCS.md 5 (bolum 3), 7.2 ---------------------------
   * Kesintisiz donmesi icin etiket grubu ikiye katlanir; animasyon -50%
   * kaydiginda ilk grup tam olarak ikincinin yerine gelir, sicrama olmaz.
   * -------------------------------------------------------------------- */

  function initStrip() {
    var track = doc.querySelector('[data-strip]');
    if (!track) return;

    var group = track.querySelector('.strip__group');
    if (!group) return;

    // Kopya zaten sunucudan gelmisse tekrar eklenmez.
    if (track.querySelectorAll('.strip__group').length < 2) {
      var clone = group.cloneNode(true);
      clone.setAttribute('aria-hidden', 'true');
      track.appendChild(clone);
    }

    // Sekme arka plana alinip donuldugunde animasyon takilmasin. Test A-06
    doc.addEventListener('visibilitychange', function () {
      track.style.animationPlayState = doc.hidden ? 'paused' : 'running';
    });
  }

  /* --- Baslatma ---------------------------------------------------------- */

  function start() {
    initReveal();
    initHead();
    initNav();
    initHero();
    initParallax();
    initCoast();
    initStrip();

    // Kural 4: scroll dinleyicisi passive ve rAF ile sinirlandirilmis.
    if (scrollTasks.length) {
      window.addEventListener('scroll', requestScrollFrame, { passive: true });
      window.addEventListener('resize', requestScrollFrame, { passive: true });
      requestScrollFrame();
    }

    // Kullanici hareket tercihini oturum sirasinda degistirebilir.
    if (reduceQuery.addEventListener) {
      reduceQuery.addEventListener('change', function () {
        if (prefersReducedMotion()) revealAll();
      });
    }
  }

  if (doc.readyState === 'loading') {
    doc.addEventListener('DOMContentLoaded', start);
  } else {
    start();
  }
})();
