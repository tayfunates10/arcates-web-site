/* Arcates R3 G-08/G-10 CTA + status acceptance checks — real Chromium. */
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

for (const viewport of [{ width: 1280, height: 900 }, { width: 390, height: 844 }]) {
  const ctx = await browser.newContext({ viewport });
  const page = await ctx.newPage();

  await page.goto(BASE + '/', { waitUntil: 'networkidle' });
  const cta = await page.evaluate(() => {
    const card = document.querySelector('.ref-final-cta__card');
    const action = document.querySelector('.ref-final-cta__action .ref-btn[href]');
    const background = card ? getComputedStyle(card).backgroundImage : '';
    return {
      card: !!card,
      background,
      action: !!action,
      href: action?.getAttribute('href') || '',
      overflow: document.documentElement.scrollWidth - innerWidth,
    };
  });
  check(cta.card && cta.background.includes('cta-mountain.webp'), `G-08 ${viewport.width}px referans CTA gorseli mevcut`);
  check(cta.action && cta.href && cta.href !== '#', `G-08 ${viewport.width}px CTA eylemi gercek URL tasiyor`);
  check(cta.overflow <= 1, `G-08 ${viewport.width}px CTA yatay tasma yok (${cta.overflow}px)`);

  await page.goto(BASE + '/tesekkurler', { waitUntil: 'networkidle' });
  const success = await page.evaluate(() => ({
    mark: !!document.querySelector('.state-mark--success'),
    robots: document.querySelector('meta[name="robots"]')?.content || '',
    overflow: document.documentElement.scrollWidth - innerWidth,
  }));
  check(success.mark, `G-10 ${viewport.width}px tesekkur basari isareti mevcut`);
  check(success.robots.includes('noindex'), `G-10 ${viewport.width}px tesekkur noindex korunuyor`);
  check(success.overflow <= 1, `G-10 ${viewport.width}px tesekkur yatay tasma yok (${success.overflow}px)`);

  await page.goto(BASE + '/blog?kategori=__r3_bos__', { waitUntil: 'networkidle' });
  const empty = await page.evaluate(() => ({
    state: !!document.querySelector('.empty-state[role="status"] .state-mark--empty'),
    overflow: document.documentElement.scrollWidth - innerWidth,
  }));
  check(empty.state, `G-10 ${viewport.width}px blog bos durum isareti mevcut`);
  check(empty.overflow <= 1, `G-10 ${viewport.width}px bos durum yatay tasma yok (${empty.overflow}px)`);

  const response = await page.goto(BASE + '/r3-kesinlikle-olmayan-sayfa', { waitUntil: 'networkidle' });
  const errorState = await page.evaluate(() => ({
    mark: !!document.querySelector('.system__visual.state-mark--error'),
    robots: document.querySelector('meta[name="robots"]')?.content || '',
    overflow: document.documentElement.scrollWidth - innerWidth,
  }));
  check(response?.status() === 404, `G-10 ${viewport.width}px 404 HTTP durumu korunuyor`);
  check(errorState.mark, `G-10 ${viewport.width}px 404 hata isareti mevcut`);
  check(errorState.robots.includes('noindex'), `G-10 ${viewport.width}px 404 noindex korunuyor`);
  check(errorState.overflow <= 1, `G-10 ${viewport.width}px 404 yatay tasma yok (${errorState.overflow}px)`);

  await ctx.close();
}

await browser.close();
console.log(fail === 0 ? '\nTUM G-08/G-10 DURUM DENETIMLERI GECTI' : `\n${fail} G-08/G-10 DENETIMI KALDI`);
process.exit(fail === 0 ? 0 : 1);
