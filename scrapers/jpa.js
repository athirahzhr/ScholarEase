import { chromium } from 'playwright';
import fs from 'fs';
import path from 'path';
import mysql from 'mysql2/promise';
import { parseRules } from './utils/ruleParser.js';

// 🔗 DB connection
const db = await mysql.createConnection({
  host: '127.0.0.1',
  user: 'root',
  password: '',
  database: 'scholarease_db'
});

const programs = [
  {
    title: 'Program Khas Lepasan Sijil Pelajaran Malaysia Dalam Negara (LSPM)',
    url: 'https://penajaan.jpa.gov.my/index.php/info-penajaan/latihan-sebelum-perkhidmatan/program-pelajar/program-khas-lepasan-sijil-pelajaran-malaysia-dalam-negara-lspm'
  },
  {
    title: 'Program Penajaan Nasional (PPN)',
    url: 'https://penajaan.jpa.gov.my/index.php/info-penajaan/latihan-sebelum-perkhidmatan/program-pelajar/program-penajaan-nasional-ppn'
  },
  {
    title: 'Program Dermasiswa B40 (DB40)',
    url: 'https://penajaan.jpa.gov.my/index.php/info-penajaan/latihan-sebelum-perkhidmatan/program-pelajar/program-dermasiswa-b40-db40'
  }
  
];

(async () => {
  const startTime = new Date();
  let success = 0;
  let failed = 0;

  const browser = await chromium.launch({ headless: true });
  const page = await browser.newPage();
  const results = [];

  for (const program of programs) {
    try {
      await page.goto(program.url, { waitUntil: 'domcontentloaded', timeout: 60000 });
      await page.waitForTimeout(2000);

      const rawText = await page.evaluate(() =>
        (document.querySelector('main') || document.body).innerText
      );

      const rules = parseRules(rawText);

      results.push({
        title: program.title,
        provider: 'Jabatan Perkhidmatan Awam',
        application_link: program.url,
        raw_eligibility: rawText,
        rules,
        scraped_at: new Date().toISOString()
      });

      success++;
    } catch (err) {
      console.error(`❌ Failed: ${program.title}`);
      failed++;
    }
  }

  await browser.close();

  // 💾 Save JSON
  const outputPath = path.resolve('scrapers/output/jpa.json');
  fs.mkdirSync(path.dirname(outputPath), { recursive: true });
  fs.writeFileSync(outputPath, JSON.stringify(results, null, 2));

  // 🧾 INSERT SCRAPING LOG
  const status =
    failed === 0 ? 'success' :
    success === 0 ? 'failed' : 'partial';

  await db.execute(
    `INSERT INTO scraping_logs 
     (source_website, total_scraped, success_count, failed_count, status, started_at, finished_at)
     VALUES (?, ?, ?, ?, ?, ?, ?)`,
    [
      'jpa',
      programs.length,
      success,
      failed,
      status,
      startTime,
      new Date()
    ]
  );

  await db.end();

  console.log('✅ JPA scraping completed with logging');
})();
