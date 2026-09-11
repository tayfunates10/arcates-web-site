import fs from 'node:fs';

const files = process.argv.slice(2);
if (files.length === 0) {
  console.error('Kullanim: node tools/browser/lighthouse-gate.mjs <report.json> [...]');
  process.exit(2);
}

const thresholds = {
  performance: 90,
  accessibility: 95,
  'best-practices': 95,
  seo: 100,
};

const percent = (value) => Math.round((Number(value) || 0) * 100);
const ms = (value) => Number.isFinite(Number(value)) ? Math.round(Number(value)) : null;
const decimal = (value, digits = 3) => Number.isFinite(Number(value)) ? Number(value).toFixed(digits) : null;

let failed = false;
const summary = [];

for (const file of files) {
  const report = JSON.parse(fs.readFileSync(file, 'utf8'));
  const label = file.toLowerCase().includes('desktop') ? 'desktop' : file.toLowerCase().includes('mobile') ? 'mobile' : file;
  const categories = report.categories || {};
  const audits = report.audits || {};

  const scores = Object.fromEntries(
    Object.keys(thresholds).map((key) => [key, percent(categories[key]?.score)])
  );

  const metrics = {
    fcpMs: ms(audits['first-contentful-paint']?.numericValue),
    lcpMs: ms(audits['largest-contentful-paint']?.numericValue),
    tbtMs: ms(audits['total-blocking-time']?.numericValue),
    cls: decimal(audits['cumulative-layout-shift']?.numericValue),
    speedIndexMs: ms(audits['speed-index']?.numericValue),
  };

  console.log(`\n[${label}]`);
  console.log(`Performance: ${scores.performance}`);
  console.log(`Accessibility: ${scores.accessibility}`);
  console.log(`Best Practices: ${scores['best-practices']}`);
  console.log(`SEO: ${scores.seo}`);
  console.log(`FCP: ${metrics.fcpMs ?? 'n/a'} ms`);
  console.log(`LCP: ${metrics.lcpMs ?? 'n/a'} ms`);
  console.log(`TBT: ${metrics.tbtMs ?? 'n/a'} ms`);
  console.log(`CLS: ${metrics.cls ?? 'n/a'}`);
  console.log(`Speed Index: ${metrics.speedIndexMs ?? 'n/a'} ms`);

  const failures = [];
  for (const [key, minimum] of Object.entries(thresholds)) {
    if (scores[key] < minimum) {
      failures.push(`${key} ${scores[key]} < ${minimum}`);
    }
  }

  if (failures.length) {
    failed = true;
    console.error(`KALDI: ${failures.join(', ')}`);
  } else {
    console.log('GECTI: Lighthouse kategori hedefleri karsilandi.');
  }

  summary.push({ label, scores, metrics, failures });
}

fs.writeFileSync('lighthouse-summary.json', JSON.stringify({ thresholds, summary }, null, 2));
console.log(failed ? '\nLIGHTHOUSE PRODUCTION KAPISI KALDI' : '\nTUM LIGHTHOUSE PRODUCTION KAPILARI GECTI');
process.exit(failed ? 1 : 0);
