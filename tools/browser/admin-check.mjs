/* Arcates R7 admin/system acceptance checks — real Chromium. */
import fs from 'node:fs';

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

const desktopRoutes = [
  '/panel',
  '/panel/sayfalar',
  '/panel/sayfalar/yeni',
  '/panel/medya',
  '/panel/seo',
  '/panel/ayarlar',
  '/panel/yedekleme',
  '/panel/yonlendirmeler',
];

const ctx = await browser.newContext({ viewport: { width: 1366, height: 900 } });
const page = await ctx.newPage();

// R7-01: login screen uses the new auth layer and normal form login works.
{
  const response = await page.goto(BASE + '/panel/giris', { waitUntil: 'networkidle' });
  const state = await page.evaluate(() => ({
    body: document.body.className,
    h1: document.querySelectorAll('h1').length,
    r7Sheet: [...document.styleSheets].some(s => (s.href || '').includes('admin-r7.css')),
    cardRadius: parseFloat(getComputedStyle(document.querySelector('.system__card')).borderRadius),
    overflow: document.documentElement.scrollWidth - innerWidth,
  }));
  check(response?.status() === 200, 'R7 /panel/giris HTTP 200');
  check(state.body.includes('admin--r7') && state.body.includes('admin--auth'), 'R7 giris body siniflari aktif');
  check(state.r7Sheet, 'R7 giris admin-r7.css yuklendi');
  check(state.h1 === 1, `R7 giris tek H1 (${state.h1})`);
  check(state.cardRadius >= 22, `R7 giris karti yeni yuzey ailesinde (${state.cardRadius}px)`);
  check(state.overflow <= 1, `R7 giris yatay tasma yok (${state.overflow}px)`);

  await page.locator('#email').fill(EMAIL);
  await page.locator('#password').fill(PASSWORD);
  await Promise.all([
    page.waitForURL(url => url.pathname === '/panel' || url.pathname === '/panel/'),
    page.locator('form').filter({ has: page.locator('#email') }).locator('button[type="submit"]').click(),
  ]);
  check(new URL(page.url()).pathname.replace(/\/$/, '') === '/panel', 'R7 gercek form girisi panoya yonlendi');
}

// R7-02: representative admin families share the workspace shell.
for (const route of desktopRoutes) {
  const response = await page.goto(BASE + route, { waitUntil: 'networkidle' });
  const state = await page.evaluate(() => {
    const panel = document.querySelector('.panel');
    const tableScroll = document.querySelector('.table-scroll');
    const filter = document.querySelector('.filters');
    const nav = document.querySelector('.admin__nav-link');
    return {
      h1: document.querySelectorAll('h1').length,
      bodyR7: document.body.classList.contains('admin--r7'),
      shell: !!document.querySelector('.admin__shell'),
      side: !!document.querySelector('.admin__side'),
      r7Sheet: [...document.styleSheets].some(s => (s.href || '').includes('admin-r7.css')),
      panelRadius: panel ? parseFloat(getComputedStyle(panel).borderRadius) : null,
      filterRadius: filter ? parseFloat(getComputedStyle(filter).borderRadius) : null,
      tableRadius: tableScroll ? parseFloat(getComputedStyle(tableScroll).borderRadius) : null,
      navHeight: nav?.getBoundingClientRect().height ?? 0,
      overflow: document.documentElement.scrollWidth - innerWidth,
    };
  });
  check(response?.status() === 200, `R7 ${route} HTTP 200`);
  check(state.bodyR7 && state.r7Sheet, `R7 ${route} tasarim katmani aktif`);
  check(state.shell && state.side, `R7 ${route} ortak workspace kabugu`);
  check(state.h1 === 1, `R7 ${route} tek H1 (${state.h1})`);
  check(state.navHeight >= 44, `R7 ${route} panel nav dokunma alani (${state.navHeight}px)`);
  if (state.panelRadius !== null) check(state.panelRadius >= 18, `R7 ${route} panel yuzeyi (${state.panelRadius}px)`);
  if (state.filterRadius !== null) check(state.filterRadius >= 18, `R7 ${route} arac cubugu yuzeyi (${state.filterRadius}px)`);
  if (state.tableRadius !== null) check(state.tableRadius >= 14, `R7 ${route} tablo yuzeyi (${state.tableRadius}px)`);
  check(state.overflow <= 1, `R7 ${route} masaustunde yatay tasma yok (${state.overflow}px)`);
}

// R7-03: dashboard KPI hierarchy and destructive actions are visually distinct.
{
  await page.goto(BASE + '/panel', { waitUntil: 'networkidle' });
  const dash = await page.evaluate(() => {
    const cards = [...document.querySelectorAll('.card--stat')];
    const value = document.querySelector('.card__value');
    return {
      cards: cards.length,
      valueSize: value ? parseFloat(getComputedStyle(value).fontSize) : 0,
      columns: cards.length > 1 ? Math.round(cards[1].getBoundingClientRect().left - cards[0].getBoundingClientRect().left) : 0,
    };
  });
  check(dash.cards === 4, `R7 pano KPI kartlari mevcut (${dash.cards})`);
  check(dash.valueSize >= 30, `R7 pano KPI degeri belirgin (${dash.valueSize}px)`);

  await page.goto(BASE + '/panel/sayfalar', { waitUntil: 'networkidle' });
  const action = await page.evaluate(() => {
    const danger = document.querySelector('.btn--danger');
    const primary = document.querySelector('.btn--primary');
    if (!danger || !primary) return null;
    const d = getComputedStyle(danger);
    const p = getComputedStyle(primary);
    return { dangerColor: d.color, primaryColor: p.backgroundImage || p.backgroundColor, dangerHeight: danger.getBoundingClientRect().height };
  });
  check(action !== null, 'R7 sayfa listesinde birincil ve tehlikeli eylemler mevcut');
  if (action) {
    check(action.dangerColor === 'rgb(185, 56, 40)', `R7 tehlikeli eylem rolu ayri (${action.dangerColor})`);
    check(action.dangerHeight >= 40, `R7 satir eylemi dokunma alani (${action.dangerHeight}px)`);
  }
}

// R7-04: mobile shell has no document overflow and off-canvas nav remains keyboard-operable.
{
  const mobile = await browser.newContext({ viewport: { width: 390, height: 844 } });
  const m = await mobile.newPage();
  await m.goto(BASE + '/panel/giris', { waitUntil: 'networkidle' });
  await m.locator('#email').fill(EMAIL);
  await m.locator('#password').fill(PASSWORD);
  await Promise.all([
    m.waitForURL(url => url.pathname === '/panel' || url.pathname === '/panel/'),
    m.locator('form').filter({ has: m.locator('#email') }).locator('button[type="submit"]').click(),
  ]);

  for (const route of ['/panel', '/panel/sayfalar', '/panel/medya', '/panel/seo', '/panel/ayarlar']) {
    await m.goto(BASE + route, { waitUntil: 'networkidle' });
    const state = await m.evaluate(() => ({
      overflow: document.documentElement.scrollWidth - innerWidth,
      toggleDisplay: getComputedStyle(document.querySelector('[data-admin-nav-toggle]')).display,
      contentLeft: document.querySelector('#panel-icerik')?.getBoundingClientRect().left ?? null,
      contentRight: document.querySelector('#panel-icerik')?.getBoundingClientRect().right ?? null,
    }));
    check(state.overflow <= 1, `R7 mobil ${route} belge yatay tasma yok (${state.overflow}px)`);
    check(state.toggleDisplay !== 'none', `R7 mobil ${route} menu dugmesi gorunur`);
    check(state.contentLeft !== null && state.contentLeft >= -1 && state.contentRight <= 391,
      `R7 mobil ${route} ana icerik viewport icinde (${state.contentLeft}..${state.contentRight})`);
  }

  await m.goto(BASE + '/panel/sayfalar', { waitUntil: 'networkidle' });
  const toggle = m.locator('[data-admin-nav-toggle]');
  await toggle.click();
  check(await toggle.getAttribute('aria-expanded') === 'true', 'R7 mobil menu acilinca aria-expanded true');
  check(await m.locator('[data-admin-side]').evaluate(el => el.classList.contains('is-open')), 'R7 mobil yan menu acildi');
  await m.keyboard.press('Escape');
  check(await toggle.getAttribute('aria-expanded') === 'false', 'R7 mobil Escape menuyu kapatti');
  check(await toggle.evaluate(el => el === document.activeElement), 'R7 mobil Escape sonrasi odak menu dugmesine dondu');

  await mobile.close();
}

// R7-05: public 404 uses the standalone R7 system design.
{
  const publicPage = await ctx.newPage();
  const response = await publicPage.goto(BASE + '/r7-olmayan-sayfa-kontrolu', { waitUntil: 'networkidle' });
  const state = await publicPage.evaluate(() => ({
    body: document.body.className,
    h1: document.querySelectorAll('h1').length,
    r7Sheet: [...document.styleSheets].some(s => (s.href || '').includes('system-r7.css')),
    codeSize: parseFloat(getComputedStyle(document.querySelector('.system__code')).fontSize),
    overflow: document.documentElement.scrollWidth - innerWidth,
  }));
  check(response?.status() === 404, 'R7 404 rotasi HTTP 404');
  check(state.body.includes('system-r7--error') && state.r7Sheet, 'R7 404 sistem tasarimi aktif');
  check(state.h1 === 1, `R7 404 tek H1 (${state.h1})`);
  check(state.codeSize >= 42, `R7 404 hata kodu belirgin (${state.codeSize}px)`);
  check(state.overflow <= 1, `R7 404 yatay tasma yok (${state.overflow}px)`);
  await publicPage.close();
}

// R7-06: install page can still render when the lock is absent; restore lock after check.
{
  const lock = 'storage/installed.lock';
  const hadLock = fs.existsSync(lock);
  try {
    if (hadLock) fs.unlinkSync(lock);
    const install = await ctx.newPage();
    const response = await install.goto(BASE + '/install', { waitUntil: 'networkidle' });
    const state = await install.evaluate(() => ({
      body: document.body.className,
      r7Sheet: [...document.styleSheets].some(s => (s.href || '').includes('system-r7.css')),
      steps: document.querySelectorAll('.system__steps li').length,
      cardRadius: parseFloat(getComputedStyle(document.querySelector('.system__card')).borderRadius),
      overflow: document.documentElement.scrollWidth - innerWidth,
    }));
    check(response?.status() === 200, 'R7 /install kilit yokken HTTP 200');
    check(state.body.includes('system-r7--install') && state.r7Sheet, 'R7 install sistem tasarimi aktif');
    check(state.steps === 3, `R7 install uc adimli akisi koruyor (${state.steps})`);
    check(state.cardRadius >= 22, `R7 install karti yeni yuzey ailesinde (${state.cardRadius}px)`);
    check(state.overflow <= 1, `R7 install yatay tasma yok (${state.overflow}px)`);
    await install.close();
  } finally {
    if (hadLock && !fs.existsSync(lock)) fs.writeFileSync(lock, 'installed\n');
  }
}

await ctx.close();
await browser.close();
console.log(fail === 0 ? '\nTUM R7 PANEL VE SISTEM DENETIMLERI GECTI' : `\n${fail} R7 DENETIMI KALDI`);
process.exit(fail === 0 ? 0 : 1);
