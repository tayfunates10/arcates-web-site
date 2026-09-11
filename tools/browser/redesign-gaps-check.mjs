/* Arcates redesign acceptance gaps — service visuals and persistent form errors. */
let chromium;
try {
  ({ chromium } = await import(process.env.PLAYWRIGHT_PATH || 'playwright'));
} catch (error) {
  console.error('Playwright bulunamadi.');
  console.error(String(error.message || error));
  process.exit(2);
}

const BASE = (process.argv[2] || 'http://127.0.0.1:8321').replace(/\/$/, '');
const launchOptions = process.env.CHROMIUM_PATH ? { executablePath: process.env.CHROMIUM_PATH } : {};
const browser = await chromium.launch(launchOptions);
let fail = 0;
const check = (ok, msg) => { console.log((ok ? 'GECTI ' : 'KALDI ') + msg); if (!ok) fail++; };

const services = {
  'web-tasarim': 'layout',
  'e-ticaret-sitesi': 'cart',
  'rezervasyon-sistemi': 'calendar',
  'seo-hizmeti': 'search',
  'coklu-dil-web-sitesi': 'globe',
  'web-sitesi-bakim': 'shield',
};

for (const width of [320, 390, 768, 1440]) {
  const ctx = await browser.newContext({ viewport: { width, height: 900 }, reducedMotion: 'reduce' });
  const page = await ctx.newPage();

  for (const [slug, key] of Object.entries(services)) {
    await page.goto(BASE + '/' + slug, { waitUntil: 'networkidle' });
    const state = await page.evaluate(() => {
      const image = document.querySelector('.service-hero__media img');
      const rect = image?.getBoundingClientRect();
      const copy = document.querySelector('.service-hero__copy')?.getBoundingClientRect();
      return {
        loaded: !!image && image.complete && image.naturalWidth === 480,
        src: image?.getAttribute('src') || '',
        decorative: image?.getAttribute('alt') === '',
        inside: !!rect && rect.left >= -1 && rect.right <= innerWidth + 1,
        belowCopy: !!rect && !!copy && rect.top >= copy.bottom - 1,
        overflow: document.documentElement.scrollWidth - innerWidth,
      };
    });

    check(state.loaded && state.src.includes('service-' + key + '.svg') && state.decorative,
      `LIVE-05 ${slug} ${width}px dogru hizmet gorseli yuklendi`);
    check(state.inside && state.overflow <= 1 && (width > 940 || state.belowCopy),
      `LIVE-05 ${slug} ${width}px gorsel tasmaz ve mobilde metinden sonra gelir`);
  }

  await ctx.close();
}

for (const viewport of [{ width: 1280, height: 900 }, { width: 390, height: 844 }]) {
  const ctx = await browser.newContext({ viewport });
  const page = await ctx.newPage();
  await page.goto(BASE + '/iletisim', { waitUntil: 'networkidle' });

  await page.locator('#teklif-formu form').evaluate(form => {
    form.dispatchEvent(new Event('submit', { bubbles: true, cancelable: true }));
  });
  await page.waitForTimeout(50);

  const explained = await page.locator('#teklif-formu [aria-invalid="true"]').evaluateAll(fields =>
    fields.length === 5 && fields.every(field =>
      (field.getAttribute('aria-describedby') || '').split(/\s+/).some(id => {
        const error = document.getElementById(id);
        return error && !error.hidden && error.textContent.trim().length > 0;
      })));
  check(explained, `LIVE-06 ${viewport.width}px tum hatali alanlar kalici aciklamayla iliskili`);

  await page.locator('#form_name').fill('Arayuz kontrolu');
  const cleared = await page.locator('#form_name').evaluate(field => {
    const error = document.getElementById('form_name_client_error');
    return field.getAttribute('aria-invalid') === 'false' && !!error && error.hidden;
  });
  check(cleared, `LIVE-06 ${viewport.width}px duzeltilen alanin istemci hatasi temizlenir`);

  await page.locator('#form_name').fill('');
  const restored = await page.locator('#form_name_client_error').evaluateAll(nodes =>
    nodes.length === 1 && !nodes[0].hidden && nodes[0].textContent.trim().length > 0);
  check(restored, `LIVE-06 ${viewport.width}px tekrar hatada aciklama cogalmaz`);

  await ctx.close();
}

await browser.close();
console.log(fail === 0 ? '\nREDESIGN KABUL BOSLUKLARI GECTI' : `\n${fail} REDESIGN KABUL KONTROLU KALDI`);
process.exit(fail === 0 ? 0 : 1);
