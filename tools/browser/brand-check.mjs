/* Arcates R3 G-09 social + favicon acceptance checks — real Chromium. */
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
const ctx = await browser.newContext({ viewport: { width: 1280, height: 900 } });
const page = await ctx.newPage();
let fail = 0;
const check = (ok, msg) => { console.log((ok ? 'GECTI ' : 'KALDI ') + msg); if (!ok) fail++; };
const pngSize = (buffer) => {
  if (!buffer || buffer.length < 24) return null;
  const sig = buffer.subarray(0, 8).toString('hex');
  if (sig !== '89504e470d0a1a0a') return null;
  return { width: buffer.readUInt32BE(16), height: buffer.readUInt32BE(20) };
};

await page.goto(BASE + '/', { waitUntil: 'networkidle' });
const head = await page.evaluate(() => ({
  ogImage: document.querySelector('meta[property="og:image"]')?.content || '',
  ogType: document.querySelector('meta[property="og:image:type"]')?.content || '',
  ogWidth: document.querySelector('meta[property="og:image:width"]')?.content || '',
  ogHeight: document.querySelector('meta[property="og:image:height"]')?.content || '',
  twitterImage: document.querySelector('meta[name="twitter:image"]')?.content || '',
  twitterCard: document.querySelector('meta[name="twitter:card"]')?.content || '',
  themeColor: document.querySelector('meta[name="theme-color"]')?.content || '',
  icons: [...document.querySelectorAll('link[rel="icon"]')].map((link) => ({ href: link.href, sizes: link.sizes.value, type: link.type })),
  apple: (() => {
    const link = document.querySelector('link[rel="apple-touch-icon"]');
    return link ? { href: link.href, sizes: link.sizes.value } : null;
  })(),
  headerMark: getComputedStyle(document.querySelector('.brand__mark')).backgroundImage,
  footerLogo: document.querySelector('.site-foot__logo')?.getAttribute('src') || '',
}));

check(head.ogImage.endsWith('/assets/social-card.php'), 'G-09 varsayilan OG sosyal kart endpointi kullaniliyor');
check(head.ogType === 'image/png', `G-09 OG MIME image/png (${head.ogType})`);
check(head.ogWidth === '1200' && head.ogHeight === '630', `G-09 OG metadata 1200x630 (${head.ogWidth}x${head.ogHeight})`);
check(head.twitterCard === 'summary_large_image', 'G-09 Twitter large card korunuyor');
check(head.twitterImage === head.ogImage, 'G-09 Twitter ve OG ayni gorseli kullaniyor');
check(head.themeColor.toLowerCase() === '#081426', `G-09 tema rengi Arcates navy (${head.themeColor})`);

for (const size of ['16x16', '32x32', '48x48', '192x192']) {
  check(head.icons.some((icon) => icon.sizes === size && icon.type === 'image/png'), `G-09 favicon ${size} bildirimi mevcut`);
}
check(head.apple?.sizes === '180x180', 'G-09 Apple touch 180x180 bildirimi mevcut');
check(head.headerMark.includes('logo-mark.png'), 'G-09 header onayli logo-mark kaynagini kullaniyor');
check(head.footerLogo.endsWith('/assets/img/logo-wordmark.png'), 'G-09 footer onayli wordmark kaynagini kullaniyor');

const targets = [
  ['/assets/social-card.php', 1200, 630, 350 * 1024],
  ['/assets/brand-icon.php?size=16', 16, 16, 32 * 1024],
  ['/assets/img/favicon-32.png', 32, 32, 32 * 1024],
  ['/assets/brand-icon.php?size=48', 48, 48, 48 * 1024],
  ['/assets/img/logo-mark.png', 192, 192, 96 * 1024],
  ['/assets/img/apple-touch-icon.png', 180, 180, 96 * 1024],
];

for (const [path, width, height, maxBytes] of targets) {
  const response = await ctx.request.get(BASE + path);
  const body = await response.body();
  const size = pngSize(body);
  const contentType = response.headers()['content-type'] || '';
  check(response.status() === 200, `G-09 ${path} HTTP 200`);
  check(contentType.includes('image/png'), `G-09 ${path} image/png donuyor`);
  check(size?.width === width && size?.height === height, `G-09 ${path} ${width}x${height} piksel`);
  check(body.length > 100 && body.length <= maxBytes, `G-09 ${path} dosya butcesi (${body.length} bayt)`);
}

const invalid = await ctx.request.get(BASE + '/assets/brand-icon.php?size=17');
check(invalid.status() === 404, 'G-09 belgelenmemis favicon boyutu reddediliyor');

await ctx.close();
await browser.close();
console.log(fail === 0 ? '\nTUM G-09 MARKA DENETIMLERI GECTI' : `\n${fail} G-09 DENETIMI KALDI`);
process.exit(fail === 0 ? 0 : 1);
