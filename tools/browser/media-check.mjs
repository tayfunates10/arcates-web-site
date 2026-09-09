/* Arcates R3 G-06/G-07 media acceptance — real Chromium. */
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

// Seed proje kayitlari gercek medya tasimaz: ekran goruntusu varmis gibi cerceve uretilmemeli.
{
  const ctx = await browser.newContext({ viewport: { width: 1280, height: 900 } });
  const page = await ctx.newPage();
  await page.goto(BASE + '/referanslar', { waitUntil: 'networkidle' });
  const list = await page.evaluate(() => ({
    cards: document.querySelectorAll('.project-card').length,
    shots: document.querySelectorAll('.project-card .project-shot').length,
    style: Array.from(document.styleSheets).some(s => String(s.href || '').includes('r3-project-blog-media.css')),
    overflow: document.documentElement.scrollWidth - innerWidth,
  }));
  check(list.cards >= 4, `G-06 proje kartlari mevcut (${list.cards})`);
  check(list.shots === 0, `G-06 medyasiz orneklerde sahte project-shot yok (${list.shots})`);
  check(list.style, 'G-06 proje medya stili yuklendi');
  check(list.overflow <= 1, `G-06 proje listesi yatay tasma yok (${list.overflow}px)`);

  await page.goto(BASE + '/referanslar/akcay-pansiyon-rezervasyon-sitesi', { waitUntil: 'networkidle' });
  const detail = await page.evaluate(() => ({
    shots: document.querySelectorAll('.project-shot--detail').length,
    gallery: document.querySelectorAll('.project-gallery__frame').length,
    overflow: document.documentElement.scrollWidth - innerWidth,
  }));
  check(detail.shots === 0, `G-06 medyasiz proje detayinda sahte kapak cercevesi yok (${detail.shots})`);
  check(detail.gallery === 0, `G-06 medyasiz proje detayinda sahte galeri yok (${detail.gallery})`);
  check(detail.overflow <= 1, `G-06 proje detayi yatay tasma yok (${detail.overflow}px)`);
  await ctx.close();
}

// Seed blog kapaklari bilerek bostur: metinsiz editoryal fallback her kartta gorunmeli.
{
  const ctx = await browser.newContext({ viewport: { width: 1280, height: 900 } });
  const page = await ctx.newPage();
  await page.goto(BASE + '/blog', { waitUntil: 'networkidle' });
  const list = await page.evaluate(() => {
    const cards = Array.from(document.querySelectorAll('.post-card'));
    const covers = Array.from(document.querySelectorAll('.post-card .editorial-cover'));
    return {
      cards: cards.length,
      covers: covers.length,
      emptyText: covers.every(el => (el.textContent || '').trim() === ''),
      hidden: covers.every(el => el.getAttribute('aria-hidden') === 'true'),
      ratio: covers.length ? covers[0].getBoundingClientRect().width / covers[0].getBoundingClientRect().height : 0,
      style: Array.from(document.styleSheets).some(s => String(s.href || '').includes('r3-project-blog-media.css')),
      overflow: document.documentElement.scrollWidth - innerWidth,
    };
  });
  check(list.cards >= 6, `G-07 blog kartlari mevcut (${list.cards})`);
  check(list.covers === list.cards, `G-07 kapaksiz seed yazilarinda fallback mevcut (${list.covers}/${list.cards})`);
  check(list.emptyText, 'G-07 fallback gorsellerine metin gomulmedi');
  check(list.hidden, 'G-07 fallback gorselleri dekoratif aria-hidden');
  check(list.ratio > 1.55 && list.ratio < 1.65, `G-07 fallback 16:10 oraninda (${list.ratio.toFixed(2)})`);
  check(list.style, 'G-07 blog medya stili yuklendi');
  check(list.overflow <= 1, `G-07 blog listesi yatay tasma yok (${list.overflow}px)`);

  await page.goto(BASE + '/blog/yerel-aramada-gorunurluk-isletme-profili', { waitUntil: 'networkidle' });
  const detail = await page.evaluate(() => {
    const cover = document.querySelector('.article-cover-fallback');
    const rect = cover?.getBoundingClientRect();
    return {
      cover: !!cover,
      text: (cover?.textContent || '').trim(),
      ratio: rect && rect.height ? rect.width / rect.height : 0,
      overflow: document.documentElement.scrollWidth - innerWidth,
    };
  });
  check(detail.cover, 'G-07 blog detay fallback kapagi mevcut');
  check(detail.text === '', 'G-07 blog detay fallback kapagi metinsiz');
  check(detail.ratio > 1.55 && detail.ratio < 1.65, `G-07 blog detay kapagi 16:10 (${detail.ratio.toFixed(2)})`);
  check(detail.overflow <= 1, `G-07 blog detayi yatay tasma yok (${detail.overflow}px)`);
  await ctx.close();
}

// Dar mobilde yeni medya yuzeyleri belgeyi genisletmemeli.
{
  const ctx = await browser.newContext({ viewport: { width: 360, height: 780 } });
  const page = await ctx.newPage();
  for (const route of ['/blog', '/blog/yerel-aramada-gorunurluk-isletme-profili', '/referanslar']) {
    await page.goto(BASE + route, { waitUntil: 'networkidle' });
    const overflow = await page.evaluate(() => document.documentElement.scrollWidth - innerWidth);
    check(overflow <= 1, `G-06/G-07 mobil ${route} yatay tasma yok (${overflow}px)`);
  }
  await ctx.close();
}

await browser.close();
console.log(fail === 0 ? '\nTUM G-06/G-07 MEDYA DENETIMLERI GECTI' : `\n${fail} G-06/G-07 DENETIMI KALDI`);
process.exit(fail === 0 ? 0 : 1);
