/* Arcates admin drawer visual regression check in Android-like Chromium. */
import fs from 'node:fs';
import path from 'node:path';

let chromium;
try {
  ({ chromium } = await import(process.env.PLAYWRIGHT_PATH || 'playwright'));
} catch (error) {
  console.error('Playwright bulunamadi.');
  console.error(String(error.message || error));
  process.exit(2);
}

const launchOptions = process.env.CHROMIUM_PATH ? { executablePath: process.env.CHROMIUM_PATH } : {};
const browser = await chromium.launch(launchOptions);
const context = await browser.newContext({
  viewport: { width: 390, height: 844 },
  screen: { width: 390, height: 844 },
  deviceScaleFactor: 3,
  isMobile: true,
  hasTouch: true,
});
const page = await context.newPage();
let fail = 0;
const check = (ok, msg) => { console.log((ok ? 'GECTI ' : 'KALDI ') + msg); if (!ok) fail++; };

const labels = [
  'Pano', 'Anasayfa', 'Sayfalar', 'Örnek siteler', 'Blog', 'SSS', 'Medya', 'Menüler',
  'SEO', 'Yönlendirmeler', 'Formlar', 'İstatistik', 'Kullanıcılar', 'Ayarlar', 'Yedekleme', 'İşlem günlüğü',
];
const nav = labels.map((label, index) =>
  `<li><a href="#" class="admin__nav-link${index === 0 ? ' is-current' : ''}">${label}</a></li>`
).join('');
const logoBytes = fs.readFileSync(path.resolve('public/assets/img/logo-wordmark.png'));
const logoSrc = 'data:image/png;base64,' + logoBytes.toString('base64');

await page.setContent(`<!doctype html><html lang="tr"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"></head>
<body class="admin admin--r7 admin-nav-open">
<div class="admin__shell">
  <aside class="admin__side is-open" id="admin-side" data-admin-side>
    <a class="admin__brand" href="#"><img class="admin__logo" src="${logoSrc}" alt="Arcates Yazılım" width="158" height="53"></a>
    <nav class="admin__nav" aria-label="Panel menusu"><ul>${nav}</ul></nav>
    <div class="admin__side-foot"><a class="admin__side-link" href="#">Siteyi gör</a></div>
  </aside>
  <div class="admin__nav-backdrop" aria-hidden="true"></div>
  <div class="admin__main"><header class="admin__top"><h1 class="admin__title">Pano</h1></header><main class="admin__content"><div class="card card--stat"><span class="card__label">Örnek</span><strong class="card__value">0</strong></div></main></div>
</div></body></html>`);

for (const css of ['admin.css', 'admin-redesign.css', 'admin-r7.css', 'admin-r7-responsive.css']) {
  await page.addStyleTag({ path: path.resolve('public/assets/css', css) });
}
await page.waitForTimeout(250);

const state = await page.evaluate(() => {
  const side = document.querySelector('.admin__side');
  const brand = document.querySelector('.admin__brand');
  const logo = document.querySelector('.admin__logo');
  const nav = document.querySelector('.admin__nav');
  const links = [...document.querySelectorAll('.admin__nav-link')];
  const foot = document.querySelector('.admin__side-foot');
  const sideRect = side.getBoundingClientRect();
  const brandRect = brand.getBoundingClientRect();
  const logoRect = logo.getBoundingClientRect();
  const navRect = nav.getBoundingClientRect();
  const footRect = foot.getBoundingClientRect();
  const firstRect = links[0].getBoundingClientRect();
  const sideStyle = getComputedStyle(side);
  const navStyle = getComputedStyle(nav);
  const visibleTopLinks = links.slice(0, 5).filter(link => {
    const r = link.getBoundingClientRect();
    return r.top >= navRect.top - 1 && r.bottom <= Math.min(navRect.bottom, innerHeight) + 1;
  }).length;
  return {
    width: sideRect.width,
    top: sideRect.top,
    bottom: sideRect.bottom,
    brandTop: brandRect.top,
    brandBottom: brandRect.bottom,
    logoTop: logoRect.top,
    logoWidth: logoRect.width,
    navTop: navRect.top,
    navBottom: navRect.bottom,
    firstNavTop: firstRect.top,
    footBottom: footRect.bottom,
    navCount: links.length,
    firstLabels: links.slice(0, 5).map(link => link.textContent.trim()),
    visibleTopLinks,
    sideDisplay: sideStyle.display,
    sideOverflowY: sideStyle.overflowY,
    navOverflowY: navStyle.overflowY,
    navScrollTop: nav.scrollTop,
    innerWidth,
    innerHeight,
    visualWidth: visualViewport ? visualViewport.width : innerWidth,
    visualHeight: visualViewport ? visualViewport.height : innerHeight,
  };
});

console.log('ANDROID_DRAWER_STATE ' + JSON.stringify(state));
check(state.innerWidth === 390, `layout viewport 390px (${state.innerWidth})`);
check(state.visualWidth >= 389 && state.visualWidth <= 391, `visual viewport 390px (${state.visualWidth})`);
check(state.width >= 240 && state.width <= 273, `drawer kompakt (${state.width}px)`);
check(Math.abs(state.top) <= 1 && state.bottom >= state.innerHeight - 1, `drawer viewporta sabit (${state.top}..${state.bottom})`);
check(state.sideDisplay === 'grid', `drawer grid yerlesiminde (${state.sideDisplay})`);
check(state.brandTop >= 0 && state.brandTop < 28, `marka ustte (${state.brandTop}px)`);
check(state.logoTop >= 0 && state.logoTop < 36, `logo ustte gorunur (${state.logoTop}px)`);
check(state.logoWidth >= 120 && state.logoWidth <= 150, `logo mobil olcekte (${state.logoWidth}px)`);
check(state.navTop >= state.brandBottom - 1 && state.navTop < 90, `menu markanin hemen altinda (${state.navTop}px)`);
check(state.firstNavTop >= state.navTop && state.firstNavTop < 125, `ilk menu ogesi ilk ekranda (${state.firstNavTop}px)`);
check(state.navCount === 16, `menu ogeleri eksiksiz (${state.navCount})`);
check(state.firstLabels.join('|') === labels.slice(0, 5).join('|'), `ilk menu etiketleri dogru (${state.firstLabels.join(', ')})`);
check(state.visibleTopLinks === 5, `ilk bes menu ogesi acilista gorunur (${state.visibleTopLinks}/5)`);
check(state.sideOverflowY === 'hidden', `drawer govdesi kaymiyor (${state.sideOverflowY})`);
check(state.navOverflowY === 'auto' || state.navOverflowY === 'scroll', `yalniz menu kaydiriliyor (${state.navOverflowY})`);
check(state.navScrollTop === 0, `menu acilista en ustte (${state.navScrollTop})`);
check(state.footBottom <= state.innerHeight + 1, `alt baglanti viewport icinde (${state.footBottom}px)`);

await page.screenshot({ path: '/tmp/admin-mobile-drawer-verified.png', fullPage: false });
await context.close();
await browser.close();
console.log(fail === 0 ? '\nANDROID MOBIL DRAWER GORSEL DENETIMI GECTI' : `\n${fail} ANDROID MOBIL DRAWER DENETIMI KALDI`);
process.exit(fail === 0 ? 0 : 1);
