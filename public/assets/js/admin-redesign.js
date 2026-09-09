/*!
 * Arcates admin redesign helpers — R4 foundation.
 * Progressive enhancement only: without JavaScript the existing admin
 * routes/forms remain usable.
 */
(function () {
  'use strict';

  var doc = document;
  var body = doc.body;
  var toggle = doc.querySelector('[data-admin-nav-toggle]');
  var side = doc.querySelector('[data-admin-side]');
  var backdrop = doc.querySelector('[data-admin-nav-backdrop]');

  if (!toggle || !side) return;

  function isOpen() {
    return toggle.getAttribute('aria-expanded') === 'true';
  }

  function resetDrawerScroll() {
    side.scrollTop = 0;
    var nav = side.querySelector('.admin__nav');
    if (nav) nav.scrollTop = 0;
  }

  function setOpen(open, restoreFocus) {
    toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    side.classList.toggle('is-open', open);
    body.classList.toggle('admin-nav-open', open);

    if (open) {
      resetDrawerScroll();
      window.requestAnimationFrame(resetDrawerScroll);
    }

    if (!open && restoreFocus) toggle.focus();
  }

  toggle.addEventListener('click', function () {
    setOpen(!isOpen(), false);
  });

  if (backdrop) {
    backdrop.addEventListener('click', function () {
      setOpen(false, true);
    });
  }

  doc.addEventListener('keydown', function (event) {
    if (event.key !== 'Escape' || !isOpen()) return;
    setOpen(false, true);
  });

  side.addEventListener('click', function (event) {
    var link = event.target.closest ? event.target.closest('a[href]') : null;
    if (!link || window.innerWidth > 940) return;
    setOpen(false, false);
  });

  window.addEventListener('resize', function () {
    if (window.innerWidth > 940 && isOpen()) setOpen(false, false);
  }, { passive: true });
})();
