import puppeteer from 'puppeteer-core';

const baseUrl = process.argv[2];
const browserUrl = process.argv[3] || 'http://127.0.0.1:9222';

if (!baseUrl) {
  console.error('Kullanim: node lighthouse-session-warmup.mjs <base-url> [browser-url]');
  process.exit(2);
}

const sleep = (ms) => new Promise((resolve) => setTimeout(resolve, ms));
const challengePattern = /wsidchk|imunify|anti[- ]?bot|checking your browser|javascript challenge|one moment,? please|please wait|enable javascript/i;
const base = new URL(baseUrl);
const robotsUrl = new URL('/robots.txt', base).href;
const maxWaitMs = 60_000;
const pollMs = 1_000;

const browser = await puppeteer.connect({ browserURL: browserUrl });
const pages = await browser.pages();
const page = pages[0] ?? await browser.newPage();

async function snapshotPage() {
  try {
    return await page.evaluate(() => ({
      url: location.href,
      title: document.title,
      text: (document.body?.innerText || '').slice(0, 2000),
    }));
  } catch {
    return { url: page.url(), title: '', text: '' };
  }
}

function isChallenge(snapshot) {
  return challengePattern.test(`${snapshot.url}\n${snapshot.title}\n${snapshot.text}`);
}

async function waitForRealPage(url, validator, label) {
  const startedAt = Date.now();
  let last = null;

  try {
    await page.goto(url, { waitUntil: 'domcontentloaded', timeout: 45_000 });
  } catch (error) {
    console.warn(`${label}: ilk navigasyon tamamlanamadi: ${error.message}`);
  }

  while (Date.now() - startedAt < maxWaitMs) {
    await sleep(pollMs);
    last = await snapshotPage();

    if (!isChallenge(last) && validator(last)) {
      return { ok: true, snapshot: last, waitedMs: Date.now() - startedAt };
    }

    const elapsed = Date.now() - startedAt;
    if (elapsed > 0 && elapsed % 10_000 < pollMs) {
      try {
        await page.reload({ waitUntil: 'domcontentloaded', timeout: 30_000 });
      } catch {
        // Challenge kendi reload akisini da kullanir; navigasyon yarisi hatalari kritik degil.
      }
    }
  }

  return { ok: false, snapshot: last ?? await snapshotPage(), waitedMs: Date.now() - startedAt };
}

const home = await waitForRealPage(
  base.href,
  (snapshot) => {
    try {
      return new URL(snapshot.url).origin === base.origin
        && snapshot.text.trim().length > 40;
    } catch {
      return false;
    }
  },
  'anasayfa',
);

const robots = home.ok
  ? await waitForRealPage(
      robotsUrl,
      (snapshot) => /user-agent\s*:/i.test(snapshot.text) && /sitemap\s*:/i.test(snapshot.text),
      'robots.txt',
    )
  : { ok: false, snapshot: { url: '', title: '', text: '' }, waitedMs: 0 };

const homeAgain = robots.ok
  ? await waitForRealPage(
      base.href,
      (snapshot) => {
        try {
          return new URL(snapshot.url).origin === base.origin
            && snapshot.text.trim().length > 40;
        } catch {
          return false;
        }
      },
      'anasayfa-son-kontrol',
    )
  : { ok: false, snapshot: { url: '', title: '', text: '' }, waitedMs: 0 };

const cookies = await page.cookies(base.href);
const cookieNames = cookies.map(({ name }) => name);

console.log(JSON.stringify({
  requestedUrl: base.href,
  home: {
    verified: home.ok,
    finalUrl: home.snapshot?.url ?? '',
    title: home.snapshot?.title ?? '',
    waitedMs: home.waitedMs,
  },
  robots: {
    verified: robots.ok,
    finalUrl: robots.snapshot?.url ?? '',
    waitedMs: robots.waitedMs,
    preview: (robots.snapshot?.text ?? '').slice(0, 240),
  },
  homeAgain: {
    verified: homeAgain.ok,
    finalUrl: homeAgain.snapshot?.url ?? '',
    title: homeAgain.snapshot?.title ?? '',
    waitedMs: homeAgain.waitedMs,
  },
  cookieNames,
}, null, 2));

if (!home.ok || !robots.ok || !homeAgain.ok) {
  console.error('Challenge sonrasi production oturumu veya robots.txt dogrulamasi basarisiz.');
  await browser.disconnect();
  process.exit(1);
}

await browser.disconnect();
