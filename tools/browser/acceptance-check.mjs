/* Arcates R8 final redesign acceptance — real Chromium. */
let chromium;
try {
  ({ chromium } = await import(process.env.PLAYWRIGHT_PATH || 'playwright'));
} catch (error) {
  console.error('Playwright bulunamadi.');
  console.error(String(error.message || error));
  process.exit(2);
}

const BASE = (process.argv[2] || 'http://127.0.0.1:8321').replace(/\/$/, '');
const EMAIL = process.env.ARC_R7_ADMIN_EMAIL || 'r7-browser@arcates.local';
const PASSWORD = process.env.ARC_R7_ADMIN_PASSWORD || 'ArcatesBrowser!2026';
const launchOptions = process.env.CHROMIUM_PATH ? { executablePath: process.env.CHROMIUM_PATH } : {};
const browser = await chromium.launch(launchOptions);
let fail = 0;
const check = (ok, msg) => { console.log((ok ? 'GECTI ' : 'KALDI ') + msg); if (!ok) fail++; };

const matrixRoutes = [
  '/',
  '/web-tasarim',
  '/edremit-web-tasarim',
  '/blog',
  '/referanslar',
  '/iletisim',
];
const viewports = [
  { width: 320, height: 800 },
  { width: 360, height: 800 },
  { width: 390, height: 844 },
  { width: 768, height: 1024 },
  { width: 1024, height: 900 },
  { width: 1366, height: 900 },
  { width: 1440, height: 900 },
  { width: 1920, height: 1080 },
];

async function layoutState(page) {
  return page.evaluate(() => {
    const main = document.querySelector('main');
    const h1 = document.querySelector('h1');
    const mainRect = main?.getBoundingClientRect() ?? null;
    const h1Rect = h1?.getBoundingClientRect() ?? null;
    return {
      h1Count: document.querySelectorAll('h1').length,
      overflow: document.documentElement.scrollWidth - innerWidth,
      mainLeft: mainRect?.left ?? null,
      mainRight: mainRect?.right ?? null,
      h1Left: h1Rect?.left ?? null,
      h1Right: h1Rect?.right ?? null,
    };
  });
}

// R8-01: 320–1920 CSS-pixel responsive matrix on representative public families.
for (const viewport of viewports) {
  const ctx = await browser.newContext({ viewport });
  const page = await ctx.newPage();
  let viewportFailures = 0;
  for (const route of matrixRoutes) {
    const response = await page.goto(BASE + route, { waitUntil: 'networkidle' });
    const state = await layoutState(page);
    const ok = response?.status() === 200
      && state.h1Count === 1
      && state.overflow <= 1
      && state.mainLeft !== null && state.mainLeft >= -1
      && state.mainRight !== null && state.mainRight <= viewport.width + 1
      && state.h1Left !== null && state.h1Left >= -1
      && state.h1Right !== null && state.h1Right <= viewport.width + 1;
    if (!ok) {
      viewportFailures++;
      console.log(`  KALDI ${viewport.width}px ${route}: status=${response?.status()} h1=${state.h1Count} overflow=${state.overflow} main=${state.mainLeft}..${state.mainRight} h1=${state.h1Left}..${state.h1Right}`);
    }
  }
  check(viewportFailures === 0, `R8 ${viewport.width}px matrisi ${matrixRoutes.length}/${matrixRoutes.length} rota tasmasiz ve tek H1`);

  if (viewport.width <= 390) {
    await page.goto(BASE + '/', { waitUntil: 'networkidle' });
    const nav = await page.evaluate(() => {
      const toggle = document.querySelector('[data-nav-toggle]');
      if (!toggle) return null;
      const rect = toggle.getBoundingClientRect();
      return { display: getComputedStyle(toggle).display, width: rect.width, height: rect.height };
    });
    check(nav !== null && nav.display !== 'none' && nav.width >= 44 && nav.height >= 44,
      `R8 ${viewport.width}px mobil menu hedefi en az 44x44 (${nav?.width ?? 0}x${nav?.height ?? 0})`);
  }
  await ctx.close();
}

// R8-02: edge families at the smallest and widest acceptance widths.
for (const viewport of [{ width: 320, height: 800 }, { width: 1920, height: 1080 }]) {
  const ctx = await browser.newContext({ viewport });
  const page = await ctx.newPage();
  for (const route of ['/sss', '/tesekkurler']) {
    const response = await page.goto(BASE + route, { waitUntil: 'networkidle' });
    const state = await layoutState(page);
    check(response?.status() === 200 && state.h1Count === 1 && state.overflow <= 1,
      `R8 ${viewport.width}px ${route} HTTP 200, tek H1 ve yatay tasmasiz (${state.overflow}px)`);
  }
  await ctx.close();
}

// R8-03: effective 200% zoom/reflow approximation.
// 1280 physical px / deviceScaleFactor 2 => 640 CSS px layout viewport.
{
  const zoomCtx = await browser.newContext({ viewport: { width: 640, height: 900 }, deviceScaleFactor: 2 });
  const page = await zoomCtx.newPage();
  for (const route of ['/', '/web-tasarim', '/iletisim', '/blog']) {
    const response = await page.goto(BASE + route, { waitUntil: 'networkidle' });
    const state = await layoutState(page);
    check(response?.status() === 200 && state.h1Count === 1 && state.overflow <= 1,
      `R8 %200 etkili reflow 1280 fiziksel/640 CSS ${route} tasmasiz (${state.overflow}px)`);
  }

  await page.goto(BASE + '/panel/giris', { waitUntil: 'networkidle' });
  await page.locator('#email').fill(EMAIL);
  await page.locator('#password').fill(PASSWORD);
  await Promise.all([
    page.waitForURL(url => url.pathname === '/panel' || url.pathname === '/panel/'),
    page.locator('form').filter({ has: page.locator('#email') }).locator('button[type="submit"]').click(),
  ]);
  await page.goto(BASE + '/panel/sayfalar', { waitUntil: 'networkidle' });
  const adminZoom = await page.evaluate(() => ({
    overflow: document.documentElement.scrollWidth - innerWidth,
    content: document.querySelector('#panel-icerik')?.getBoundingClientRect() ?? null,
    toggle: getComputedStyle(document.querySelector('[data-admin-nav-toggle]')).display,
  }));
  check(adminZoom.overflow <= 1 && adminZoom.content && adminZoom.content.left >= -1 && adminZoom.content.right <= 641,
    `R8 %200 etkili reflow panel sayfalari belgeyi buyutmuyor (${adminZoom.overflow}px)`);
  check(adminZoom.toggle !== 'none', 'R8 %200 etkili reflow panel mobil navigasyonu erisilebilir');
  await zoomCtx.close();
}

// R8-04: keyboard-only entry path, focus visibility and public mobile navigation.
{
  const ctx = await browser.newContext({ viewport: { width: 1280, height: 900 } });
  const page = await ctx.newPage();
  await page.goto(BASE + '/', { waitUntil: 'networkidle' });
  await page.keyboard.press('Tab');
  const firstFocus = await page.evaluate(() => {
    const el = document.activeElement;
    const style = el ? getComputedStyle(el) : null;
    return {
      skip: !!el?.classList?.contains('skip-link'),
      outlineStyle: style?.outlineStyle ?? 'none',
      outlineWidth: parseFloat(style?.outlineWidth || '0'),
    };
  });
  check(firstFocus.skip, 'R8 klavye ilk odagi skip-link');
  check(firstFocus.outlineStyle !== 'none' && firstFocus.outlineWidth >= 2,
    `R8 klavye odak halkasi gorunur (${firstFocus.outlineStyle} ${firstFocus.outlineWidth}px)`);
  await page.keyboard.press('Enter');
  await page.waitForTimeout(50);
  check(new URL(page.url()).hash === '#icerik', 'R8 skip-link Enter ile #icerik hedefine gider');
  await ctx.close();

  const mobile = await browser.newContext({ viewport: { width: 390, height: 844 } });
  const m = await mobile.newPage();
  await m.goto(BASE + '/', { waitUntil: 'networkidle' });
  const toggle = m.locator('[data-nav-toggle]');
  await toggle.focus();
  await m.keyboard.press('Enter');
  check(await toggle.getAttribute('aria-expanded') === 'true', 'R8 klavye Enter mobil site menusunu acar');
  await m.keyboard.press('Escape');
  check(await toggle.getAttribute('aria-expanded') === 'false', 'R8 klavye Escape mobil site menusunu kapatir');
  check(await toggle.evaluate(el => el === document.activeElement), 'R8 mobil menu kapaninca odak dugmeye doner');
  await mobile.close();
}

// R8-05: actual Arabic route is RTL at narrow and desktop widths.
for (const viewport of [{ width: 320, height: 800 }, { width: 1366, height: 900 }]) {
  const ctx = await browser.newContext({ viewport });
  const page = await ctx.newPage();
  const response = await page.goto(BASE + '/ar/', { waitUntil: 'networkidle' });
  const rtl = await page.evaluate(() => ({
    lang: document.documentElement.lang,
    dir: document.documentElement.dir,
    computed: getComputedStyle(document.documentElement).direction,
    overflow: document.documentElement.scrollWidth - innerWidth,
    h1: document.querySelectorAll('h1').length,
  }));
  check(response?.status() === 200, `R8 RTL ${viewport.width}px /ar/ HTTP 200`);
  check(rtl.lang === 'ar' && rtl.dir === 'rtl' && rtl.computed === 'rtl',
    `R8 RTL ${viewport.width}px html lang=ar dir=rtl`);
  check(rtl.overflow <= 1 && rtl.h1 === 1,
    `R8 RTL ${viewport.width}px tasmasiz ve tek H1 (${rtl.overflow}px)`);
  await ctx.close();
}

// R8-06: reduced-motion keeps content visible and removes representative motion.
{
  const ctx = await browser.newContext({ viewport: { width: 1280, height: 900 }, reducedMotion: 'reduce' });
  const page = await ctx.newPage();
  await page.goto(BASE + '/', { waitUntil: 'networkidle' });
  const publicMotion = await page.evaluate(() => {
    function maxTime(value) {
      return Math.max(0, ...String(value).split(',').map(part => {
        const v = part.trim();
        if (v.endsWith('ms')) return parseFloat(v) / 1000;
        if (v.endsWith('s')) return parseFloat(v);
        return 0;
      }).filter(Number.isFinite));
    }
    const reveals = [...document.querySelectorAll('[data-reveal]')];
    const hidden = reveals.filter(el => {
      const s = getComputedStyle(el);
      return s.opacity === '0' || s.visibility === 'hidden';
    }).length;
    const samples = [...document.querySelectorAll('[data-reveal], .hero__scene, .btn')].slice(0, 24);
    const maxDuration = Math.max(0, ...samples.map(el => {
      const s = getComputedStyle(el);
      return Math.max(maxTime(s.animationDuration), maxTime(s.transitionDuration));
    }));
    return { reveals: reveals.length, hidden, maxDuration };
  });
  check(publicMotion.hidden === 0, `R8 reduced-motion tum reveal icerigi gorunur (${publicMotion.reveals - publicMotion.hidden}/${publicMotion.reveals})`);
  check(publicMotion.maxDuration <= 0.01, `R8 reduced-motion on yuz temsilci hareketleri kapali (${publicMotion.maxDuration}s)`);

  await page.goto(BASE + '/panel/giris', { waitUntil: 'networkidle' });
  const adminMotion = await page.evaluate(() => {
    function maxTime(value) {
      return Math.max(0, ...String(value).split(',').map(part => {
        const v = part.trim();
        if (v.endsWith('ms')) return parseFloat(v) / 1000;
        if (v.endsWith('s')) return parseFloat(v);
        return 0;
      }).filter(Number.isFinite));
    }
    const samples = [...document.querySelectorAll('.system__card, .btn, input')];
    return Math.max(0, ...samples.map(el => {
      const s = getComputedStyle(el);
      return Math.max(maxTime(s.animationDuration), maxTime(s.transitionDuration));
    }));
  });
  check(adminMotion <= 0.01, `R8 reduced-motion panel/sistem temsilci hareketleri kapali (${adminMotion}s)`);
  await ctx.close();
}

await browser.close();
console.log(fail === 0 ? '\nTUM R8 NIHAI KABUL DENETIMLERI GECTI' : `\n${fail} R8 KABUL DENETIMI KALDI`);
process.exit(fail === 0 ? 0 : 1);
