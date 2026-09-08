/* Arcates v1.1 tasarim kabul denetimleri — gercek Chromium. */
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

// F-T3-b + E-08: ilce sayfasi responsive siralama ve TOC.
{
  const ctx = await browser.newContext({ viewport: { width: 900, height: 800 } });
  const page = await ctx.newPage();
  await page.goto(BASE + '/edremit-web-tasarim', { waitUntil: 'networkidle' });

  const order = await page.evaluate(() => {
    const aside = document.querySelector('.content-aside');
    const main = document.querySelector('.content-main');
    const links = [...document.querySelectorAll('[data-toc-link]')];
    return {
      aside: aside ? aside.getBoundingClientRect().top : null,
      main: main ? main.getBoundingClientRect().top : null,
      links: links.length,
    };
  });
  check(order.aside !== null && order.main !== null && order.aside < order.main,
    `F-T3-b 940 altinda yan sutun once geliyor (aside=${order.aside}, main=${order.main})`);
  check(order.links >= 2, `E-08 TOC baglantilari mevcut (${order.links})`);

  const firstHref = await page.getAttribute('[data-toc-link]', 'href');
  await page.focus('[data-toc-link]');
  const focused = await page.evaluate(() => document.activeElement?.hasAttribute('data-toc-link'));
  check(focused === true, 'E-08 TOC klavye ile odaklanabiliyor');
  await page.keyboard.press('Enter');
  await page.waitForTimeout(250);
  check((await page.evaluate(() => location.hash)) === firstHref, 'E-08 Enter ile hedef basliga gidiliyor');

  const current = await page.getAttribute('[data-toc-link][aria-current]', 'aria-current');
  check(current === 'location', `E-08 aktif TOC baglantisi aria-current tasiyor (${current})`);
  await ctx.close();
}

// F-T4-a: form masaustu ve mobil ilk ekranda.
for (const viewport of [{ width: 1280, height: 900 }, { width: 390, height: 844 }]) {
  const ctx = await browser.newContext({ viewport });
  const page = await ctx.newPage();
  await page.goto(BASE + '/iletisim', { waitUntil: 'networkidle' });
  const state = await page.evaluate(() => {
    const form = document.querySelector('#teklif-formu form');
    const copy = document.querySelector('.contact-copy');
    const button = form?.querySelector('button[type="submit"]');
    return {
      formTop: form ? form.getBoundingClientRect().top : null,
      copyTop: copy ? copy.getBoundingClientRect().top : null,
      viewport: innerHeight,
      disabled: button ? button.disabled : null,
    };
  });
  check(state.formTop !== null && state.formTop < state.viewport,
    `F-T4-a ${viewport.width} genislikte form ilk ekranda (top=${state.formTop}, h=${state.viewport})`);
  if (viewport.width < 940) {
    check(state.copyTop !== null && state.formTop < state.copyTop, 'F-T4-a mobilde form metinden once');
  }
  check(state.disabled === true, 'F-T4 form gecerli olana kadar gonder butonu kilitli');
  await ctx.close();
}

// A-11: sticky kart scroll boyunca layout animasyonu yapmadan akici kalir.
{
  const ctx = await browser.newContext({ viewport: { width: 1280, height: 900 } });
  const page = await ctx.newPage();
  await page.goto(BASE + '/edremit-web-tasarim', { waitUntil: 'networkidle' });
  const position = await page.evaluate(() => getComputedStyle(document.querySelector('[data-sticky-cta]')).position);
  check(position === 'sticky', `A-11 masaustunde teklif karti sticky (${position})`);

  const fps = await page.evaluate(() => new Promise(resolve => {
    const times = [];
    let y = 0;
    const max = Math.max(1, document.documentElement.scrollHeight - innerHeight);
    function frame(t) {
      times.push(t);
      y = Math.min(max, y + max / 90);
      scrollTo(0, y);
      if (times.length < 90) requestAnimationFrame(frame);
      else {
        const span = times[times.length - 1] - times[0];
        resolve(span > 0 ? ((times.length - 1) * 1000) / span : 60);
      }
    }
    requestAnimationFrame(frame);
  }));
  check(fps >= 50, `A-11 sticky scroll akiciligi hedefe yakin (${fps.toFixed(1)} fps)`);
  await ctx.close();
}

await browser.close();
console.log(fail === 0 ? '\nTUM TASARIM TARAYICI DENETIMLERI GECTI' : `\n${fail} TASARIM DENETIMI KALDI`);
process.exit(fail === 0 ? 0 : 1);
