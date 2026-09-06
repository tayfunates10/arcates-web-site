/*
 * Arcates Web Site — tarayici animasyon denetimi
 *
 * DOCS.md 14.5'teki A testlerinin tarayicida calisan karsiligi. Sunucu
 * tarafi testler (php tests/run.php) sartnamenin kod kosullarini dogrular;
 * bu betik gercek bir tarayicida davranisi olcer.
 *
 * Gereksinim: Playwright ve bir Chromium. Depoya bagimlilik EKLEMEZ;
 * calistirmak istege baglidir ve CI'da zorunlu degildir.
 *
 * Kullanim:
 *   php -S 127.0.0.1:8321 -t public &
 *   node tools/browser/animation-check.mjs [adres]
 *
 * Cikis kodu: tum denetimler gecerse 0, aksi halde 1.
 */

import { chromium } from 'playwright';
const URL = process.argv[2] || 'http://127.0.0.1:8321/';
const launchOptions = process.env.CHROMIUM_PATH ? { executablePath: process.env.CHROMIUM_PATH } : {};
const browser = await chromium.launch(launchOptions);
let fail = 0;
const check = (ok, msg) => { console.log((ok ? 'GECTI ' : 'KALDI ') + msg); if (!ok) fail++; };

// --- A-01: JavaScript kapali -> hicbir icerik gizli degil ---
{
  const ctx = await browser.newContext({ javaScriptEnabled: false, viewport: { width: 1280, height: 900 } });
  const page = await ctx.newPage();
  await page.goto(URL, { waitUntil: 'load' });
  const hidden = await page.evaluate(() => {
    const out = [];
    // Icerik ogeleri: dekoratif sekiller (aria-hidden) tasarim geregi yari
    // saydam olabilir; gizlilik denetimi onlarin gorunur OLMASINI arar.
    document.querySelectorAll('[data-reveal], .hero__line > span, .site-nav__list, .hero__text, .hero__actions').forEach(el => {
      const cs = getComputedStyle(el);
      if (parseFloat(cs.opacity) < 0.99 || cs.visibility === 'hidden' || cs.display === 'none') {
        out.push(el.className + ' opacity=' + cs.opacity + ' display=' + cs.display);
      }
    });
    document.querySelectorAll('.shape').forEach(el => {
      const cs = getComputedStyle(el);
      if (parseFloat(cs.opacity) <= 0 || cs.display === 'none') {
        out.push('dekoratif gizli: ' + el.className);
      }
    });
    return out;
  });
  check(hidden.length === 0, 'A-01 JS kapaliyken gizli icerik yok' + (hidden.length ? ' -> ' + hidden.slice(0,3).join(' | ') : ''));
  const jsClass = await page.evaluate(() => document.documentElement.classList.contains('js'));
  check(jsClass === false, 'A-01 html.js sinifi JS kapaliyken eklenmiyor');
  await ctx.close();
}

// --- A-02: prefers-reduced-motion ---
{
  const ctx = await browser.newContext({ reducedMotion: 'reduce', viewport: { width: 1280, height: 900 } });
  const page = await ctx.newPage();
  await page.goto(URL, { waitUntil: 'networkidle' });
  await page.waitForTimeout(400);
  const state = await page.evaluate(() => {
    const els = [...document.querySelectorAll('[data-reveal]')];
    const hidden = els.filter(el => parseFloat(getComputedStyle(el).opacity) < 0.99).length;
    const hero = getComputedStyle(document.querySelector('.hero__line > span'));
    const strip = getComputedStyle(document.querySelector('.strip__track'));
    return { total: els.length, hidden, heroOpacity: hero.opacity, stripAnim: strip.animationName };
  });
  check(state.hidden === 0, `A-02 azaltilmis harekette tum ogeler gorunur (${state.total} oge, ${state.hidden} gizli)`);
  check(state.stripAnim === 'none', 'A-02 serit dongusu duruyor');
  await ctx.close();
}

// --- A-03/A-05: acilis ve kaydirma ---
{
  const ctx = await browser.newContext({ viewport: { width: 1280, height: 900 } });
  const page = await ctx.newPage();
  await page.goto(URL, { waitUntil: 'networkidle' });

  // DOCS.md 7.3 uygulama notu: 1.4 s ICERIK icindir; dekoratif kuyruk
  // 7.2'deki 950 ms giris suresiyle biraz sonra oturur.
  await page.waitForTimeout(1450);
  const heroContent = await page.evaluate(() =>
    [...document.querySelectorAll('.hero__badge, .hero__line > span, .hero__text, .hero__actions')]
      .every(el => parseFloat(getComputedStyle(el).opacity) > 0.95));
  check(heroContent, 'A-03 icerik 1.45 s icinde yerine oturdu');

  await page.waitForTimeout(600);
  const shapesSettled = await page.evaluate(() =>
    [...document.querySelectorAll('.shape')].every(el => parseFloat(getComputedStyle(el).opacity) > 0.25));
  check(shapesSettled, 'A-03 dekoratif sekiller acilis sonrasi yerine oturdu');

  const floating = await page.evaluate(() => document.querySelectorAll('.shape.is-floating').length);
  check(floating > 0, `A-03 suzulme dongusu acilis sonrasi basladi (${floating} sekil)`);

  // Sayfayi adim adim kaydir
  const height = await page.evaluate(() => document.body.scrollHeight);
  for (let y = 0; y < height; y += 500) {
    await page.evaluate(v => window.scrollTo(0, v), y);
    await page.waitForTimeout(160);
  }
  await page.waitForTimeout(900);

  const revealState = await page.evaluate(() => {
    const els = [...document.querySelectorAll('[data-reveal]')];
    return { total: els.length, visible: els.filter(el => el.classList.contains('is-visible')).length };
  });
  check(revealState.visible === revealState.total,
    `A-05 kaydirinca tum ogeler acildi (${revealState.visible}/${revealState.total})`);

  // Geri kaydir: gorunmus ogeler kapanmamali
  await page.evaluate(() => window.scrollTo(0, 0));
  await page.waitForTimeout(600);
  const afterBack = await page.evaluate(() =>
    [...document.querySelectorAll('[data-reveal]')].filter(el => el.classList.contains('is-visible')).length);
  check(afterBack === revealState.total, 'A-05 geri kaydirmada ogeler tekrar oynatilmiyor');

  // A-04: harita
  await page.evaluate(() => document.querySelector('[data-coast]').scrollIntoView({ block: 'center' }));
  await page.waitForTimeout(900);
  const coast = await page.evaluate(() => {
    const dots = [...document.querySelectorAll('.coast__dot')];
    const path = document.querySelector('.coast__path');
    return {
      total: dots.length,
      lit: dots.filter(d => d.classList.contains('is-lit')).length,
      offset: getComputedStyle(path).strokeDashoffset,
      length: path.style.getPropertyValue('--coast-length'),
    };
  });
  check(coast.lit === coast.total, `A-04 harita noktalari yaniyor (${coast.lit}/${coast.total})`);
  check(parseFloat(coast.length) > 0, `A-04 cizgi uzunlugu olculdu (${coast.length})`);

  // A-07: yeniden boyutlandirma
  await page.setViewportSize({ width: 900, height: 800 });
  await page.waitForTimeout(500);
  const relength = await page.evaluate(() => document.querySelector('.coast__path').style.getPropertyValue('--coast-length'));
  check(parseFloat(relength) > 0, `A-07 boyutlanmada uzunluk yeniden hesaplandi (${relength})`);

  // Ilerleme cubugu
  await page.setViewportSize({ width: 1280, height: 900 });
  await page.evaluate(() => window.scrollTo(0, document.body.scrollHeight));
  await page.waitForTimeout(400);
  const bar = await page.evaluate(() => getComputedStyle(document.querySelector('.progress__bar')).transform);
  check(bar.includes('matrix'), `A-P ilerleme cubugu scaleX uyguluyor (${bar})`);

  const stuck = await page.evaluate(() => document.querySelector('.site-head').classList.contains('is-stuck'));
  check(stuck, 'A-P sabit ust menu kucuk duruma gecti');

  await ctx.close();
}

// --- A-08: 360 px ---
{
  const ctx = await browser.newContext({ viewport: { width: 360, height: 780 } });
  const page = await ctx.newPage();
  await page.goto(URL, { waitUntil: 'networkidle' });
  await page.waitForTimeout(1900);
  const overflow = await page.evaluate(() => document.documentElement.scrollWidth - window.innerWidth);
  check(overflow <= 1, `A-08 360 px genisligde yatay kaydirma yok (fark=${overflow}px)`);
  const floatingMobile = await page.evaluate(() => {
    const s = document.querySelector('.shape--circle');
    return s ? getComputedStyle(s).animationName : 'yok';
  });
  check(floatingMobile === 'none', `A-08 mobilde suzulme kapali (${floatingMobile})`);
  await ctx.close();
}

// --- E-01/E-02: klavye gezinmesi ---
{
  const ctx = await browser.newContext({ viewport: { width: 1280, height: 900 } });
  const page = await ctx.newPage();
  await page.goto(URL, { waitUntil: 'networkidle' });
  await page.waitForTimeout(600);
  const focusable = await page.evaluate(() => {
    const sel = 'a[href], button, input, select, textarea, summary, [tabindex]:not([tabindex="-1"])';
    return [...document.querySelectorAll(sel)].filter(el => el.offsetParent !== null || el.classList.contains('skip-link')).length;
  });
  check(focusable > 10, `E-01 odaklanabilir oge sayisi ${focusable}`);

  await page.keyboard.press('Tab');
  const firstFocus = await page.evaluate(() => document.activeElement.className);
  check(firstFocus.includes('skip-link'), `E-01 ilk odak atlama baglantisi (${firstFocus})`);

  const outline = await page.evaluate(() => {
    const el = document.activeElement;
    const cs = getComputedStyle(el);
    return cs.outlineStyle + ' ' + cs.outlineWidth;
  });
  check(!outline.startsWith('none'), `E-02 odak halkasi gorunur (${outline})`);
  await ctx.close();
}

await browser.close();
console.log(fail === 0 ? '\nTUM TARAYICI DENETIMLERI GECTI' : `\n${fail} DENETIM KALDI`);
process.exit(fail === 0 ? 0 : 1);
