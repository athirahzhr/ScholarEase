// scrapers/utils/ruleParser.js

export function parseRules(rawText) {
  const text = rawText.toLowerCase();

  const minAs = detectMinAs(text);

  return {
    // ===== Academic =====
    min_spm_as: minAs,
    max_spm_as: null,
    required_subjects: detectSubjects(text),

    // ===== Income =====
    max_monthly_income: detectMaxIncome(text),

    income_categories:
  detectIncomeCategory(text),
    

    // ===== Study =====
    study_paths: detectStudyPath(text),
    fields_of_study: detectFields(text),
    study_destination: detectStudyDestination(text),

    // ===== Demographic =====
    bumiputera_required: /bumiputera\s+(sahaja|only)/i.test(text),
    bumiputera_priority: /bumiputera/i.test(text),

    gender_requirement: detectGender(text),

    citizenship_required:
    /warganegara malaysia|malaysian citizen|malaysian citizens|citizen of malaysia/i.test(text)
    ? 'Malaysian'
    : null,

    state_requirement: detectState(text),
    rural_priority: /luar bandar|rural/i.test(text),

    // ===== Age =====
    min_age: detectMinAge(text),
    max_age: detectMaxAge(text),

    // ===== Merit =====
    leadership_required: /mesti.*kepimpinan|required.*leadership/i.test(text),
    leadership_priority: /kepimpinan|leadership/i.test(text),

    sports_achievement: /sukan|athlete|sports/i.test(text),
    min_community_hours: detectCommunityHours(text),

    // ===== Bond =====
    bond_required: /ikatan perkhidmatan|service bond|pinjaman boleh ubah|pbu/i.test(text),
    bond_years: detectBondYears(text),

    // ===== System =====
    priority_weight: 1,
    max_score: 100,
    notes: buildNotes(text)
  };
}

/* ================= HELPER FUNCTIONS ================= */

// ---------- ACADEMIC ----------

function detectSubjects(text) {
  const subjects = [];

  if (/add(itional)? math|additional mathematics/i.test(text)) subjects.push('Mathematics');
  else if (/mathematics|math/i.test(text)) subjects.push('Mathematics');

  if (/physics|fizik/i.test(text)) subjects.push('Physics');
  if (/chemistry|kimia/i.test(text)) subjects.push('Chemistry');
  if (/biology|biologi/i.test(text)) subjects.push('Biology');

  return subjects.length ? subjects : null;
}


// ---------- STUDY ----------

function detectStudyPath(text) {
  const paths = [];

  if (/foundation|asasi|pre[- ]?university|a[- ]?level/i.test(text)) {
    paths.push('Foundation');
  }

  if (/matrikulasi|matriculation/i.test(text)) {
    paths.push('Matriculation');
  }

  if (/diploma/i.test(text)) {
    paths.push('Diploma');
  }

  if (/ijazah|degree|sarjana muda|undergraduate/i.test(text)) {
    paths.push('Degree');
  }

  if (/tvet|vocational/i.test(text)) {
    paths.push('TVET');
  }

  if (/master|phd|postgraduate/i.test(text)) {
    paths.push('Postgraduate');
  }

  return paths.length
    ? [...new Set(paths)]
    : null;
}

function detectMaxIncome(text) {

  const patterns = [

    /income[\s\S]*?rm\s?([\d,]+)/i,

    /household income[\s\S]*?rm\s?([\d,]+)/i,

    /not exceeding\s*rm\s?([\d,]+)/i,

    /rm\s?([\d,]+)\s*(and below|below)/i,

    /rm\s?([\d,]+)/i,

    /rm\s?([\d,]+(?:\.\d+)?)\s*sebulan/i,
  ];

  for (const pattern of patterns) {

    const match = text.match(pattern);

    if (match) {

      return parseInt(
        match[1].replace(/,/g, ''),
        10
      );
    }
  }

  return null;
}

function detectIncomeCategory(text) {

  const categories = [];


  if (/b40/i.test(text)) {
    categories.push('B40');
  }

  if (/m40/i.test(text)) {
    categories.push('M40');
  }

  if (/t20/i.test(text)) {
    categories.push('T20');
  }

  return categories.length
    ? categories
    : null;
}

function detectFields(text) {

  const fields = [];

  // ================= ARCHITECTURE =================
if (
  /architecture|seni bina/i.test(text)
) {
  fields.push('Architecture');
}

  // ================= ENGINEERING =================
  if (
    /engineering|kejuruteraan/i.test(text)
  ) {
    fields.push('Engineering');
  }

  // ================= MEDICINE =================
  if (
    /medicine|medical|perubatan/i.test(text)
  ) {
    fields.push('Medicine');
  }

  // ================= COMPUTER SCIENCE =================
  if (
  /computer science|computing|software|it\b|information technology|teknologi maklumat/i.test(text)
) {
    fields.push('Computer Science');
  }

  // ================= DATA SCIENCE =================
  if (
    /data science|big data|machine learning|artificial intelligence/i.test(text)
  ) {
    fields.push('Data Science');
  }

  // ================= FINANCE =================
  if (
    /finance|financial/i.test(text)
  ) {
    fields.push('Finance');
  }

  // ================= ACCOUNTING =================
  if (
    /accounting|perakaunan/i.test(text)
  ) {
    fields.push('Accounting');
  }

  // ================= ECONOMICS =================
  if (
    /economics|ekonomi/i.test(text)
  ) {
    fields.push('Economics');
  }

  // ================= LAW =================
  if (
    /field.*law|bidang.*undang-undang|program.*law|law degree|ijazah undang-undang/i
  ) {
    fields.push('Law');
  }

  // ================= ACTUARIAL =================
  if (
    /actuarial/i.test(text)
  ) {
    fields.push('Actuarial Science');
  }

  // ================= MATHEMATICS =================
  if (
    /mathematics|math/i.test(text)
  ) {
    fields.push('Mathematics');
  }

  // ================= STATISTICS =================
  if (
    /statistics|statistic/i.test(text)
  ) {
    fields.push('Statistics');
  }

  // ================= GENERAL SCIENCE =================
 if (
  /\bgeneral science\b|\bsains tulen\b/i.test(text) &&
  !/computer science|data science/i.test(text)
) {
  fields.push('Science');
}


  // ================= ARTS =================

if (/archaeology/i.test(text))
  fields.push('Archaeology');

if (/architecture/i.test(text))
  fields.push('Architecture');

if (/arts?\s*&\s*design|arts?\s+and\s+design/i.test(text))
  fields.push('Art & Design');

if (/history/i.test(text))
  fields.push('History');

if (/linguistics/i.test(text))
  fields.push('Linguistics');

if (/performing arts/i.test(text))
  fields.push('Performing Arts');

if (/philosophy/i.test(text))
  fields.push('Philosophy');

// ================= SCIENCE =================

if (/chemistry/i.test(text))
  fields.push('Chemistry');

if (/physics/i.test(text))
  fields.push('Physics');

if (/geography/i.test(text))
  fields.push('Geography');

if (/environmental sciences/i.test(text))
  fields.push('Environmental Science');

if (/biological science/i.test(text))
  fields.push('Biological Science');

if (/pharmacy/i.test(text))
  fields.push('Pharmacy');

// ================= SOCIAL SCIENCE =================

if (/business/i.test(text))
  fields.push('Business');

if (/communication/i.test(text))
  fields.push('Communication');

if (/education/i.test(text))
  fields.push('Education');

if (/hospitality/i.test(text))
  fields.push('Hospitality');

if (/anthropology/i.test(text))
  fields.push('Anthropology');

if (
  /social science|sains sosial|sastera/i.test(text)
) {
  fields.push('Social Science');
}

// ================= TECHNICAL =================
if (
  /technical|teknikal|teknologi/i.test(text)
) {
  fields.push('Technical');
}

  return fields.length
    ? [...new Set(fields)]
    : null;
}

function detectStudyDestination(text) {
  if (/overseas|luar negara|abroad/i.test(text)) return 'Overseas';
  if (/dalam negara|local/i.test(text)) return 'Local';
  return 'Both';
}

// ---------- DEMOGRAPHIC ----------

function detectGender(text) {
  if (/lelaki sahaja|male only/i.test(text)) return 'Male';
  if (/perempuan sahaja|female only/i.test(text)) return 'Female';
  return 'Any';
}

function detectState(text) {

  const lowered = text.toLowerCase();

  // ignore contact/address sections
  if (
    lowered.includes('contact us') ||
    lowered.includes('address') ||
    lowered.includes('jalan') ||
    lowered.includes('tower')
  ) {
    return null;
  }

  const states = [
    'johor',
    'kedah',
    'kelantan',
    'melaka',
    'negeri sembilan',
    'pahang',
    'perak',
    'perlis',
    'pulau pinang',
    'penang',
    'sabah',
    'sarawak',
    'selangor',
    'terengganu',
    'kuala lumpur',
    'labuan',
    'putrajaya'
  ];

  const patterns = [

    /anak negeri\s+([a-z\s]+)/i,

    /from\s+([a-z\s]+)/i,

    /students from\s+([a-z\s]+)/i,

    /state\s*:\s*([a-z\s]+)/i,

    /negeri\s+([a-z\s]+)/i
  ];

  for (const pattern of patterns) {

    const match = text.match(pattern);

    if (match) {

      const found =
        states.find(state =>
          match[1].toLowerCase().includes(state)
        );

      return found
        ? capitalize(found)
        : null;
    }
  }

  return null;
}

// ---------- AGE ----------

function detectMinAge(text) {

  const rangeMatch = text.match(
    /at least\s*(\d+)\s*years?.*not more than\s*(\d+)/i
  );

  if (rangeMatch) {
    return parseInt(rangeMatch[1], 10);
  }

  const match = text.match(
    /minimum\s*(\d+)\s*(tahun|years?)/i
  );

  return match
    ? parseInt(match[1], 10)
    : null;
}

function detectMaxAge(text) {

  const patterns = [

    /not exceeding\s*(\d+)\s*years?\s*of\s*age/i,

    /aged\s*(\d+)\s*and below/i,

    /age[d]?\s*(\d+)\s*(years?)?\s*and below/i,

    /not exceeding\s*(\d+)/i,

    /tidak melebihi\s*(\d+)/i,

    /berumur\s*(\d+)\s*tahun/i,

    /belum mencapai\s*(\d+)\s*tahun/i,

    /umur\s*(\d+)\s*tahun/i,

    /berusia\s*(\d+)\s*tahun/i,

    /umur\s*belum\s*mencapai\s*(\d+)\s*tahun/i
  ];

  for (const pattern of patterns) {

    const match = text.match(pattern);

    if (match) {

      return parseInt(match[1], 10);
    }
  }

  return null;
}

function detectMinAs(text) {

  // ================= PRIORITIZE SPM =================

  const spmPatterns = [

    /spm[^.\n]*minimum\s*(\d{1,2})\s*a[s]?/i,

    /spm[^.\n]*at least\s*(\d{1,2})\s*a[s]?/i,

    /spm[^.\n]*(\d{1,2})\s*a[s]?/i
  ];

  for (const pattern of spmPatterns) {

    const match = text.match(pattern);

    if (match) {

      const val = parseInt(match[1], 10);

      if (val >= 1 && val <= 12) {
        return val;
      }
    }
  }

  // ================= GENERAL FALLBACK =================

  const patterns = [

    /minimum\s*of\s*(\d{1,2})\s*a[s]?/i,

    /minimum\s*(\d{1,2})\s*a[s]?/i,

    /at least\s*(\d{1,2})\s*a[s]?/i,

    /(\d{1,2})\s*a[s]?\s*(minimum|required)/i
  ];

  for (const pattern of patterns) {

    const match = text.match(pattern);

    if (match) {

      const val = parseInt(
        match.find(v => /^\d+$/.test(v)),
        10
      );

      if (val >= 1 && val <= 12) {
        return val;
      }
    }
  }

  if (
    /pelajar terbaik|cemerlang|berprestasi tinggi|excellent/i.test(text)
  ) {
    return 8;
  }

  return null;
}

// ---------- COMMUNITY / BOND ----------

function detectCommunityHours(text) {
  const match = text.match(/(\d+)\s*jam\s*khidmat|community service\s*(\d+)/i);
  return match ? parseInt(match[1] || match[2], 10) : null;
}

function detectBondYears(text) {
  const match = text.match(/(\d+)\s*tahun\s*(ikatan|perkhidmatan|bond)/i);
  return match ? parseInt(match[1], 10) : null;
}

// ---------- NOTES ----------

function buildNotes(text) {

  if (
    !/(\d+)\s*a|rm\s?[\d,]+|umur|age|ikatan|bond|cgpa/i.test(text)
  ) {
    return 'Eligibility details not explicitly stated on official page';
  }

  return 'Auto-parsed by ruleParser';
}

function capitalize(word) {
  return word.charAt(0).toUpperCase() + word.slice(1);
}

export function detectDeadline(text) {

  // ================= PETRONAS MALAY RANGE =================
// Example:
// 31 Mac 2026 sehingga 10 April 2026

const malayRangeMatch = text.match(
  /(\d{1,2})\s+(januari|februari|mac|march|april|mei|may|jun|june|julai|july|ogos|august|september|oktober|october|november|disember|december)\s+(20\d{2})[\s\S]*?(\d{1,2})\s+(januari|februari|mac|march|april|mei|may|jun|june|julai|july|ogos|august|september|oktober|october|november|disember|december)\s+(20\d{2})/i
);

if (malayRangeMatch) {

  const day = malayRangeMatch[4];
  const month = malayRangeMatch[5];
  const year = malayRangeMatch[6];

  return formatMalayDate(
    `${day} ${month} ${year}`
  );
}

  // ================= RANGE FORMAT =================
  // Example:
  // 17 April - 26 April 2026

  const rangeMatch = text.match(
    /(\d{1,2})\s+(January|February|March|April|May|June|July|August|September|October|November|December)\s*-\s*(\d{1,2})\s+(January|February|March|April|May|June|July|August|September|October|November|December)\s+(\d{4})/i
  );

  if (rangeMatch) {

    const day = rangeMatch[3];
    const month = rangeMatch[4];
    const year = rangeMatch[5];

    return formatDate(
      `${day} ${month} ${year}`
    );
  }

  // ================= ORDINAL RANGE FORMAT =================
// Example:
// 31st March - 14th April 2026

const ordinalRangeMatch = text.match(
  /(\d{1,2})(st|nd|rd|th)\s+(January|February|March|April|May|June|July|August|September|October|November|December)\s*-\s*(\d{1,2})(st|nd|rd|th)\s+(January|February|March|April|May|June|July|August|September|October|November|December)\s+(20\d{2})/i
);

if (ordinalRangeMatch) {

  const day =
    ordinalRangeMatch[4];

  const month =
    ordinalRangeMatch[6];

  const year =
    ordinalRangeMatch[7];

  return formatDate(
    `${day} ${month} ${year}`
  );
}

  // ================= 13th April 2026 =================

const ordinalMatch = text.match(
  /(\d{1,2})(st|nd|rd|th)\s+(January|February|March|April|May|June|July|August|September|October|November|December)\s+(20\d{2})/i
);

if (ordinalMatch) {

  const day =
    ordinalMatch[1];

  const month =
    ordinalMatch[3];

  const year =
    ordinalMatch[4];

  const parsed =
    new Date(`${day} ${month} ${year}`);

  if (!isNaN(parsed)) {

    return parsed
      .toISOString()
      .split('T')[0];
  }
}

  // ================= SINGLE DATE FORMAT =================
  // Example:
  // 18 May 2026

  const match = text.match(
    /(\d{1,2})\s+(January|February|March|April|May|June|July|August|September|October|November|December)\s+(\d{4})/i
  );

  if (!match) {
    return null;
  }

  const day = match[1];
  const month = match[2];
  const year = match[3];

  return formatDate(
    `${day} ${month} ${year}`
  );
}

function formatDate(dateString) {

  const months = {
    january: '01',
    february: '02',
    march: '03',
    april: '04',
    may: '05',
    june: '06',
    july: '07',
    august: '08',
    september: '09',
    october: '10',
    november: '11',
    december: '12'
  };

  const parts = dateString
    .trim()
    .split(' ');

  const day = parts[0]
    .padStart(2, '0');

  const month =
    months[
      parts[1].toLowerCase()
    ];

  const year = parts[2];

  return `${year}-${month}-${day}`;
}

function formatMalayDate(dateString) {

  const months = {

    januari: '01',
    februari: '02',
    mac: '03',
    march: '03',
    april: '04',
    mei: '05',
    may: '05',
    june: '06',
    july: '07',
    august: '08',
    october: '10',
    december: '12',
    jun: '06',
    julai: '07',
    ogos: '08',
    september: '09',
    oktober: '10',
    november: '11',
    disember: '12'
  };

  const parts =
    dateString.trim().split(' ');

  const day =
    parts[0].padStart(2, '0');

  const month =
    months[
      parts[1].toLowerCase()
    ];

  const year = parts[2];

  return `${year}-${month}-${day}`;
}
