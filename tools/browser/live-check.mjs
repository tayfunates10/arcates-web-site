/* Arcates R9 live-host acceptance — real Chromium, GET-only smoke checks. */
let chromium;
try {
  ({ chromium } = await import(process.env.PLAYWRIGHT_PATH || 'playwright'));
} catch (error) {
  console.error('Playwright bulunamadi.');
  console.error(String(error.message || error));
  process.exit(2);
}

const rawBase = process.argv[2] || process.env.ARC_LIVE_BASE_URL || '';
const adminPath = String(process.argv[3] || process.env.ARC_LIVE_ADMIN_PATH || 'panel').replace(/^\/+|\/+$/g, '');
const redirectFrom = process.argv[4] || process.env.ARC_LIVE_REDIRECT_FROM || '';
const redirectTo = process.argv[5] || process.env.ARC_LIVE_REDIRECT_TO || '';

if (!rawBase) {
  console.error('Kullanim: node tools/browser/live-check.mjs https://ornek.test [admin_path] [redirect_from] [redirect_to]');
  process.exit(2);
}

let base;
try {
  base = new URL(rawBase);
} catch {
  console.error('Gecersiz base URL.');
  process.exit(2);
}

if (base.protocol !== 'https:') {
  console.error('Canli kabul yalniz HTTPS URL ile calisir.');
  process.exit(2);
}
if (base.username || base.password) {
  console.error('Base URL kullanici bilgisi icermemeli.');
  process.exit(2);
}
base.pathname = base.pathname.replace(/\/$/, '');
base.search = '';
base.hash = '';
const BASE = base.href.replace(/\/$/, '');

console.log('NOT: Bu smoke testi yalnız HTTP GET yapar; form/panel mutasyonu yapmaz. Uygulamanin normal ziyaret, 404 ve redirect sayaçları bu istekleri kaydedebilir.');

const browser = await chromium.launch();
let fail = 0;
const check = (ok, msg) => {
  console.log((ok ? 'GECTI ' : 'KALDI ') + msg);
  if (!ok) fail++;
};

function parsedUrl(value) {
  try {
    return new URL(value, BASE);
  } catch {
    return null;
  }
}

function sameOrigin(value) {
  const url = parsedUrl(value);
  return url !== null && url.origin === base.origin;
}

function canonicalMatches(value, finalUrl) {
  const canonical = parsedUrl(value);
  if (!canonical) return false;
  const expectedPath = finalUrl.pathname.replace(/\/$/, '') || '/';
  const canonicalPath = canonical.pathname.replace(/\/$/, '') || '/';
  return canonical.origin === base.origin && canonicalPath === expectedPath;
}

async function documentState(page) {
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
      canonical: document.querySelector('link[rel="canonical"]')?.href || '',
      ogImage: document.querySelector('meta[property="og:image"]')?.content || '',
      favicon: document.querySelector('link[rel~="icon"]')?.href || '',
    };
  });
}

function contained(state, width) {
  return state.overflow <= 1
    && state.h1Count === 1
    && state.mainLeft !== null && state.mainLeft >= -1
    && state.mainRight !== null && state.mainRight <= width + 1
    && state.h1Left !== null && state.h1Left >= -1
    && state.h1Right !== null && state.h1Right <= width + 1;
}

const routes = ['/', '/web-tasarim', '/iletisim', '/blog', '/sss'];
for (const viewport of [{ width: 390, height: 844 }, { width: 1366, height: 900 }]) {
  const context = await browser.newContext({ viewport });
  const page = await context.newPage();

  for (const route of routes) {
    const response = await page.goto(BASE + route, { waitUntil: 'domcontentloaded', timeout: 30000 });
    await page.waitForTimeout(100);
    const state = await documentState(page);
    const finalUrl = new URL(page.url());
    check(response?.status() === 200, `R9 ${viewport.width}px ${route} HTTP 200`);
    check(finalUrl.protocol === 'https:' && finalUrl.origin === base.origin,
      `R9 ${viewport.width}px ${route} ayni HTTPS canonical hostta`);
    check(contained(state, viewport.width),
      `R9 ${viewport.width}px ${route} tek H1, viewport icinde ve yatay tasmasiz (${state.overflow}px)`);
    check(state.canonical !== '' && sameOrigin(state.canonical) && canonicalMatches(state.canonical, finalUrl),
      `R9 ${viewport.width}px ${route} canonical ayni origin ve dogru path`);
  }

  await context.close();
}

// Public machine-readable endpoints.
{
  const context = await browser.newContext({ viewport: { width: 1280, height: 900 } });
  const robotsResponse = await context.request.get(BASE + '/robots.txt', { failOnStatusCode: false });
  const robots = await robotsResponse.text();
  const sitemapMatch = robots.match(/^Sitemap:\s*(\S+)\s*$/im);
  const robotsSitemap = sitemapMatch ? parsedUrl(sitemapMatch[1]) : null;
  check(robotsResponse.status() === 200, 'R9 robots.txt HTTP 200');
  check(robotsSitemap !== null && robotsSitemap.protocol === 'https:' && robotsSitemap.origin === base.origin,
    'R9 robots.txt ayni production origininde HTTPS Sitemap satiri tasiyor');
  check(robots.includes(`Disallow: /${adminPath}`), `R9 robots.txt /${adminPath} yolunu engelliyor`);

  const sitemapResponse = await context.request.get(BASE + '/sitemap.xml', { failOnStatusCode: false });
  const sitemap = await sitemapResponse.text();
  const locs = [...sitemap.matchAll(/<loc>([^<]+)<\/loc>/gi)].map(match => parsedUrl(match[1])).filter(Boolean);
  check(sitemapResponse.status() === 200, 'R9 sitemap.xml HTTP 200');
  check(/<urlset\b/i.test(sitemap) && locs.length > 0, 'R9 sitemap.xml URL listesi uretiyor');
  check(locs.length > 0 && locs.every(url => url.protocol === 'https:' && url.origin === base.origin),
    `R9 sitemap.xml tum loc adreslerini ayni HTTPS production origininde tutuyor (${locs.length})`);

  const home = await context.newPage();
  const homeResponse = await home.goto(BASE + '/', { waitUntil: 'domcontentloaded', timeout: 30000 });
  const homeState = await documentState(home);
  check(homeResponse?.status() === 200, 'R9 ana sayfa varlik kontrolu icin acildi');

  if (homeState.favicon) {
    const faviconResponse = await context.request.get(homeState.favicon, { failOnStatusCode: false });
    check(sameOrigin(homeState.favicon) && faviconResponse.status() >= 200 && faviconResponse.status() < 400,
      'R9 favicon ayni origindeki gercek URLden aciliyor');
  } else {
    check(false, 'R9 favicon linki mevcut');
  }

  if (homeState.ogImage) {
    const ogResponse = await context.request.get(homeState.ogImage, { failOnStatusCode: false });
    check(sameOrigin(homeState.ogImage) && ogResponse.status() >= 200 && ogResponse.status() < 400,
      'R9 OG gorseli ayni origindeki gercek URLden aciliyor');
  } else {
    check(false, 'R9 og:image mevcut');
  }

  const missing = await home.goto(BASE + '/__arcates-r9-live-404__', { waitUntil: 'domcontentloaded', timeout: 30000 });
  const missingState = await documentState(home);
  check(missing?.status() === 404, 'R9 bilinmeyen rota HTTP 404');
  check(contained(missingState, 1280), 'R9 404 sayfasi tek H1 ve viewport containment koruyor');

  await context.close();
}

// Optional known legacy-slug redirect proof. Both values must be supplied together.
if (redirectFrom || redirectTo) {
  check(Boolean(redirectFrom && redirectTo), 'R9 redirect_from ve redirect_to birlikte verildi');
  if (redirectFrom && redirectTo) {
    const context = await browser.newContext();
    const fromUrl = new URL(redirectFrom, BASE);
    const expectedUrl = new URL(redirectTo, BASE);
    check(fromUrl.origin === base.origin && expectedUrl.origin === base.origin,
      'R9 redirect kaniti production origin disina cikmiyor');
    const response = await context.request.get(fromUrl.href, { maxRedirects: 0, failOnStatusCode: false });
    const location = response.headers()['location'] || '';
    let actual = '';
    try { actual = new URL(location, fromUrl).href; } catch {}
    check(response.status() === 301, `R9 eski slug 301 donuyor (${fromUrl.pathname})`);
    check(actual === expectedUrl.href, `R9 eski slug beklenen hedefe gidiyor (${expectedUrl.pathname})`);
    await context.close();
  }
}

await browser.close();
console.log(fail === 0 ? '\nTUM R9 CANLI HOST DENETIMLERI GECTI' : `\n${fail} R9 CANLI HOST DENETIMI KALDI`);
process.exit(fail === 0 ? 0 : 1);
