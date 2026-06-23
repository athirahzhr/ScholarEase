// scrapers/moe.asasi.js

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

const programs = [
  {
    title: 'Bantuan Kewangan Asasi',
    provider: 'Kementerian Pendidikan Malaysia (KPM)',
    url: 'https://www.moe.gov.my/bantuan-kewangan-asasi'
  }
];

(async () => {

  const startTime = new Date();

  let success = 0;
  let failed = 0;

  let inserted = 0;
  let updated = 0;
  let skipped = 0;

  const results = [];

  console.log('🚀 Scraping KPM Bantuan Kewangan Asasi');

  const browser = await chromium.launch({
    headless: true
  });

  const page = await browser.newPage();

  for (const program of programs) {

    try {

      console.log(`🔍 Scraping: ${program.title}`);

      await page.goto(program.url, {
        waitUntil: 'networkidle',
        timeout: 60000
      });

      await page.waitForTimeout(3000);

      const rawText = await page.evaluate(() => {
        return document.body.innerText || '';
      });

      if (!rawText || rawText.length < 200) {
        throw new Error('No usable content found');
      }

      console.log(
        rawText.substring(0, 3000)
      );

      // ================= PARSE RULES =================

      const rules =
        parseRules(rawText);

      const deadline =
        detectDeadline(rawText);

      console.log(
        'PARSED RULES:',
        JSON.stringify(rules, null, 2)
      );

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
        source_website: 'moe_asasi',
        scraped_at: new Date().toISOString()
      });

      // ================= CHECK EXISTING =================

      const [existingRows] =
        await db.execute(
          `
          SELECT id, deadline
          FROM scholarships
          WHERE title = ?
          LIMIT 1
          `,
          [program.title]
        );

      let scholarshipId = null;

      if (existingRows.length > 0) {

        scholarshipId =
          existingRows[0].id;

        await db.execute(
          `
          UPDATE scholarships
          SET
            provider = ?,
            raw_eligibility = ?,
            application_link = ?,
            deadline = ?,
            updated_at = NOW()
          WHERE id = ?
          `,
          [
            program.provider,
            rawText.slice(0, 8000),
            program.url,
            deadline,
            scholarshipId
          ]
        );

        await db.execute(
          `
          UPDATE scholarship_eligibility_criteria
          SET

            min_spm_as = ?,
            max_spm_as = ?,

            required_subjects = ?,

            max_monthly_income = ?,

            income_categories = ?,

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
              rules.income_categories
            ),

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

        updated++;

        console.log(
          `♻️ Updated: ${program.title}`
        );

      } else {

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
              'moe_asasi',
              1,
              1
            ]
          );

        scholarshipId =
          scholarshipResult.insertId;

        await db.execute(
          `
          INSERT INTO scholarship_eligibility_criteria (

            scholarship_id,

            min_spm_as,
            max_spm_as,

            required_subjects,

            max_monthly_income,

            income_categories,

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
            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW()
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
              rules.income_categories
            ),

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

        inserted++;

        console.log(
          `✅ Inserted: ${program.title}`
        );
      }

      success++;

    } catch (err) {

      failed++;

      console.error(
        `❌ Failed: ${program.title}`
      );

      console.error(err);
    }
  }

  await browser.close();

  const outputPath =
    path.resolve(
      'scrapers/output/moe.asasi.json'
    );

  fs.mkdirSync(
    path.dirname(outputPath),
    { recursive: true }
  );

  fs.writeFileSync(
    outputPath,
    JSON.stringify(results, null, 2)
  );

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
      'moe_asasi',
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
Failed   : ${failed}
==============================
`);

})();