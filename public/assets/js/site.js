/*!
 * Arcates Web Site — site.js
 * Bagimliliksiz hareket ve etkileşim motoru. DOCS.md 7.
 */
(function () {
  'use strict';

  var doc = document;
  var root = doc.documentElement;
  var THRESHOLD = 0.15;
  var ROOT_MARGIN = '0px 0px -8% 0px';
  var HEAD_STUCK_AT = 24;
  var PARALLAX_LIMIT = 1.3;
  var HERO_SETTLE = 1400;

  var reduceQuery = window.matchMedia
    ? window.matchMedia('(prefers-reduced-motion: reduce)')
    : { matches: false, addEventListener: null };

  function prefersReducedMotion() {
    return reduceQuery.matches === true;
  }

  var scrollTasks = [];
  var ticking = false;

  function onScrollFrame() {
    ticking = false;
    var y = window.pageYOffset || root.scrollTop || 0;
    for (var i = 0; i < scrollTasks.length; i++) scrollTasks[i](y);
  }

  function requestScrollFrame() {
    if (ticking) return;
    ticking = true;
    window.requestAnimationFrame(onScrollFrame);
  }

  function addScrollTask(task) {
    scrollTasks.push(task);
  }

  function revealAll() {
    var nodes = doc.querySelectorAll('[data-reveal]');
    for (var i = 0; i < nodes.length; i++) nodes[i].classList.add('is-visible');
  }

  function initReveal() {
    var nodes = doc.querySelectorAll('[data-reveal]');
    if (!nodes.length) return;

    if (!('IntersectionObserver' in window) || prefersReducedMotion()) {
      revealAll();
      return;
    }

    var groups = doc.querySelectorAll('[data-reveal-group]');
    for (var g = 0; g < groups.length; g++) {
      var items = groups[g].querySelectorAll('[data-reveal]');
      var stepped = groups[g].hasAttribute('data-reveal-steps');
      for (var i = 0; i < items.length && i < 6; i++) {
        var explicit = parseInt(items[i].getAttribute('data-reveal-step'), 10);
        var index = isNaN(explicit) ? i : explicit;
        if (index <= 0) continue;
        items[i].classList.add((stepped ? 's' : 'd') + Math.min(index, stepped ? 3 : 6));
      }
    }

    var observer = new IntersectionObserver(function (entries) {
      for (var i = 0; i < entries.length; i++) {
        if (!entries[i].isIntersecting) continue;
        entries[i].target.classList.add('is-visible');
        observer.unobserve(entries[i].target);
      }
    }, { threshold: THRESHOLD, rootMargin: ROOT_MARGIN });

    for (var n = 0; n < nodes.length; n++) {
      var rect = nodes[n].getBoundingClientRect();
      if (rect.top < window.innerHeight * 0.9) {
        nodes[n].classList.add('is-visible');
      } else {
        observer.observe(nodes[n]);
      }
    }
  }

  function initHead() {
    var head = doc.querySelector('[data-site-head]');
    var bar = doc.querySelector('[data-progress] .progress__bar');
    if (!head && !bar) return;

    addScrollTask(function (y) {
      if (head) head.classList.toggle('is-stuck', y > HEAD_STUCK_AT);
      if (bar) {
        var height = doc.body.scrollHeight - window.innerHeight;
        var ratio = height > 0 ? Math.min(1, Math.max(0, y / height)) : 0;
        bar.style.transform = 'scaleX(' + ratio.toFixed(4) + ')';
      }
    });
  }

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

    doc.addEventListener('keydown', function (event) {
      if (event.key !== 'Escape' || toggle.getAttribute('aria-expanded') !== 'true') return;
      toggle.setAttribute('aria-expanded', 'false');
      panel.classList.remove('is-open');
      toggle.focus();
    });
  }

  function initHero() {
    var line = doc.querySelector('.shape--line path');
    if (line && typeof line.getTotalLength === 'function') {
      line.style.setProperty('--draw-length', Math.ceil(line.getTotalLength()));
    }
    if (prefersReducedMotion()) return;

    window.setTimeout(function () {
      var shapes = doc.querySelectorAll('.shapes .shape');
      for (var i = 0; i < shapes.length; i++) shapes[i].classList.add('is-floating');
    }, HERO_SETTLE);
  }

  function initParallax() {
    if (prefersReducedMotion()) return;
    var nodes = doc.querySelectorAll('[data-depth]');
    if (!nodes.length || window.innerWidth <= 720) return;

    addScrollTask(function (y) {
      var limit = window.innerHeight * PARALLAX_LIMIT;
      if (y > limit) return;
      for (var i = 0; i < nodes.length; i++) {
        var depth = parseFloat(nodes[i].getAttribute('data-depth')) || 0;
        nodes[i].style.setProperty('--drift', (y * depth * -0.06).toFixed(2) + 'px');
      }
    });
  }

  function initCoast() {
    var figure = doc.querySelector('[data-coast]');
    if (!figure) return;

    var path = figure.querySelector('.coast__path');
    var dots = figure.querySelectorAll('.coast__dot');
    if (!path || typeof path.getTotalLength !== 'function') {
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

  function initStrip() {
    var track = doc.querySelector('[data-strip]');
    if (!track) return;
    var group = track.querySelector('.strip__group');
    if (!group) return;

    if (track.querySelectorAll('.strip__group').length < 2) {
      var clone = group.cloneNode(true);
      clone.setAttribute('aria-hidden', 'true');
      track.appendChild(clone);
    }

    doc.addEventListener('visibilitychange', function () {
      track.style.animationPlayState = doc.hidden ? 'paused' : 'running';
    });
  }

  /* Ic sayfa icindekiler: gorunen basligi aria-current ile isaretler. */
  function initToc() {
    var navs = doc.querySelectorAll('[data-toc]');
    if (!navs.length) return;

    for (var n = 0; n < navs.length; n++) {
      (function (nav) {
        var links = nav.querySelectorAll('[data-toc-link]');
        if (!links.length) return;
        var headings = [];
        var linkById = {};

        for (var i = 0; i < links.length; i++) {
          var href = links[i].getAttribute('href') || '';
          var id = href.charAt(0) === '#' ? href.slice(1) : '';
          if (!id) continue;
          var heading = doc.getElementById(id);
          if (!heading) continue;
          headings.push(heading);
          linkById[id] = links[i];
        }
        if (!headings.length) return;

        function activate(id) {
          for (var j = 0; j < links.length; j++) links[j].removeAttribute('aria-current');
          if (linkById[id]) linkById[id].setAttribute('aria-current', 'location');
        }

        activate(headings[0].id);
        if (!('IntersectionObserver' in window)) return;

        var visible = {};
        var observer = new IntersectionObserver(function (entries) {
          for (var k = 0; k < entries.length; k++) {
            visible[entries[k].target.id] = entries[k].isIntersecting;
          }
          for (var h = 0; h < headings.length; h++) {
            if (visible[headings[h].id]) {
              activate(headings[h].id);
              break;
            }
          }
        }, { rootMargin: '-18% 0px -68% 0px', threshold: 0 });

        for (var h = 0; h < headings.length; h++) observer.observe(headings[h]);
      })(navs[n]);
    }
  }

  /* Alt CTA ekrana geldiginde sticky kart tekrar etmeyi birakir. */
  function initStickyCta() {
    var cards = doc.querySelectorAll('[data-sticky-cta]');
    var finalCta = doc.querySelector('.section--cta');
    if (!cards.length || !finalCta || !('IntersectionObserver' in window)) return;

    var observer = new IntersectionObserver(function (entries) {
      var hidden = entries[0] && entries[0].isIntersecting;
      for (var i = 0; i < cards.length; i++) cards[i].classList.toggle('is-suppressed', hidden);
    }, { threshold: 0.05 });
    observer.observe(finalCta);
  }

  /* Form alanlari doldukca yerel durum gosterir ve cift gonderimi kilitler. */
  function initFormState() {
    var forms = doc.querySelectorAll('.form');
    for (var f = 0; f < forms.length; f++) {
      (function (form) {
        var required = form.querySelectorAll('input[required], select[required], textarea[required]');
        var submit = form.querySelector('button[type="submit"]');
        if (!submit) return;

        function setFieldState(field, force) {
          if (!force && field.getAttribute('data-touched') !== '1') return;
          var valid = field.checkValidity();
          field.setAttribute('aria-invalid', valid ? 'false' : 'true');
          var wrapper = field.closest ? field.closest('.field') : null;
          if (wrapper) {
            wrapper.classList.toggle('is-valid', valid);
            wrapper.classList.toggle('is-invalid', !valid);
          }
        }

        function syncButton() {
          submit.disabled = !form.checkValidity();
        }

        for (var i = 0; i < required.length; i++) {
          required[i].addEventListener('input', function () {
            this.setAttribute('data-touched', '1');
            setFieldState(this, false);
            syncButton();
          });
          required[i].addEventListener('change', function () {
            this.setAttribute('data-touched', '1');
            setFieldState(this, false);
            syncButton();
          });
          required[i].addEventListener('blur', function () {
            this.setAttribute('data-touched', '1');
            setFieldState(this, true);
          });
        }

        syncButton();
        form.addEventListener('submit', function (event) {
          if (!form.checkValidity()) {
            event.preventDefault();
            var firstInvalid = null;
            for (var i = 0; i < required.length; i++) {
              required[i].setAttribute('data-touched', '1');
              setFieldState(required[i], true);
              if (!firstInvalid && !required[i].checkValidity()) firstInvalid = required[i];
            }
            if (firstInvalid) firstInvalid.focus();
            return;
          }
          submit.disabled = true;
          submit.setAttribute('aria-busy', 'true');
          form.setAttribute('aria-busy', 'true');
        });
      })(forms[f]);
    }
  }

  function start() {
    initReveal();
    initHead();
    initNav();
    initHero();
    initParallax();
    initCoast();
    initStrip();
    initToc();
    initStickyCta();
    initFormState();

    if (scrollTasks.length) {
      window.addEventListener('scroll', requestScrollFrame, { passive: true });
      window.addEventListener('resize', requestScrollFrame, { passive: true });
      requestScrollFrame();
    }

    if (reduceQuery.addEventListener) {
      reduceQuery.addEventListener('change', function () {
        if (prefersReducedMotion()) revealAll();
      });
    }
  }

  if (doc.readyState === 'loading') doc.addEventListener('DOMContentLoaded', start);
  else start();
})();
