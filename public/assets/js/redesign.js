/*!
 * Arcates full redesign helpers — R4 foundation.
 * Keeps the existing site.js behavior and adds state text/focus refinements.
 */
(function () {
  'use strict';

  var doc = document;

  function initNavLabel() {
    var toggle = doc.querySelector('[data-nav-toggle]');
    if (!toggle) return;
    var text = toggle.querySelector('.visually-hidden');
    var openLabel = toggle.getAttribute('data-open-label') || '';
    var closeLabel = toggle.getAttribute('data-close-label') || '';

    function sync() {
      var expanded = toggle.getAttribute('aria-expanded') === 'true';
      var label = expanded ? closeLabel : openLabel;
      if (label) toggle.setAttribute('aria-label', label);
      if (text && label) text.textContent = label;
    }

    toggle.addEventListener('click', function () {
      window.requestAnimationFrame(sync);
    });
    doc.addEventListener('keydown', function (event) {
      if (event.key === 'Escape') window.requestAnimationFrame(sync);
    });
    sync();
  }

  function initServerErrorFocus() {
    var summary = doc.querySelector('[data-error-summary]');
    if (!summary) return;
    window.requestAnimationFrame(function () {
      summary.focus({ preventScroll: true });
      summary.scrollIntoView({ block: 'center', behavior: 'auto' });
    });
  }

  function initSubmitLabels() {
    var forms = doc.querySelectorAll('.form');
    for (var i = 0; i < forms.length; i++) {
      (function (form) {
        var submit = form.querySelector('button[type="submit"]');
        if (!submit) return;
        var idle = submit.textContent;
        var busy = submit.getAttribute('data-loading-label') || idle;

        form.addEventListener('submit', function (event) {
          if (event.defaultPrevented || !form.checkValidity()) return;
          submit.textContent = busy;
          submit.setAttribute('aria-busy', 'true');
        });

        window.addEventListener('pageshow', function () {
          submit.textContent = idle;
          submit.removeAttribute('aria-busy');
          form.removeAttribute('aria-busy');
          submit.disabled = false;
        });
      })(forms[i]);
    }
  }

  function start() {
    initNavLabel();
    initServerErrorFocus();
    initSubmitLabels();
  }

  if (doc.readyState === 'loading') doc.addEventListener('DOMContentLoaded', start);
  else start();
})();
