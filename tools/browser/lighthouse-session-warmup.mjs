import puppeteer from 'puppeteer-core';

const baseUrl = process.argv[2];
const browserUrl = process.argv[3] || 'http://127.0.0.1:9222';

if (!baseUrl) {
  console.error('Kullanim: node lighthouse-session-warmup.mjs <base-url> [browser-url]');
  process.exit(2);
}

const sleep = (ms) => new Promise((resolve) => setTimeout(resolve, ms));

const browser = await puppeteer.connect({ browserURL: browserUrl });
const pages = await browser.pages();
const page = pages[0] ?? await browser.newPage();

await page.goto(baseUrl, { waitUntil: 'domcontentloaded', timeout: 45_000 });

let settled = false;
let snapshot = null;
for (let attempt = 0; attempt < 20; attempt += 1) {
  await sleep(750);
  snapshot = await page.evaluate(() => ({
    url: location.href,
    title: document.title,
    text: (document.body?.innerText || '').slice(0, 1200),
  }));

  const looksLikeChallenge = /wsidchk|imunify|anti[- ]?bot|checking your browser|javascript challenge/i.test(
    `${snapshot.url}\n${snapshot.title}\n${snapshot.text}`,
  );

  if (!looksLikeChallenge && new URL(snapshot.url).origin === new URL(baseUrl).origin) {
    settled = true;
    break;
  }
}

const cookies = await page.cookies(baseUrl);
const cookieNames = cookies.map(({ name }) => name);

console.log(JSON.stringify({
  requestedUrl: baseUrl,
  finalUrl: snapshot?.url ?? page.url(),
  title: snapshot?.title ?? '',
  settled,
  cookieNames,
}, null, 2));

if (!settled) {
  console.error('Tarayici oturumu challenge sonrasinda production sayfasina yerlesemedi.');
  await browser.disconnect();
  process.exit(1);
}

await browser.disconnect();
