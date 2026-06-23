import { chromium } from 'playwright';

const browser = await chromium.launch({
  headless: true
});

const page = await browser.newPage();

await page.goto(
  'https://ytm.tm.com.my/scholarships-future-leader',
  {
    waitUntil: 'networkidle'
  }
);

const text = await page.evaluate(() =>
  document.body.innerText
);

console.log(text);

await browser.close();