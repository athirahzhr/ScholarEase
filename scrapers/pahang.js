import { chromium } from "playwright";
import { db } from "../db.js";

const URLS = [
  "https://www.yp.org.my/program-bantuan-pendidikan/geran-pra-siswazah-pasca-siswazah/",
  "https://www.yp.org.my/program-bantuan-pendidikan/skim-pelajar-cemerlang-yayasan-pahang-spcyp/"
];

export async function scrapePahang() {
  const browser = await chromium.launch({ headless: true });
  const page = await browser.newPage();

  for (const url of URLS) {
    console.log(`[Pahang] Scraping: ${url}`);
    await page.goto(url, { waitUntil: "domcontentloaded" });

    // Title
    const title = await page.$eval("h1", el => el.innerText.trim());

    // Full eligibility text
    const rawEligibility = await page.$eval(
      ".entry-content",
      el => el.innerText.trim()
    );

    await db.execute(
      `
      INSERT INTO scholarships
      (title, provider, raw_eligibility, source, source_website, is_active)
      VALUES (?, ?, ?, 'scraped', ?, 1)
      `,
      [
        title,
        "Yayasan Pahang",
        rawEligibility,
        url
      ]
    );

    console.log(`[Pahang] Saved: ${title}`);
  }

  await browser.close();
}
