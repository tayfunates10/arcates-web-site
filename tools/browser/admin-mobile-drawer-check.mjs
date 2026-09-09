/* Arcates mobile admin drawer regression check — real Chromium. */
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
const context = await browser.newContext({ viewport: { width: 390, height: 844 } });
const page = await context.newPage();
let fail = 0;
const check = (ok, msg) => { console.log((ok ? 'GECTI ' : 'KALDI ') + msg); if (!ok) fail++; };

await page.goto(BASE + '/panel/giris', { waitUntil: 'networkidle' });
await page.locator('#email').fill(EMAIL);
await page.locator('#password').fill(PASSWORD);
await Promise.all([
  page.waitForURL(url => url.pathname === '/panel' || url.pathname === '/panel/'),
  page.locator('form').filter({ has: page.locator('#email') }).locator('button[type="submit"]').click(),
]);

const toggle = page.locator('[data-admin-nav-toggle]');
await toggle.click();

const state = await page.evaluate(() => {
  const side = document.querySelector('[data-admin-side]');
  const brand = side?.querySelector('.admin__brand');
  const firstLink = side?.querySelector('.admin__nav-link');
  const rect = side?.getBoundingClientRect();
  const brandRect = brand?.getBoundingClientRect();
  const firstRect = firstLink?.getBoundingClientRect();
  const style = side ? getComputedStyle(side) : null;

  return {
    width: rect?.width ?? 0,
    top: rect?.top ?? 999,
    bottom: rect?.bottom ?? 0,
    brandTop: brandRect?.top ?? 999,
    firstNavTop: firstRect?.top ?? 999,
    navCount: side?.querySelectorAll('.admin__nav-link').length ?? 0,
    overflowY: style?.overflowY ?? '',
    justify: style?.justifyContent ?? '',
    bodyOverflow: getComputedStyle(document.body).overflow,
    docOverflow: document.documentElement.scrollWidth - innerWidth,
  };
});

check(state.width <= 273 && state.width >= 220, `mobil drawer kompakt genislikte (${state.width}px)`);
check(Math.abs(state.top) <= 1 && state.bottom >= 843, `mobil drawer viewport yuksekligini kapliyor (${state.top}..${state.bottom})`);
check(state.brandTop < 40, `mobil marka ustten basliyor (${state.brandTop}px)`);
check(state.firstNavTop < 170, `ilk menu ogesi ust bolumde (${state.firstNavTop}px)`);
check(state.navCount >= 10, `admin menu ogeleri drawer icinde mevcut (${state.navCount})`);
check(state.overflowY === 'auto' || state.overflowY === 'scroll', `drawer dikey kaydirilabilir (${state.overflowY})`);
check(state.justify === 'flex-start' || state.justify === 'normal', `drawer icerigi dikey ortalanmiyor (${state.justify})`);
check(state.bodyOverflow === 'hidden', `drawer acikken arka sayfa kaymasi kilitli (${state.bodyOverflow})`);
check(state.docOverflow <= 1, `mobil panel yatay tasma yok (${state.docOverflow}px)`);

await page.keyboard.press('Escape');
check(await toggle.getAttribute('aria-expanded') === 'false', 'Escape drawer kapatir');

await context.close();
await browser.close();
console.log(fail === 0 ? '\nMOBIL ADMIN DRAWER DENETIMI GECTI' : `\n${fail} MOBIL DRAWER DENETIMI KALDI`);
process.exit(fail === 0 ? 0 : 1);
