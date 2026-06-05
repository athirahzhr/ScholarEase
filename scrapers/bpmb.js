// scrapers/bpmb.js

import { chromium } from 'playwright';
import fs from 'fs';
import path from 'path';
import mysql from 'mysql2/promise';

import {
  parseRules,
  detectDeadline
} from './utils/ruleParser.js';

// ================= DB CONNECTION =================

const db = await mysql.createConnection({
  host: '127.0.0.1',
  user: 'scholarease',
  password: 'ScholarEase@2026',
  database: 'scholarease_db'
});

// ================= PROGRAM CONFIG =================

const program = {
   title: 'BPMB Award of Group Undergraduate Scholarship (BAGUS)',
  provider: 'Bank Pembangunan Malaysia Berhad',
  url: 'https://www.bpmb.com.my/scholarship/'
};

(async () => {

  const startTime = new Date();

  let success = 0;
  let failed = 0;

  let inserted = 0;
  let updated = 0;
  let skipped = 0;

  const results = [];

  console.log('🚀 Scraping BPMB – BAGUS Scholarship');

  const browser = await chromium.launch({
    headless: true
  });

  const page = await browser.newPage();

  try {

    console.log(`🔍 Scraping: ${program.title}`);

    await page.goto(program.url, {
      waitUntil: 'domcontentloaded',
      timeout: 60000
    });

    await page.waitForTimeout(3000);

    await page.waitForSelector('body');

    // ================= EXTRACT PAGE TEXT =================

    const rawText = await page.evaluate(() => {

      const body = document.body;

      if (!body) {
        return '';
      }

      return body.innerText || '';
    });

    if (!rawText || rawText.length < 100) {
      throw new Error(
        'Failed to extract scholarship content'
      );
    }

    // ================= PARSE RULES =================

    const rules =
      parseRules(rawText);

    const deadline =
      detectDeadline(rawText);

    // ================= SAVE JSON =================

    results.push({
      title: program.title,
      provider: program.provider,
      description: program.title,
      raw_eligibility: rawText.slice(0, 8000),
      application_link: program.url,
      application_deadline: deadline,
      rules,
      source: 'scraped',
      source_website: 'bpmb',
      scraped_at: new Date().toISOString()
    });

    // ================= CHECK EXISTING =================

    const [existingRows] = await db.execute(
      `
      SELECT id, deadline
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

      const existing =
        existingRows[0];

      scholarshipId =
        existing.id;

      const existingDeadline =
        existing.deadline
          ? [
              existing.deadline.getFullYear(),
              String(existing.deadline.getMonth() + 1)
                .padStart(2, '0'),
              String(existing.deadline.getDate())
                .padStart(2, '0')
            ].join('-')
          : null;

      const deadlineChanged =
        String(existingDeadline || '') !==
        String(deadline || '');

      if (deadlineChanged) {

        await db.execute(
          `
          UPDATE scholarships
          SET
            provider = ?,
            raw_eligibility = ?,
            deadline = ?,
            updated_at = NOW()
          WHERE id = ?
          `,
          [
            program.provider,
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

      // ================= CHECK ELIGIBILITY =================

      const [criteriaRows] =
        await db.execute(
          `
          SELECT id
          FROM scholarship_eligibility_criteria
          WHERE scholarship_id = ?
          LIMIT 1
          `,
          [scholarshipId]
        );

      // =====================================================
      // INSERT ELIGIBILITY IF NOT EXIST
      // =====================================================

      if (criteriaRows.length === 0) {

        await insertEligibility(
          db,
          scholarshipId,
          rules
        );

      } else {

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

            JSON.stringify(
              rules.required_subjects
            ),

            rules.max_monthly_income,

            JSON.stringify(
              rules.study_paths
            ),

            JSON.stringify(
              rules.fields_of_study
            ),

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
      }

    } else {

      // =====================================================
      // INSERT NEW SCHOLARSHIP
      // =====================================================

      const [scholarshipResult] =
        await db.execute(
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
            'bpmb',
            1,
            1
          ]
        );

      scholarshipId =
        scholarshipResult.insertId;

      // ================= INSERT ELIGIBILITY =================

      await insertEligibility(
        db,
        scholarshipId,
        rules
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
      `❌ Failed: ${program.title}`
    );

    console.error(err);

  }

  await browser.close();

  // ================= SAVE JSON =================

  const outputPath =
    path.resolve(
      'scrapers/output/bpmb.json'
    );

  fs.mkdirSync(
    path.dirname(outputPath),
    { recursive: true }
  );

  fs.writeFileSync(
    outputPath,
    JSON.stringify(results, null, 2)
  );

  // ================= SCRAPING LOG =================

  const status =
    failed === 0
      ? 'success'
      : success === 0
      ? 'failed'
      : 'partial';

  await db.execute(
    `
    INSERT INTO scraping_logs
    (
      source_website,
      total_scraped,
      success_count,
      failed_count,
      status,
      started_at,
      finished_at
    )
    VALUES (?, ?, ?, ?, ?, ?, ?)
    `,
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

  console.log(`
==============================
SCRAPING SUMMARY
==============================
Inserted : ${inserted}
Updated  : ${updated}
Skipped  : ${skipped}
==============================
`);

  console.log(
    '🎉 BPMB scraping completed'
  );

})();

// =====================================================
// INSERT ELIGIBILITY HELPER
// =====================================================

async function insertEligibility(
  db,
  scholarshipId,
  rules
) {

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

      JSON.stringify(
        rules.required_subjects
      ),

      rules.max_monthly_income,

      JSON.stringify(
        rules.study_paths
      ),

      JSON.stringify(
        rules.fields_of_study
      ),

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
}