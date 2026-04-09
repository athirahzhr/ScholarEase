// scrapers/bpmb.js

import { chromium } from 'playwright';
import fs from 'fs';
import path from 'path';
import mysql from 'mysql2/promise';
import { parseRules } from './utils/ruleParser.js';

// ===== DB connection (same pattern as BNM) =====
const db = await mysql.createConnection({
  host: '127.0.0.1',
  user: 'root',
  password: '',
  database: 'scholarease_db'
});

const PROGRAM = {
  title: 'BPMB Award of Group Undergraduate Scholarship (BAGUS)',
  provider: 'Bank Pembangunan Malaysia Berhad',
  url: 'https://www.bpmb.com.my/scholarship/'
};

(async () => {
  console.log('🚀 Scraping BPMB – BAGUS Scholarship');

  const startTime = new Date();
  let success = 0;
  let failed = 0;
  const results = [];

  const browser = await chromium.launch({ headless: true });
  const page = await browser.newPage();

  try {
    await page.goto(PROGRAM.url, {
      waitUntil: 'domcontentloaded',
      timeout: 60000
    });

    // bagi page betul-betul render
    await page.waitForTimeout(4000);

    const rawText = await page.evaluate(() => {
      const main =
        document.querySelector('main') ||
        document.querySelector('.container') ||
        document.body;

      return main ? main.innerText : '';
    });

    if (!rawText || rawText.length < 300) {
      throw new Error('No usable text extracted');
    }

    const rules = parseRules(rawText);

    results.push({
      title: PROGRAM.title,
      provider: PROGRAM.provider,
      description: PROGRAM.title,
      raw_eligibility: rawText,
      application_link: PROGRAM.url,
      application_deadline: null,
      source: 'scraped',
      source_website: 'bpmb',
      rules,
      scraped_at: new Date().toISOString()
    });

    success++;
    console.log('✅ BPMB parsed successfully');

  } catch (err) {
    failed++;
    console.error('❌ BPMB scrape failed:', err.message);
  }

  await browser.close();

  // ===== Save JSON =====
  const outputDir = path.resolve('scrapers/output');
  fs.mkdirSync(outputDir, { recursive: true });

  const outputPath = path.join(outputDir, 'bpmb.json');
  fs.writeFileSync(outputPath, JSON.stringify(results, null, 2));

  console.log(`📁 BPMB output saved → ${outputPath}`);

  // ===== Scraping log =====
  const status =
    failed === 0 ? 'success' :
    success === 0 ? 'failed' : 'partial';

  await db.execute(
    `INSERT INTO scraping_logs
     (source_website, total_scraped, success_count, failed_count, status, started_at, finished_at)
     VALUES (?, ?, ?, ?, ?, ?, ?)`,
    [
      'bpmb',
      1,
      success,
      failed,
      status,
      startTime,
      new Date()
    ]
  );

  await db.end();

  console.log('🎉 BPMB scraping completed');
})();
