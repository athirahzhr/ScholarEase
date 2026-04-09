// scrapers/mara.js
import { chromium } from 'playwright';
import fs from 'fs';
import path from 'path';
import mysql from 'mysql2/promise';
import { parseRules } from './utils/ruleParser.js';

const db = await mysql.createConnection({
  host: '127.0.0.1',
  user: 'root',
  password: '',
  database: 'scholarease_db'
});

(async () => {
  console.log('🚀 Scraping MARA – Young Talent Development Program');

  const startTime = new Date();
  let success = 0;
  let failed = 0;

  const browser = await chromium.launch({ headless: true });
  const page = await browser.newPage();

  const url =
    'https://www.mara.gov.my/en/index/education/education-financing/peringkat-persediaan-2/';

  try {
    await page.goto(url, {
      waitUntil: 'networkidle',
      timeout: 60000
    });

    // ❗ BERI MASA JS settle
    await page.waitForTimeout(4000);

    const rawText = await page.evaluate(() => {
      // Ambil SEMUA text, bukan rely on visibility
      return document.body.innerText;
    });

    console.log('📏 Extracted text length:', rawText.length);

    if (!rawText || rawText.length < 800) {
      throw new Error('Extracted text too short');
    }

    const data = [
      {
        title: 'MARA Young Talent Development Program (YTP)',
        provider: 'Majlis Amanah Rakyat (MARA)',
        application_link: url,
        application_deadline: null,
        raw_eligibility: rawText.slice(0, 9000),
        rules: parseRules(rawText),
        source: 'scraped',
        source_website: 'mara',
        scraped_at: new Date().toISOString()
      }
    ];

    // Save JSON
    const outputDir = path.resolve('scrapers/output');
    fs.mkdirSync(outputDir, { recursive: true });

    fs.writeFileSync(
      path.join(outputDir, 'mara.json'),
      JSON.stringify(data, null, 2)
    );

    success++;
    console.log('✅ MARA YTP parsed & saved');

  } catch (err) {
    failed++;
    console.error('❌ MARA scrape failed:', err.message);
  }

  await browser.close();

  const status =
    failed === 0 ? 'success' :
    success === 0 ? 'failed' : 'partial';

  await db.execute(
    `INSERT INTO scraping_logs
     (source_website, total_scraped, success_count, failed_count, status, started_at, finished_at)
     VALUES (?, ?, ?, ?, ?, ?, ?)`,
    [
      'mara',
      1,
      success,
      failed,
      status,
      startTime,
      new Date()
    ]
  );

  await db.end();

  console.log('🎉 MARA scraping completed');
})();
