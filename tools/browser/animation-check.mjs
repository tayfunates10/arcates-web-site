/* Arcates referans ana sayfa — gercek Chromium hareket ve erisilebilirlik denetimi. */
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

// JavaScript kapali: referans ana icerigi kaybolmamalidir.
{
  const ctx = await browser.newContext({ javaScriptEnabled: false, viewport: { width: 1280, height: 900 } });
  const page = await ctx.newPage();
  await page.goto(URL, { waitUntil: 'load' });
  const state = await page.evaluate(() => {
    const selectors = ['.ref-hero__copy', '.ref-hero__visual', '.ref-metrics', '.ref-services', '.ref-process', '.ref-final-cta'];
    const hidden = selectors.flatMap(selector => [...document.querySelectorAll(selector)]).filter(el => {
      const cs = getComputedStyle(el);
      return parseFloat(cs.opacity) < 0.99 || cs.visibility === 'hidden' || cs.display === 'none';
    }).length;
    return {
      hidden,
      jsClass: document.documentElement.classList.contains('js'),
      hero: !!document.querySelector('.ref-hero__visual[aria-hidden="true"]'),
      services: document.querySelectorAll('.ref-service[href]').length,
    };
  });
  check(state.hidden === 0, `REF JS kapaliyken ana icerik gorunur (${state.hidden} gizli)`);
  check(state.jsClass === false, 'REF html.js sinifi JS kapaliyken eklenmiyor');
  check(state.hero === true, 'REF hero gorsel grubu dekoratif olarak isaretli');
  check(state.services > 0, `REF hizmet baglantilari JS olmadan mevcut (${state.services})`);
  await ctx.close();
}

// prefers-reduced-motion: referans etkileşimleri hareketi azaltir.
{
  const ctx = await browser.newContext({ reducedMotion: 'reduce', viewport: { width: 1280, height: 900 } });
  const page = await ctx.newPage();
  await page.goto(URL, { waitUntil: 'networkidle' });
  const state = await page.evaluate(() => {
    function maxTime(value) {
      return Math.max(0, ...String(value).split(',').map(part => {
        const v = part.trim();
        if (v.endsWith('ms')) return parseFloat(v) / 1000;
        if (v.endsWith('s')) return parseFloat(v);
        return 0;
      }).filter(Number.isFinite));
    }
    const samples = [...document.querySelectorAll('.ref-btn, .ref-service, .ref-project img, .ref-post img')];
    return {
      hidden: samples.filter(el => {
        const s = getComputedStyle(el);
        return s.opacity === '0' || s.visibility === 'hidden';
      }).length,
      maxDuration: Math.max(0, ...samples.map(el => {
        const s = getComputedStyle(el);
        return Math.max(maxTime(s.animationDuration), maxTime(s.transitionDuration));
      })),
    };
  });
  check(state.hidden === 0, 'REF reduced-motion icerigi gorunur');
  check(state.maxDuration <= 0.01, `REF reduced-motion hareketleri kapali (${state.maxDuration}s)`);
  await ctx.close();
}

// Masaustu: hero gorseli, temel gridler, gercek URLler ve scroll davranisi.
{
  const ctx = await browser.newContext({ viewport: { width: 1440, height: 900 } });
  const page = await ctx.newPage();
  await page.goto(URL, { waitUntil: 'networkidle' });
  await page.waitForTimeout(350);

  const state = await page.evaluate(() => {
    const image = document.querySelector('.ref-hero__visual img');
    const serviceLinks = [...document.querySelectorAll('.ref-service[href]')];
    const projects = [...document.querySelectorAll('.ref-project')];
    return {
      overflow: document.documentElement.scrollWidth - innerWidth,
      heroLoaded: !!image && image.complete && image.naturalWidth > 0 && image.naturalHeight > 0,
      services: serviceLinks.length,
      invalidServiceLinks: serviceLinks.filter(a => !a.getAttribute('href') || a.getAttribute('href') === '#').length,
      projects: projects.length,
      h1: document.querySelectorAll('h1').length,
    };
  });
  check(state.overflow <= 1, `REF 1440px yatay tasma yok (${state.overflow}px)`);
  check(state.heroLoaded, 'REF hero WebP gorseli yuklendi');
  check(state.services >= 4 && state.invalidServiceLinks === 0, `REF hizmet kartlari gercek URL tasiyor (${state.services})`);
  check(state.projects <= 3, `REF proje vitrini en fazla uc kart (${state.projects})`);
  check(state.h1 === 1, 'REF ana sayfada tek H1');

  await page.evaluate(() => window.scrollTo(0, document.body.scrollHeight));
  await page.waitForTimeout(300);
  const header = await page.evaluate(() => ({
    bar: getComputedStyle(document.querySelector('.progress__bar')).transform,
    stuck: document.querySelector('.site-head')?.classList.contains('is-stuck') || false,
  }));
  check(header.bar.includes('matrix'), `REF ilerleme cubugu scroll ile scaleX uyguluyor (${header.bar})`);
  check(header.stuck, 'REF sticky header scroll durumuna geciyor');
  await ctx.close();
}

// Mobil: referans tek kolon akisi ve belge seviyesinde yatay tasma yok.
{
  const ctx = await browser.newContext({ viewport: { width: 360, height: 780 } });
  const page = await ctx.newPage();
  await page.goto(URL, { waitUntil: 'networkidle' });
  await page.waitForTimeout(250);
  const state = await page.evaluate(() => {
    const hero = document.querySelector('.ref-hero');
    const rect = hero?.getBoundingClientRect();
    const services = document.querySelector('.ref-services__grid');
    const process = document.querySelector('.ref-process__list');
    const toggle = document.querySelector('[data-nav-toggle]');
    return {
      overflow: document.documentElement.scrollWidth - innerWidth,
      heroLeft: rect?.left ?? null,
      heroRight: rect?.right ?? null,
      serviceCols: services ? getComputedStyle(services).gridTemplateColumns : '',
      processCols: process ? getComputedStyle(process).gridTemplateColumns : '',
      toggleDisplay: toggle ? getComputedStyle(toggle).display : 'none',
      toggleWidth: toggle?.getBoundingClientRect().width ?? 0,
      toggleHeight: toggle?.getBoundingClientRect().height ?? 0,
    };
  });
  check(state.overflow <= 1, `REF 360px yatay belge kaydirmasi yok (${state.overflow}px)`);
  check(state.heroLeft !== null && state.heroLeft >= -1 && state.heroRight <= 361,
    `REF mobil hero viewport icinde (${state.heroLeft}..${state.heroRight})`);
  check(state.serviceCols.split(' ').filter(Boolean).length === 1, `REF hizmetler mobilde tek kolon (${state.serviceCols})`);
  check(state.processCols.split(' ').filter(Boolean).length === 1, `REF surec mobilde tek kolon (${state.processCols})`);
  check(state.toggleDisplay !== 'none' && state.toggleWidth >= 44 && state.toggleHeight >= 44,
    `REF mobil menu hedefi >=44px (${state.toggleWidth}x${state.toggleHeight})`);
  await ctx.close();
}

// Klavye: skip-link ve gorunur focus korunur.
{
  const ctx = await browser.newContext({ viewport: { width: 1280, height: 900 } });
  const page = await ctx.newPage();
  await page.goto(URL, { waitUntil: 'networkidle' });
  await page.keyboard.press('Tab');
  const first = await page.evaluate(() => {
    const el = document.activeElement;
    const cs = el ? getComputedStyle(el) : null;
    return {
      skip: !!el?.classList?.contains('skip-link'),
      outline: cs?.outlineStyle ?? 'none',
      width: parseFloat(cs?.outlineWidth || '0'),
    };
  });
  check(first.skip, 'REF ilk klavye odagi skip-link');
  check(first.outline !== 'none' && first.width >= 2, `REF focus halkasi gorunur (${first.outline} ${first.width}px)`);
  await ctx.close();
}

await browser.close();
console.log(fail === 0 ? '\nTUM REFERANS ANA SAYFA DENETIMLERI GECTI' : `\n${fail} REFERANS DENETIMI KALDI`);
process.exit(fail === 0 ? 0 : 1);
