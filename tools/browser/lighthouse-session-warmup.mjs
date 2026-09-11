import puppeteer from 'puppeteer-core';

const baseUrl = process.argv[2];
const browserUrl = process.argv[3] || 'http://127.0.0.1:9222';

if (!baseUrl) {
  console.error('Kullanim: node lighthouse-session-warmup.mjs <base-url> [browser-url]');
  process.exit(2);
}

const sleep = (ms) => new Promise((resolve) => setTimeout(resolve, ms));
const challengePattern = /wsidchk|imunify|anti[- ]?bot|checking your browser|javascript challenge/i;

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

  const looksLikeChallenge = challengePattern.test(
    `${snapshot.url}\n${snapshot.title}\n${snapshot.text}`,
  );

  if (!looksLikeChallenge && new URL(snapshot.url).origin === new URL(baseUrl).origin) {
    settled = true;
    break;
  }
}

let robots = { ok: false, status: 0, text: '' };
if (settled) {
  robots = await page.evaluate(async () => {
    const response = await fetch('/robots.txt', { credentials: 'include', cache: 'no-store' });
    return {
      ok: response.ok,
      status: response.status,
      text: (await response.text()).slice(0, 2000),
    };
  });
}

const robotsIsReal = robots.ok
  && /user-agent\s*:/i.test(robots.text)
  && !challengePattern.test(robots.text);

const cookies = await page.cookies(baseUrl);
const cookieNames = cookies.map(({ name }) => name);

console.log(JSON.stringify({
  requestedUrl: baseUrl,
  finalUrl: snapshot?.url ?? page.url(),
  title: snapshot?.title ?? '',
  settled,
  cookieNames,
  robots: {
    status: robots.status,
    verified: robotsIsReal,
    preview: robots.text.slice(0, 240),
  },
}, null, 2));

if (!settled || !robotsIsReal) {
  console.error('Challenge sonrasi production oturumu veya robots.txt dogrulamasi basarisiz.');
  await browser.disconnect();
  process.exit(1);
}

await browser.disconnect();
