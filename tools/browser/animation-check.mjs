/* Arcates R5 ana sayfa — gercek Chromium hareket ve erisilebilirlik denetimi. */
let chromium;
try {
  ({ chromium } = await import(process.env.PLAYWRIGHT_PATH || 'playwright'));
} catch (error) {
  console.error('Playwright bulunamadi. Kurun ya da PLAYWRIGHT_PATH verin.');
  console.error(String(error.message || error));
  process.exit(2);
}

const URL = process.argv[2] || 'http://127.0.0.1:8321/';
const launchOptions = process.env.CHROMIUM_PATH ? { executablePath: process.env.CHROMIUM_PATH } : {};
const browser = await chromium.launch(launchOptions);
let fail = 0;
const check = (ok, msg) => { console.log((ok ? 'GECTI ' : 'KALDI ') + msg); if (!ok) fail++; };

// A-01: JavaScript kapali -> icerik ve hero sahnesi gorunur.
{
  const ctx = await browser.newContext({ javaScriptEnabled: false, viewport: { width: 1280, height: 900 } });
  const page = await ctx.newPage();
  await page.goto(URL, { waitUntil: 'load' });
  const state = await page.evaluate(() => {
    const hidden = [];
    document.querySelectorAll('[data-reveal], .hero__line > span, .hero__text, .hero__actions, .hero-scene__web, .hero-scene__mobile, .hero-scene__mark').forEach(el => {
      const cs = getComputedStyle(el);
      if (parseFloat(cs.opacity) < 0.99 || cs.visibility === 'hidden' || cs.display === 'none') {
        hidden.push((el.className || el.tagName) + ' opacity=' + cs.opacity + ' display=' + cs.display);
      }
    });
    return {
      hidden,
      jsClass: document.documentElement.classList.contains('js'),
      scene: !!document.querySelector('.hero-scene[aria-hidden="true"]'),
      sectorLinks: document.querySelectorAll('.sector-link').length,
    };
  });
  check(state.hidden.length === 0, 'A-01 JS kapaliyken gizli icerik yok' + (state.hidden.length ? ' -> ' + state.hidden.slice(0, 3).join(' | ') : ''));
  check(state.jsClass === false, 'A-01 html.js sinifi JS kapaliyken eklenmiyor');
  check(state.scene === true, 'E-03 hero yazilim sahnesi dekoratif grup olarak tanimli');
  check(state.sectorLinks > 0, `F-R5 sektor grubu JS olmadan mevcut (${state.sectorLinks})`);
  await ctx.close();
}

// A-02: prefers-reduced-motion -> reveal ve hero girisleri son durumda.
{
  const ctx = await browser.newContext({ reducedMotion: 'reduce', viewport: { width: 1280, height: 900 } });
  const page = await ctx.newPage();
  await page.goto(URL, { waitUntil: 'networkidle' });
  await page.waitForTimeout(250);
  const state = await page.evaluate(() => {
    const reveal = [...document.querySelectorAll('[data-reveal]')];
    const hero = [...document.querySelectorAll('.hero__line > span, .hero__text, .hero__actions, .hero-scene__web, .hero-scene__mobile, .hero-scene__mark')];
    return {
      revealTotal: reveal.length,
      revealHidden: reveal.filter(el => parseFloat(getComputedStyle(el).opacity) < 0.99).length,
      heroHidden: hero.filter(el => parseFloat(getComputedStyle(el).opacity) < 0.99).length,
      heroAnimated: hero.filter(el => getComputedStyle(el).animationName !== 'none').length,
    };
  });
  check(state.revealHidden === 0, `A-02 reduced-motion reveal son durumda (${state.revealTotal} oge)`);
  check(state.heroHidden === 0, `A-02 reduced-motion hero gorunur (${state.heroHidden} gizli)`);
  check(state.heroAnimated === 0, `A-02 reduced-motion hero animasyonu kapali (${state.heroAnimated} aktif)`);
  await ctx.close();
}

// A-03/A-05/A-04/A-07: kisa hero acilisi, reveal, bolge cizgisi ve header.
{
  const ctx = await browser.newContext({ viewport: { width: 1280, height: 900 } });
  const page = await ctx.newPage();
  await page.goto(URL, { waitUntil: 'networkidle' });

  await page.waitForTimeout(1050);
  const hero = await page.evaluate(() => {
    const content = [...document.querySelectorAll('.hero__line > span, .hero__text, .hero__actions')];
    const scene = [...document.querySelectorAll('.hero-scene__web, .hero-scene__mobile, .hero-scene__mark')];
    return {
      contentOk: content.length > 0 && content.every(el => parseFloat(getComputedStyle(el).opacity) > 0.95),
      sceneOk: scene.length === 3 && scene.every(el => parseFloat(getComputedStyle(el).opacity) > 0.95),
      oldShapes: document.querySelectorAll('.shapes .shape').length,
      movingStrip: document.querySelectorAll('[data-strip], .strip__track').length,
    };
  });
  check(hero.contentOk, 'A-03 hero icerigi 1.05 s icinde yerine oturdu');
  check(hero.sceneOk, 'A-03 web + mobil yazilim sahnesi 1.05 s icinde yerine oturdu');
  check(hero.oldShapes === 0, 'A-03 eski soyut sekil kumesi DOMdan kaldirildi');
  check(hero.movingStrip === 0, 'A-10 eski kayan sektor seridi DOMda yok');

  const sector = await page.evaluate(() => {
    const all = [...document.querySelectorAll('.sector-link')];
    const anchors = all.filter(el => el.matches('a[href]'));
    return {
      total: all.length,
      linked: anchors.length,
      invalid: anchors.filter(el => !el.getAttribute('href') || el.getAttribute('href') === '#').length,
      animated: all.filter(el => getComputedStyle(el).animationName !== 'none').length,
    };
  });
  check(sector.total > 0, `A-10 sektor kartlari mevcut (${sector.total})`);
  check(sector.linked > 0 && sector.invalid === 0, `F-R5 yayindaki sektorler gercek URL tasiyor (${sector.linked} baglanti)`);
  check(sector.animated === 0, 'A-10 sektor kartlarinda sonsuz animasyon yok');

  const height = await page.evaluate(() => document.body.scrollHeight);
  for (let y = 0; y < height; y += 500) {
    await page.evaluate(v => window.scrollTo(0, v), y);
    await page.waitForTimeout(130);
  }
  await page.waitForTimeout(650);

  const revealState = await page.evaluate(() => {
    const els = [...document.querySelectorAll('[data-reveal]')];
    return { total: els.length, visible: els.filter(el => el.classList.contains('is-visible')).length };
  });
  check(revealState.visible === revealState.total,
    `A-05 kaydirinca tum reveal ogeleri acildi (${revealState.visible}/${revealState.total})`);

  await page.evaluate(() => window.scrollTo(0, 0));
  await page.waitForTimeout(350);
  const afterBack = await page.evaluate(() =>
    [...document.querySelectorAll('[data-reveal]')].filter(el => el.classList.contains('is-visible')).length);
  check(afterBack === revealState.total, 'A-05 geri kaydirmada ogeler tekrar kapanmiyor');

  await page.evaluate(() => document.querySelector('[data-coast]')?.scrollIntoView({ block: 'center' }));
  await page.waitForTimeout(800);
  const coast = await page.evaluate(() => {
    const dots = [...document.querySelectorAll('.coast__dot')];
    const path = document.querySelector('.coast__path');
    return {
      total: dots.length,
      lit: dots.filter(d => d.classList.contains('is-lit')).length,
      length: path ? path.style.getPropertyValue('--coast-length') : '',
      note: document.querySelector('.coast__note')?.textContent || '',
    };
  });
  check(coast.total > 0 && coast.lit === coast.total, `A-04 bolge noktalari yaniyor (${coast.lit}/${coast.total})`);
  check(parseFloat(coast.length) > 0, `A-04 bolge cizgisi olculdu (${coast.length})`);
  check(coast.note.length > 10, 'E-07 bolge grafiginin temsili oldugu metinle aciklaniyor');

  await page.setViewportSize({ width: 900, height: 800 });
  await page.waitForTimeout(400);
  const relength = await page.evaluate(() => document.querySelector('.coast__path')?.style.getPropertyValue('--coast-length') || '');
  check(parseFloat(relength) > 0, `A-07 boyutlanmada bolge cizgisi yeniden olculdu (${relength})`);

  await page.setViewportSize({ width: 1280, height: 900 });
  await page.evaluate(() => window.scrollTo(0, document.body.scrollHeight));
  await page.waitForTimeout(300);
  const header = await page.evaluate(() => ({
    bar: getComputedStyle(document.querySelector('.progress__bar')).transform,
    stuck: document.querySelector('.site-head')?.classList.contains('is-stuck') || false,
  }));
  check(header.bar.includes('matrix'), `A-P ilerleme cubugu scaleX uyguluyor (${header.bar})`);
  check(header.stuck, 'A-P sabit ust menu kucuk duruma gecti');

  await ctx.close();
}

// A-08: 360 px responsive ve belge seviyesinde yatay tasma yok.
{
  const ctx = await browser.newContext({ viewport: { width: 360, height: 780 } });
  const page = await ctx.newPage();
  await page.goto(URL, { waitUntil: 'networkidle' });
  await page.waitForTimeout(1050);
  const state = await page.evaluate(() => {
    const scene = document.querySelector('.hero-scene');
    const rect = scene?.getBoundingClientRect();
    return {
      overflow: document.documentElement.scrollWidth - window.innerWidth,
      sceneLeft: rect?.left ?? null,
      sceneRight: rect?.right ?? null,
      sectorCols: getComputedStyle(document.querySelector('.sector-links__grid')).gridTemplateColumns,
      serviceCols: getComputedStyle(document.querySelector('.cards--services')).gridTemplateColumns,
    };
  });
  check(state.overflow <= 1, `A-08 360 px genislikte yatay belge kaydirmasi yok (fark=${state.overflow}px)`);
  check(state.sceneLeft !== null && state.sceneLeft >= -1 && state.sceneRight <= 361,
    `A-08 hero sahnesi viewport icinde (${state.sceneLeft}..${state.sceneRight})`);
  check(state.serviceCols.split(' ').length === 1, `A-08 hizmetler mobilde tek kolon (${state.serviceCols})`);
  await ctx.close();
}

// E-01/E-02: klavye gezinmesi ve gorunur focus.
{
  const ctx = await browser.newContext({ viewport: { width: 1280, height: 900 } });
  const page = await ctx.newPage();
  await page.goto(URL, { waitUntil: 'networkidle' });
  await page.waitForTimeout(350);
  const focusable = await page.evaluate(() => {
    const sel = 'a[href], button, input, select, textarea, summary, [tabindex]:not([tabindex="-1"])';
    return [...document.querySelectorAll(sel)].filter(el => el.offsetParent !== null || el.classList.contains('skip-link')).length;
  });
  check(focusable > 10, `E-01 odaklanabilir oge sayisi ${focusable}`);

  await page.keyboard.press('Tab');
  const firstFocus = await page.evaluate(() => document.activeElement.className);
  check(firstFocus.includes('skip-link'), `E-01 ilk odak atlama baglantisi (${firstFocus})`);

  const outline = await page.evaluate(() => {
    const cs = getComputedStyle(document.activeElement);
    return cs.outlineStyle + ' ' + cs.outlineWidth;
  });
  check(!outline.startsWith('none'), `E-02 odak halkasi gorunur (${outline})`);
  await ctx.close();
}

await browser.close();
console.log(fail === 0 ? '\nTUM R5 TARAYICI DENETIMLERI GECTI' : `\n${fail} R5 DENETIMI KALDI`);
process.exit(fail === 0 ? 0 : 1);
