/* Referans anasayfa goruntu yakalama — CI artefakti. */
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

const captures = [
  { name: 'desktop', viewport: { width: 1440, height: 900 }, path: '/tmp/arcates-reference-desktop.png' },
  { name: 'mobile', viewport: { width: 390, height: 844 }, path: '/tmp/arcates-reference-mobile.png' },
];

for (const capture of captures) {
  const ctx = await browser.newContext({ viewport: capture.viewport, deviceScaleFactor: 1 });
  const page = await ctx.newPage();
  const response = await page.goto(BASE + '/', { waitUntil: 'networkidle' });
  if (!response || response.status() !== 200) {
    console.error(`KALDI ${capture.name}: HTTP ${response?.status() ?? 'yok'}`);
    await ctx.close();
    await browser.close();
    process.exit(1);
  }

  await page.evaluate(async () => {
    if (document.fonts?.ready) await document.fonts.ready;
    window.scrollTo(0, 0);
  });
  await page.waitForTimeout(300);
  await page.screenshot({ path: capture.path, fullPage: true, animations: 'disabled' });

  const info = await page.evaluate(() => ({
    width: innerWidth,
    height: document.documentElement.scrollHeight,
    overflow: document.documentElement.scrollWidth - innerWidth,
    h1: document.querySelectorAll('h1').length,
  }));
  console.log(`GECTI ${capture.name}: ${info.width}x${info.height}, overflow=${info.overflow}, h1=${info.h1}`);
  if (info.overflow > 1 || info.h1 !== 1) {
    await ctx.close();
    await browser.close();
    process.exit(1);
  }
  await ctx.close();
}

await browser.close();
console.log('REFERANS EKRAN GORUNTULERI HAZIR');
