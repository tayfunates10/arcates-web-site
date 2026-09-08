/* Arcates R6 inner-page acceptance checks — real Chromium. */
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

const representativeRoutes = [
  '/web-tasarim',
  '/edremit-web-tasarim',
  '/otel-pansiyon-web-sitesi',
  '/blog',
  '/referanslar',
  '/hakkimizda',
  '/iletisim',
  '/referanslar/akcay-pansiyon-rezervasyon-sitesi',
  '/blog/yerel-aramada-gorunurluk-isletme-profili',
];

// R6-01/R6-02: all major inner-page families return 200, one H1 and the shared dark hero.
{
  const ctx = await browser.newContext({ viewport: { width: 1280, height: 900 } });
  const page = await ctx.newPage();
  for (const route of representativeRoutes) {
    const response = await page.goto(BASE + route, { waitUntil: 'networkidle' });
    const state = await page.evaluate(() => {
      const hero = document.querySelector('.page-hero');
      const title = hero?.querySelector('.page__title');
      const heroStyle = hero ? getComputedStyle(hero) : null;
      const titleStyle = title ? getComputedStyle(title) : null;
      return {
        h1: document.querySelectorAll('h1').length,
        hero: !!hero,
        background: heroStyle?.backgroundImage || '',
        titleColor: titleStyle?.color || '',
        overflow: document.documentElement.scrollWidth - innerWidth,
      };
    });
    check(response?.status() === 200, `R6 ${route} HTTP 200`);
    check(state.h1 === 1, `R6 ${route} tek H1 (${state.h1})`);
    check(state.hero === true && state.background !== 'none', `R6 ${route} ortak hero yuzeyi`);
    check(state.titleColor === 'rgb(245, 248, 255)', `R6 ${route} hero basligi yuksek kontrast (${state.titleColor})`);
    check(state.overflow <= 1, `R6 ${route} masaustunde yatay tasma yok (${state.overflow}px)`);
  }
  await ctx.close();
}

// R6-03: editorial service/location/sector/article pages use raised content surfaces.
{
  const ctx = await browser.newContext({ viewport: { width: 1280, height: 900 } });
  const page = await ctx.newPage();
  for (const route of [
    '/web-tasarim',
    '/edremit-web-tasarim',
    '/otel-pansiyon-web-sitesi',
    '/blog/yerel-aramada-gorunurluk-isletme-profili',
  ]) {
    await page.goto(BASE + route, { waitUntil: 'networkidle' });
    const state = await page.evaluate(() => {
      const surface = document.querySelector('.content-main, .article-main');
      const style = surface ? getComputedStyle(surface) : null;
      return {
        exists: !!surface,
        radius: style ? parseFloat(style.borderRadius) : 0,
        border: style?.borderTopStyle || '',
        background: style?.backgroundColor || '',
      };
    });
    check(state.exists, `R6 ${route} editorial icerik yuzeyi mevcut`);
    check(state.radius >= 14, `R6 ${route} icerik yuzeyi radius (${state.radius}px)`);
    check(state.border !== 'none', `R6 ${route} icerik yuzeyi siniri gorunur`);
  }
  await ctx.close();
}

// R6-04: list cards and project detail preserve their semantic structures.
{
  const ctx = await browser.newContext({ viewport: { width: 1280, height: 900 } });
  const page = await ctx.newPage();

  await page.goto(BASE + '/referanslar', { waitUntil: 'networkidle' });
  const projects = await page.evaluate(() => ({
    cards: document.querySelectorAll('.project-card').length,
    rounded: parseFloat(getComputedStyle(document.querySelector('.project-card')).borderRadius),
  }));
  check(projects.cards >= 4, `R6 proje listesinde kartlar mevcut (${projects.cards})`);
  check(projects.rounded >= 14, `R6 proje karti yeni yuzey ailesinde (${projects.rounded}px)`);

  await page.goto(BASE + '/blog', { waitUntil: 'networkidle' });
  const posts = await page.evaluate(() => ({
    cards: document.querySelectorAll('.post-card').length,
    rounded: parseFloat(getComputedStyle(document.querySelector('.post-card')).borderRadius),
  }));
  check(posts.cards >= 1, `R6 blog kartlari mevcut (${posts.cards})`);
  check(posts.rounded >= 14, `R6 blog karti yeni yuzey ailesinde (${posts.rounded}px)`);

  await page.goto(BASE + '/referanslar/akcay-pansiyon-rezervasyon-sitesi', { waitUntil: 'networkidle' });
  const detail = await page.evaluate(() => ({
    meta: document.querySelectorAll('.project-meta > div').length,
    detail: !!document.querySelector('.project-detail'),
    galleryOverflow: document.documentElement.scrollWidth - innerWidth,
  }));
  check(detail.meta >= 2, `R6 proje detay meta yuzeyi dolu (${detail.meta})`);
  check(detail.detail === true, 'R6 proje detay hikaye yuzeyi mevcut');
  check(detail.galleryOverflow <= 1, `R6 proje detay yatay tasma yok (${detail.galleryOverflow}px)`);

  await ctx.close();
}

// R6-05: contact keeps the quote form above copy and visible in the first viewport.
for (const viewport of [{ width: 1280, height: 900 }, { width: 390, height: 844 }]) {
  const ctx = await browser.newContext({ viewport });
  const page = await ctx.newPage();
  await page.goto(BASE + '/iletisim', { waitUntil: 'networkidle' });
  const state = await page.evaluate(() => {
    const form = document.querySelector('#teklif-formu form');
    const card = document.querySelector('.contact-card');
    const copy = document.querySelector('.contact-copy');
    return {
      formTop: form?.getBoundingClientRect().top ?? null,
      cardTop: card?.getBoundingClientRect().top ?? null,
      copyTop: copy?.getBoundingClientRect().top ?? null,
      viewport: innerHeight,
      overflow: document.documentElement.scrollWidth - innerWidth,
    };
  });
  check(state.formTop !== null && state.formTop < state.viewport,
    `R6 iletisim ${viewport.width}px form ilk ekranda (top=${state.formTop}, h=${state.viewport})`);
  check(state.cardTop !== null && state.copyTop !== null && (viewport.width >= 940 || state.cardTop < state.copyTop),
    `R6 iletisim ${viewport.width}px mobil DOM/gorsel onceligi korunuyor`);
  check(state.overflow <= 1, `R6 iletisim ${viewport.width}px yatay tasma yok (${state.overflow}px)`);
  await ctx.close();
}

// R6-06: representative pages remain safe at narrow mobile width.
{
  const ctx = await browser.newContext({ viewport: { width: 360, height: 780 } });
  const page = await ctx.newPage();
  for (const route of ['/web-tasarim', '/edremit-web-tasarim', '/blog', '/referanslar', '/hakkimizda']) {
    await page.goto(BASE + route, { waitUntil: 'networkidle' });
    const state = await page.evaluate(() => {
      const title = document.querySelector('.page-hero .page__title');
      const rect = title?.getBoundingClientRect();
      return {
        overflow: document.documentElement.scrollWidth - innerWidth,
        left: rect?.left ?? null,
        right: rect?.right ?? null,
      };
    });
    check(state.overflow <= 1, `R6 mobil ${route} yatay tasma yok (${state.overflow}px)`);
    check(state.left !== null && state.left >= -1 && state.right <= 361,
      `R6 mobil ${route} H1 viewport icinde (${state.left}..${state.right})`);
  }
  await ctx.close();
}

await browser.close();
console.log(fail === 0 ? '\nTUM R6 IC SAYFA DENETIMLERI GECTI' : `\n${fail} R6 DENETIMI KALDI`);
process.exit(fail === 0 ? 0 : 1);
