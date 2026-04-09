// scrapers/petronas.js

import { chromium } from 'playwright';
import fs from 'fs';
import path from 'path';
import mysql from 'mysql2/promise';
import { parseRules } from './utils/ruleParser.js';

// DB untuk scraping_logs
const db = await mysql.createConnection({
  host: '127.0.0.1',
  user: 'root',
  password: '',
  database: 'scholarease_db'
});

(async () => {
  console.log('🚀 Scraping PETRONAS');

  const startTime = new Date();
  let success = 0;
  let failed = 0;
  const results = [];

  const browser = await chromium.launch({ headless: true });
  const page = await browser.newPage();

  try {
    await page.goto(
      'https://www.petronas.com/careers/students-graduates',
      {
        waitUntil: 'domcontentloaded',
        timeout: 60000
      }
    );

    await page.waitForTimeout(5000);

    const rawText = await page.evaluate(() => document.body.innerText);

    if (!rawText || rawText.length < 300) {
      throw new Error('No usable text found');
    }

    results.push({
      title: 'PETRONAS Education Sponsorship (SPM Leavers)',
      provider: 'PETRONAS',
      application_link: page.url(),
      application_deadline: null,
      source: 'scraped',
      source_website: 'petronas',
      raw_eligibility: rawText.slice(0, 6000),
      rules: parseRules(rawText),
      scraped_at: new Date().toISOString()
    });

    success++;
    console.log('✅ Parsed: PETRONAS Education Sponsorship');

  } catch (err) {
    failed++;
    console.error('❌ PETRONAS failed:', err.message);
  }

  await browser.close();

  // Save JSON
  const outputPath = path.resolve('scrapers/output/petronas.json');
  fs.mkdirSync(path.dirname(outputPath), { recursive: true });
  fs.writeFileSync(outputPath, JSON.stringify(results, null, 2));

  // Scraping log
  const status =
    failed === 0 ? 'success' :
    success === 0 ? 'failed' : 'partial';

  await db.execute(
    `INSERT INTO scraping_logs
     (source_website, total_scraped, success_count, failed_count, status, started_at, finished_at)
     VALUES (?, ?, ?, ?, ?, ?, ?)`,
    [
      'petronas',
      1,
      success,
      failed,
      status,
      startTime,
      new Date()
    ]
  );

  await db.end();

  console.log('🎉 PETRONAS scraping completed with log');
})();
