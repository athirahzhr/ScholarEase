// scrapers/bnm.js

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

const programs = [
  {
    title: 'Kijang Pre-University Scholarship',
    url: 'https://www.bnm.gov.my/careers/scholarships'
  },
  {
    title: 'Kijang Undergraduate Scholarship',
    url: 'https://www.bnm.gov.my/careers/scholarships'
  }
];

(async () => {
  const startTime = new Date();
  let success = 0;
  let failed = 0;
  const results = [];

  const browser = await chromium.launch({ headless: true });
  const page = await browser.newPage();

  for (const program of programs) {
    try {
      console.log(`🔍 Scraping: ${program.title}`);

      await page.goto(program.url, {
        waitUntil: 'domcontentloaded',
        timeout: 60000
      });

      // tunggu tab render
      await page.waitForTimeout(2500);

      const rawText = await page.evaluate(() => {
        const main = document.querySelector('main') || document.body;
        return main.innerText;
      });

      const rules = parseRules(rawText);

      results.push({
        title: program.title,
        provider: 'Bank Negara Malaysia',
        application_link: program.url,
        application_deadline: null,
        raw_eligibility: rawText,
        rules,
        source: 'scraped',
        source_website: 'bnm',
        scraped_at: new Date().toISOString()
      });

      success++;
      console.log(`✅ Parsed: ${program.title}`);

    } catch (err) {
      failed++;
      console.error(`❌ Failed: ${program.title}`, err.message);
    }
  }

  await browser.close();

  // Save JSON
  const outputPath = path.resolve('scrapers/output/bnm.json');
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
      'bnm',
      programs.length,
      success,
      failed,
      status,
      startTime,
      new Date()
    ]
  );

  await db.end();

  console.log('🎉 Bank Negara Malaysia scraping completed');
})();
