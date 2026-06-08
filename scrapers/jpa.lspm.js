// scrapers/jpa.lspm.js

import { chromium } from 'playwright';
import fs from 'fs';
import path from 'path';
import mysql from 'mysql2/promise';
import {parseRules,detectDeadline} from './utils/ruleParser.js';

// ================= DB CONNECTION =================
const db = await mysql.createConnection({
  host: '127.0.0.1',
  user: 'scholarease',
  password: 'ScholarEase@2026',
  database: 'scholarease_db'
});

// ================= PROGRAM CONFIG =================
const programs = [
  {
    title: 'Program Khas Lepasan Sijil Pelajaran Malaysia Dalam Negara (LSPM)',
    provider: 'Jabatan Perkhidmatan Awam (JPA)',
    url: 'https://penajaan.jpa.gov.my/info-penajaan/latihan-sebelum-perkhidmatan/program-pelajar/program-khas-lepasan-sijil-pelajaran-malaysia-dalam-negara-lspm.html'
  }
];

(async () => {

  console.log('🚀 Scraping JPA : LSPM Foundation');

  const startTime = new Date();

  let success = 0;
  let failed = 0;
  let inserted = 0;
  let updated = 0;
  let skipped = 0;
  

  const results = [];

  const browser = await chromium.launch({
    headless: true
  });

  const page = await browser.newPage();

  for (const program of programs) {

    try {

      console.log(`🔍 Scraping: ${program.title}`);

      await page.goto(program.url, {
        waitUntil: 'domcontentloaded',
        timeout: 60000
      });

      // allow rendering
      await page.waitForTimeout(3000);

      // extract content
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

      // ================= PARSE RULES =================

      const rules = parseRules(rawText);
      const deadline = detectDeadline(rawText);

      // ================= STORE JSON =================

      results.push({

        title: program.title,

        provider: program.provider,

        description: program.title,

        raw_eligibility: rawText.slice(0, 8000),

        application_link: program.url,

        application_deadline: deadline,

        source: 'scraped',

        source_website: 'jpa_lspm',

        is_official: 1,

        is_active: 1,

        scraped_at: new Date().toISOString(),

        rules
      });

// ================= CHECK EXISTING SCHOLARSHIP =================

const [existingRows] = await db.execute(
  `
  SELECT id, deadline, raw_eligibility
  FROM scholarships
  WHERE title = ?
  LIMIT 1
  `,
  [program.title]
);

let scholarshipId = null;

// =====================================================
// UPDATE EXISTING
// =====================================================

if (existingRows.length > 0) {

  const existing = existingRows[0];

  scholarshipId = existing.id;

  const contentChanged = false;

  const existingDeadline =
  existing.deadline
    ? [
        existing.deadline.getFullYear(),
        String(existing.deadline.getMonth() + 1).padStart(2, '0'),
        String(existing.deadline.getDate()).padStart(2, '0')
      ].join('-')
    : null;

console.log('DB Deadline:', existingDeadline);
console.log('Parsed Deadline:', deadline);

const deadlineChanged =
  existingDeadline !== deadline;

  // ================= UPDATE SCHOLARSHIP =================

  if (contentChanged || deadlineChanged) {

console.log('DB Deadline:', existingDeadline);
console.log('Parsed Deadline:', deadline);
console.log('Deadline Changed:', deadlineChanged);

    await db.execute(
      `
      UPDATE scholarships
      SET
        raw_eligibility = ?,
        deadline = ?,
        updated_at = NOW()
      WHERE id = ?
      `,
      [
        rawText.slice(0, 8000),
        deadline,
        scholarshipId
      ]
    );

    console.log(
      `🔄 Updated scholarship: ${program.title}`
    );

    updated++;

  } else {

    console.log(
      `⏩ No scholarship changes: ${program.title}`
    );

    skipped++;
  }

  // ================= UPDATE ELIGIBILITY =================

  await db.execute(
    `
    UPDATE scholarship_eligibility_criteria
    SET

      min_spm_as = ?,
      max_spm_as = ?,

      required_subjects = ?,

      max_monthly_income = ?,

      study_paths = ?,

      fields_of_study = ?,

      study_destination = ?,

      bumiputera_required = ?,
      bumiputera_priority = ?,

      gender_requirement = ?,

      citizenship_required = ?,

      state_requirement = ?,

      rural_priority = ?,

      min_age = ?,
      max_age = ?,

      leadership_required = ?,
      leadership_priority = ?,

      sports_achievement = ?,

      min_community_hours = ?,

      bond_required = ?,
      bond_years = ?,

      priority_weight = ?,

      max_score = ?,

      notes = ?,

      updated_at = NOW()

    WHERE scholarship_id = ?
    `,
    [

      rules.min_spm_as,

      rules.max_spm_as,

      JSON.stringify(rules.required_subjects),

      rules.max_monthly_income,

      JSON.stringify(rules.study_paths),

      JSON.stringify(rules.fields_of_study),

      rules.study_destination,

      rules.bumiputera_required,

      rules.bumiputera_priority,

      rules.gender_requirement,

      rules.citizenship_required,

      rules.state_requirement,

      rules.rural_priority,

      rules.min_age,

      rules.max_age,

      rules.leadership_required,

      rules.leadership_priority,

      rules.sports_achievement,

      rules.min_community_hours,

      rules.bond_required,

      rules.bond_years,

      rules.priority_weight,

      rules.max_score,

      rules.notes,

      scholarshipId
    ]
  );

  console.log(
    `♻️ Eligibility synced: ${program.title}`
  );

} else {

  // =====================================================
  // INSERT NEW SCHOLARSHIP
  // =====================================================

  const [scholarshipResult] = await db.execute(
    `
    INSERT INTO scholarships (
      title,
      provider,
      description,
      raw_eligibility,
      application_link,
      deadline,
      source,
      source_website,
      is_official,
      is_active,
      created_at,
      updated_at
    )
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
    `,
    [
      program.title,

      program.provider,

      program.title,

      rawText.slice(0, 8000),

      program.url,

      deadline,

      'scraped',

      'jpa_lspm',

      1,

      1
    ]
  );

  scholarshipId =
    scholarshipResult.insertId;

  // ================= INSERT ELIGIBILITY =================

  await db.execute(
    `
    INSERT INTO scholarship_eligibility_criteria (

      scholarship_id,

      min_spm_as,
      max_spm_as,

      required_subjects,

      max_monthly_income,

      study_paths,

      fields_of_study,

      study_destination,

      bumiputera_required,
      bumiputera_priority,

      gender_requirement,

      citizenship_required,

      state_requirement,

      rural_priority,

      min_age,
      max_age,

      leadership_required,
      leadership_priority,

      sports_achievement,

      min_community_hours,

      bond_required,
      bond_years,

      priority_weight,

      max_score,

      notes,

      created_at,
      updated_at

    )
    VALUES (
      ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW()
    )
    `,
    [

      scholarshipId,

      rules.min_spm_as,

      rules.max_spm_as,

      JSON.stringify(rules.required_subjects),

      rules.max_monthly_income,

      JSON.stringify(rules.study_paths),

      JSON.stringify(rules.fields_of_study),

      rules.study_destination,

      rules.bumiputera_required,

      rules.bumiputera_priority,

      rules.gender_requirement,

      rules.citizenship_required,

      rules.state_requirement,

      rules.rural_priority,

      rules.min_age,

      rules.max_age,

      rules.leadership_required,

      rules.leadership_priority,

      rules.sports_achievement,

      rules.min_community_hours,

      rules.bond_required,

      rules.bond_years,

      rules.priority_weight,

      rules.max_score,

      rules.notes
    ]
  );

  console.log(
    `✅ Inserted new scholarship: ${program.title}`
  );

  inserted++;
}

success++;

console.log(
  `✅ Parsed & synchronized: ${program.title}`
);

    } catch (err) {

      failed++;

      console.error(
        `❌ Failed: ${program.title} → ${err.message}`
      );
    }
  }

  await browser.close();

  // ================= SAVE JSON FILE =================

  const outputDir =
    path.resolve('scrapers/output');

  fs.mkdirSync(outputDir, {
    recursive: true
  });

  const outputPath =
    path.join(outputDir, 'jpa.lspm.json');

  fs.writeFileSync(
    outputPath,
    JSON.stringify(results, null, 2)
  );

  console.log(`📄 JSON saved → ${outputPath}`);

  // ================= SAVE SCRAPING LOG =================

  const status =
    failed === 0
      ? 'success'
      : success === 0
      ? 'failed'
      : 'partial';

 await db.execute(`
  INSERT INTO scraping_logs (
      source_website,
      total_scraped,
      success_count,
      failed_count,
      inserted_count,
      updated_count,
      skipped_count,
      status,
      started_at,
      finished_at
  ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
  `, [
      'jpa_lspm',
      programs.length,
      success,
      failed,

      inserted,
      updated,
      skipped,

      status,
      startTime,
      new Date()
  ]);

  await db.end();

console.log(`
==============================
SCRAPING SUMMARY
==============================
Inserted : ${inserted}
Updated  : ${updated}
Skipped  : ${skipped}
==============================
`);

  console.log('🎉 JPA: LSPM scraping completed');

})();