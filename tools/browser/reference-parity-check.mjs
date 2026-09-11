/* Arcates reference parity checks — geometry and responsive composition only. */
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
const inRange = (value, min, max) => Number.isFinite(value) && value >= min && value <= max;

async function desktopCheck() {
  const ctx = await browser.newContext({ viewport: { width: 1440, height: 1600 }, deviceScaleFactor: 1 });
  const page = await ctx.newPage();
  await page.goto(BASE + '/', { waitUntil: 'networkidle' });
  await page.emulateMedia({ reducedMotion: 'reduce' });
  await page.waitForTimeout(150);

  const state = await page.evaluate(() => {
    const rect = (selector) => {
      const node = document.querySelector(selector);
      if (!node) return null;
      const r = node.getBoundingClientRect();
      return { x: r.x, y: r.y, width: r.width, height: r.height, right: r.right, bottom: r.bottom };
    };
    const cards = (selector) => [...document.querySelectorAll(selector)].map((node) => {
      const r = node.getBoundingClientRect();
      return { x: r.x, y: r.y, width: r.width, height: r.height, right: r.right, bottom: r.bottom };
    });
    const columns = (selector) => {
      const node = document.querySelector(selector);
      if (!node) return 0;
      return getComputedStyle(node).gridTemplateColumns.split(' ').filter(Boolean).length;
    };
    const physicalTextLines = (selector) => {
      const root = document.querySelector(selector);
      if (!root) return 0;
      const ys = [];
      const walker = document.createTreeWalker(root, NodeFilter.SHOW_TEXT);
      while (walker.nextNode()) {
        const text = walker.currentNode;
        if (!text.textContent || !text.textContent.trim()) continue;
        const range = document.createRange();
        range.selectNodeContents(text);
        for (const r of range.getClientRects()) {
          if (r.width > 1 && !ys.some((y) => Math.abs(y - r.y) < 2)) ys.push(r.y);
        }
      }
      return ys.length;
    };

    const serviceCards = cards('.ref-service');
    const projectCards = cards('.ref-project');
    const process = rect('.ref-process');
    const why = rect('.ref-why');
    const about = rect('.ref-about');
    const posts = rect('.ref-posts');
    const heroImage = document.querySelector('.ref-hero__visual img');

    return {
      wrap: rect('.ref-hero .wrap'),
      header: rect('.site-head'),
      hero: rect('.ref-hero'),
      metric: rect('.ref-metric'),
      serviceCards,
      projectCards,
      serviceColumns: columns('.ref-services__grid'),
      projectColumns: columns('.ref-projects__grid'),
      duoColumns: columns('.ref-duo'),
      editorialColumns: columns('.ref-editorial__grid'),
      process,
      why,
      about,
      posts,
      cta: rect('.ref-final-cta__card'),
      footer: rect('.site-foot'),
      heroTitleLines: physicalTextLines('.ref-hero__title'),
      heroImageReady: !!heroImage && heroImage.complete && heroImage.naturalWidth > 0 && heroImage.naturalHeight > 0,
      overflow: document.documentElement.scrollWidth - innerWidth,
      pageHeight: document.documentElement.scrollHeight,
    };
  });

  check(state.wrap && inRange(state.wrap.width, 1250, 1290), `DESKTOP 1280px referans rayi (${state.wrap?.width}px)`);
  check(state.header && inRange(state.header.height, 60, 69), `DESKTOP kompakt header (${state.header?.height}px)`);
  check(state.hero && inRange(state.hero.height, 360, 440), `DESKTOP hero referans yogunlugunda (${state.hero?.height}px)`);
  check(state.heroTitleLines === 3, `DESKTOP hero basligi CMS uc satirini fiziksel olarak koruyor (${state.heroTitleLines})`);
  check(state.heroImageReady, 'DESKTOP laptop hero gorseli gercekten decode edildi');
  check(state.metric && inRange(state.metric.height, 56, 72), `DESKTOP metrik bandi kompakt (${state.metric?.height}px)`);
  check(state.serviceColumns === 6, `DESKTOP hizmetler 6 kolon (${state.serviceColumns})`);
  check(state.serviceCards.length >= 4 && state.serviceCards.every(card => inRange(card.height, 130, 165)), `DESKTOP hizmet kartlari 130-165px (${state.serviceCards.map(c => Math.round(c.height)).join(',')})`);
  check(state.projectColumns === 3, `DESKTOP projeler 3 kolon (${state.projectColumns})`);
  check(state.projectCards.length === 0 || state.projectCards.every(card => inRange(card.height, 130, 175)), `DESKTOP proje kartlari kompakt (${state.projectCards.map(c => Math.round(c.height)).join(',')})`);
  check(state.duoColumns === 2, `DESKTOP surec + neden Arcates yan yana (${state.duoColumns})`);
  check(state.process && state.why && Math.abs(state.process.y - state.why.y) <= 2, `DESKTOP surec/avantaj ust hiza farki ${Math.abs((state.process?.y || 0) - (state.why?.y || 0)).toFixed(1)}px`);
  check(state.editorialColumns === 2, `DESKTOP biz kimiz + son yazilar yan yana (${state.editorialColumns})`);
  check((!state.about || !state.posts) || Math.abs(state.about.y - state.posts.y) <= 2, `DESKTOP editorial ust hiza farki ${Math.abs((state.about?.y || 0) - (state.posts?.y || 0)).toFixed(1)}px`);
  check(state.cta && inRange(state.cta.height, 95, 130), `DESKTOP final CTA kompakt (${state.cta?.height}px)`);
  check(state.footer && state.footer.height < 350, `DESKTOP footer referans yogunlugunda (${state.footer?.height}px)`);
  check(state.overflow <= 1, `DESKTOP yatay tasma yok (${state.overflow}px)`);
  check(state.pageHeight < 2050, `DESKTOP referans gibi sikistirilmis tek akis (${state.pageHeight}px)`);

  await page.screenshot({ path: '/tmp/arcates-reference-parity/desktop.png', fullPage: true });
  await ctx.close();
}

async function mobileCheck() {
  const ctx = await browser.newContext({ viewport: { width: 390, height: 844 }, deviceScaleFactor: 1, isMobile: true, hasTouch: true });
  const page = await ctx.newPage();
  await page.goto(BASE + '/', { waitUntil: 'networkidle' });
  await page.emulateMedia({ reducedMotion: 'reduce' });
  await page.waitForTimeout(150);

  const state = await page.evaluate(() => {
    const rect = (selector) => {
      const node = document.querySelector(selector);
      if (!node) return null;
      const r = node.getBoundingClientRect();
      return { x: r.x, y: r.y, width: r.width, height: r.height, right: r.right, bottom: r.bottom };
    };
    const columns = (selector) => {
      const node = document.querySelector(selector);
      if (!node) return 0;
      return getComputedStyle(node).gridTemplateColumns.split(' ').filter(Boolean).length;
    };
    const firstProject = document.querySelector('.ref-project > a');
    const projectMedia = firstProject?.querySelector('.ref-project__media');
    const projectBody = firstProject?.querySelector('.ref-project__body');
    const firstPost = document.querySelector('.ref-post');
    const postMedia = firstPost?.querySelector('.ref-post__media');
    const postBody = firstPost?.querySelector('.ref-post__body');
    const heroImage = document.querySelector('.ref-hero__visual img');
    const r = (node) => {
      if (!node) return null;
      const b = node.getBoundingClientRect();
      return { x: b.x, y: b.y, width: b.width, height: b.height, right: b.right, bottom: b.bottom };
    };
    return {
      header: rect('.site-head'),
      brand: rect('.brand'),
      mobileCta: rect('.ref-mobile-head-cta'),
      menuToggle: rect('.site-nav__toggle'),
      hero: rect('.ref-hero'),
      heroVisual: rect('.ref-hero__visual'),
      heroImageReady: !!heroImage && heroImage.complete && heroImage.naturalWidth > 0 && heroImage.naturalHeight > 0,
      metric: rect('.ref-metric'),
      service: rect('.ref-service'),
      serviceColumns: columns('.ref-services__grid'),
      projectColumns: firstProject ? getComputedStyle(firstProject).gridTemplateColumns.split(' ').filter(Boolean).length : 0,
      projectMedia: r(projectMedia),
      projectBody: r(projectBody),
      processColumns: columns('.ref-process__list'),
      whyColumns: columns('.ref-why__body'),
      postMedia: r(postMedia),
      postBody: r(postBody),
      cta: rect('.ref-final-cta__card'),
      footer: rect('.site-foot'),
      overflow: document.documentElement.scrollWidth - innerWidth,
      pageHeight: document.documentElement.scrollHeight,
    };
  });

  check(state.header && inRange(state.header.height, 60, 69), `MOBILE kompakt header (${state.header?.height}px)`);
  check(state.brand && state.mobileCta && state.menuToggle, 'MOBILE logo + CTA + hamburger birlikte render edildi');
  check(state.brand && state.mobileCta && state.menuToggle && state.brand.x < state.mobileCta.x && state.mobileCta.x < state.menuToggle.x, 'MOBILE header sirasi logo -> CTA -> hamburger');
  check(state.mobileCta && inRange(state.mobileCta.height, 30, 44), `MOBILE header CTA referans yuksekligi (${state.mobileCta?.height}px)`);
  check(state.hero && inRange(state.hero.height, 520, 760), `MOBILE hero tek kolon akisi (${state.hero?.height}px)`);
  check(state.heroVisual && inRange(state.heroVisual.height, 230, 310), `MOBILE hero gorseli telefon oraninda (${state.heroVisual?.height}px)`);
  check(state.heroImageReady, 'MOBILE laptop hero gorseli gercekten decode edildi');
  check(state.metric && inRange(state.metric.height, 52, 66), `MOBILE metrik karti kompakt (${state.metric?.height}px)`);
  check(state.serviceColumns === 1, `MOBILE hizmetler tek kolon (${state.serviceColumns})`);
  check(state.service && inRange(state.service.height, 82, 104), `MOBILE hizmet karti referans yuksekligi (${state.service?.height}px)`);
  check(state.projectColumns === 2, `MOBILE proje karti metin+gorsel iki kolon (${state.projectColumns})`);
  check(state.projectMedia && state.projectBody && state.projectMedia.x > state.projectBody.x, 'MOBILE proje gorseli sagda, metin solda');
  check(state.processColumns === 1, `MOBILE surec dikey akis (${state.processColumns})`);
  check(state.whyColumns === 2, `MOBILE neden Arcates iki kolon (${state.whyColumns})`);
  check(state.postMedia && state.postBody && state.postBody.y >= state.postMedia.y && state.postBody.bottom <= state.postMedia.bottom + 1, 'MOBILE blog metni gorsel uzerinde');
  check(state.cta && inRange(state.cta.height, 300, 370), `MOBILE final CTA referans kart orani (${state.cta?.height}px)`);
  check(state.footer && state.footer.height < 720, `MOBILE footer referans yogunlugunda (${state.footer?.height}px)`);
  check(state.overflow <= 1, `MOBILE yatay tasma yok (${state.overflow}px)`);
  check(state.pageHeight < 4900, `MOBILE gereksiz dikey bosluk yok (${state.pageHeight}px)`);

  await page.screenshot({ path: '/tmp/arcates-reference-parity/mobile.png', fullPage: true });
  await ctx.close();
}

await import('node:fs').then(({ mkdirSync }) => mkdirSync('/tmp/arcates-reference-parity', { recursive: true }));
await desktopCheck();
await mobileCheck();
await browser.close();
console.log(fail === 0 ? '\nREFERANS GEOMETRI DENETIMLERI GECTI' : `\n${fail} REFERANS GEOMETRI DENETIMI KALDI`);
process.exit(fail === 0 ? 0 : 1);
