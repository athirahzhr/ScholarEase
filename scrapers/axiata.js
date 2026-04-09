// scrapers/axiata.js

import { chromium } from 'playwright';
import fs from 'fs';
import path from 'path';
import mysql from 'mysql2/promise';
import { parseRules } from './utils/ruleParser.js';

// ================= DB CONNECTION =================
const db = await mysql.createConnection({
  host: '127.0.0.1',
  user: 'root',
  password: '',
  database: 'scholarease_db'
});

// ================= PROGRAM CONFIG =================
const programs = [
  {
    title: 'Axiata Equity in Education Fund',
    provider: 'Axiata Foundation',
    url: 'https://www.axiata-foundation.com/education/equity-in-education-fund/application'
  }
];

(async () => {
  console.log('🚀 Scraping AXIATA Foundation');

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

      // bagi content betul-betul render
      await page.waitForTimeout(3000);

      // extract text (safe selector)
      const rawText = await page.evaluate(() => {
        const main =
          document.querySelector('main') ||
          document.querySelector('article') ||
          document.body;

        return main.innerText;
      });

      if (!rawText || rawText.length < 300) {
        throw new Error('No usable text extracted');
      }

      const rules = parseRules(rawText);

      results.push({
        title: program.title,
        provider: program.provider,
        description: program.title,
        raw_eligibility: rawText.slice(0, 8000),
        application_link: program.url,
        application_deadline: null,
        source: 'scraped',
        source_website: 'axiata',
        is_official: 1,
        is_active: 1,
        scraped_at: new Date().toISOString(),
        rules
      });

      success++;
      console.log(`✅ Parsed: ${program.title}`);

    } catch (err) {
      failed++;
      console.error(`❌ Failed: ${program.title} → ${err.message}`);
    }
  }

  await browser.close();

  // ================= SAVE JSON =================
  const outputDir = path.resolve('scrapers/output');
  fs.mkdirSync(outputDir, { recursive: true });

  const outputPath = path.join(outputDir, 'axiata.json');
  fs.writeFileSync(outputPath, JSON.stringify(results, null, 2));

  console.log(`📄 JSON saved → ${outputPath}`);

  // ================= SCRAPING LOG =================
  const status =
    failed === 0 ? 'success' :
    success === 0 ? 'failed' : 'partial';

  await db.execute(
    `INSERT INTO scraping_logs
     (source_website, total_scraped, success_count, failed_count, status, started_at, finished_at)
     VALUES (?, ?, ?, ?, ?, ?, ?)`,
    [
      'axiata',
      programs.length,
      success,
      failed,
      status,
      startTime,
      new Date()
    ]
  );

  await db.end();

  console.log('🎉 AXIATA scraping completed');
})();
