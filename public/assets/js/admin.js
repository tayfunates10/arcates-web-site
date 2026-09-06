/*!
 * Arcates Web Site — admin.js
 *
 * Panelde satir ici script bulunmaz; tum davranis bu dosyadadir.
 * DOCS.md 10.7
 *
 * Ilerlemeli iyilestirme: JavaScript calismazsa panel yine kullanilabilir;
 * buradaki her sey yalnizca kolaylik saglar.  DOCS.md 1 (ilke 2)
 */
(function () {
  'use strict';

  var doc = document;

  /* ---------------------------------------------------------------------
   * Yikici islemler icin onay
   * `data-confirm` tasiyan formlar gonderilmeden once sorar.
   * ------------------------------------------------------------------- */
  doc.addEventListener('submit', function (event) {
    var form = event.target;
    if (!(form instanceof HTMLFormElement)) return;

    var question = form.getAttribute('data-confirm');
    if (!question) return;

    if (!window.confirm(question)) {
      event.preventDefault();
    }
  });

  /* ---------------------------------------------------------------------
   * Tekrarlanan form satirlari (calisma saatleri, sosyal hesaplar, kartlar)
   * ------------------------------------------------------------------- */
  doc.addEventListener('click', function (event) {
    var target = event.target;
    if (!(target instanceof Element)) return;

    var addKey = target.closest('[data-repeat-add]');
    if (addKey) {
      var key = addKey.getAttribute('data-repeat-add');
      var container = doc.querySelector('[data-repeat="' + key + '"]');
      if (!container) return;

      var rows = container.querySelectorAll('.repeat__row');
      if (!rows.length) return;

      var clone = rows[rows.length - 1].cloneNode(true);
      var nextIndex = rows.length;

      clone.querySelectorAll('input, select, textarea').forEach(function (input) {
        if (input.type === 'checkbox' || input.type === 'radio') {
          input.checked = false;
        } else {
          input.value = '';
        }

        // Dizi adlarindaki ilk sayisal dizin yenilenir; aksi halde yeni satir
        // son satirin degerlerinin ustune yazar.
        var name = input.getAttribute('name');
        if (name && /\[\d+\]/.test(name)) {
          input.setAttribute('name', name.replace(/\[\d+\]/, '[' + nextIndex + ']'));
        }

        // Etiket baglantisi korunsun diye benzersiz kimlik uretilir.
        if (input.id) {
          var fresh = input.id.replace(/_\d+$/, '') + '_' + nextIndex + '_' + Date.now();
          var label = clone.querySelector('label[for="' + input.id + '"]');
          input.id = fresh;
          if (label) label.setAttribute('for', fresh);
        }
      });
      container.appendChild(clone);
      var first = clone.querySelector('input, select, textarea');
      if (first) first.focus();
      return;
    }

    var removeButton = target.closest('[data-repeat-remove]');
    if (removeButton) {
      var row = removeButton.closest('.repeat__row');
      if (!row) return;
      var parent = row.parentElement;
      if (parent && parent.querySelectorAll('.repeat__row').length > 1) {
        row.remove();
      } else {
        row.querySelectorAll('input, select, textarea').forEach(function (input) {
          input.value = '';
        });
      }
    }
  });

  /* ---------------------------------------------------------------------
   * Baslik alanindan slug onerisi
   * Kullanici slug alanina elle dokunduysa bir daha ustune yazilmaz.
   * DOCS.md 11.4
   * ------------------------------------------------------------------- */
  var TR_MAP = {
    'ç': 'c', 'Ç': 'c', 'ğ': 'g', 'Ğ': 'g', 'ı': 'i', 'I': 'i', 'İ': 'i',
    'ö': 'o', 'Ö': 'o', 'ş': 's', 'Ş': 's', 'ü': 'u', 'Ü': 'u'
  };

  function slugify(text) {
    var out = '';
    for (var i = 0; i < text.length; i++) {
      var ch = text[i];
      out += Object.prototype.hasOwnProperty.call(TR_MAP, ch) ? TR_MAP[ch] : ch;
    }
    return out
      .toLowerCase()
      .normalize('NFD')
      .replace(/[\u0300-\u036f]/g, '')
      .replace(/[^a-z0-9]+/g, '-')
      .replace(/^-+|-+$/g, '')
      .replace(/-{2,}/g, '-');
  }

  doc.querySelectorAll('[data-slug-source]').forEach(function (source) {
    var targetId = source.getAttribute('data-slug-source');
    var target = doc.getElementById(targetId);
    if (!target) return;

    var touched = target.value.trim() !== '';
    target.addEventListener('input', function () { touched = true; });

    source.addEventListener('input', function () {
      if (touched) return;
      target.value = slugify(source.value);
      target.dispatchEvent(new Event('change', { bubbles: true }));
    });
  });

  /* ---------------------------------------------------------------------
   * Karakter sayaci — meta baslik ve aciklama sinirlari
   * DOCS.md 9.6, 11.1
   * ------------------------------------------------------------------- */
  doc.querySelectorAll('[data-counter]').forEach(function (input) {
    var limit = parseInt(input.getAttribute('data-counter'), 10);
    if (!limit) return;

    var output = doc.createElement('span');
    output.className = 'field__hint field__counter';
    output.setAttribute('aria-live', 'polite');
    input.parentNode.appendChild(output);

    function render() {
      var length = input.value.length;
      output.textContent = length + ' / ' + limit + ' karakter';
      output.classList.toggle('is-over', length > limit);
    }

    input.addEventListener('input', render);
    render();
  });

  /* ---------------------------------------------------------------------
   * Sekmeler (dil sekmeleri, form bolumleri)
   * ------------------------------------------------------------------- */
  doc.querySelectorAll('[data-tabs]').forEach(function (group) {
    var buttons = group.querySelectorAll('[data-tab]');
    if (!buttons.length) return;

    function activate(name) {
      buttons.forEach(function (button) {
        var isCurrent = button.getAttribute('data-tab') === name;
        button.classList.toggle('is-current', isCurrent);
        button.setAttribute('aria-selected', isCurrent ? 'true' : 'false');
        button.setAttribute('tabindex', isCurrent ? '0' : '-1');
      });

      // Panel, sekme grubunu `data-tab-group` ile isaretler.
      var groupName = group.getAttribute('data-tabs');
      doc.querySelectorAll('[data-tab-panel][data-tab-group="' + groupName + '"]').forEach(function (panel) {
        panel.hidden = panel.getAttribute('data-tab-panel') !== name;
      });
    }

    buttons.forEach(function (button) {
      button.addEventListener('click', function () {
        activate(button.getAttribute('data-tab'));
      });
    });

    activate(buttons[0].getAttribute('data-tab'));
  });

  /* ---------------------------------------------------------------------
   * Kaydedilmemis degisiklik uyarisi
   * ------------------------------------------------------------------- */
  doc.querySelectorAll('form[data-dirty-guard]').forEach(function (form) {
    var dirty = false;

    form.addEventListener('input', function () { dirty = true; });
    form.addEventListener('submit', function () { dirty = false; });

    window.addEventListener('beforeunload', function (event) {
      if (!dirty) return;
      event.preventDefault();
      event.returnValue = '';
    });
  });
})();
