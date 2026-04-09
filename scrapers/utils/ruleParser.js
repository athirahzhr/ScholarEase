// scrapers/utils/ruleParser.js

export function parseRules(rawText) {
  const text = rawText.toLowerCase();

  const minAs = detectMinAs(text);
  const academicCategory = detectAcademicCategory(text, minAs);

  return {
    // ===== Academic =====
    min_spm_as: minAs,
    max_spm_as: null,
    academic_categories: academicCategory,
    required_subjects: detectSubjects(text),

    // ===== Income =====
    income_categories: detectIncome(text),
    income_strict: !/keutamaan|priority|digalakkan|preference/i.test(text),

    // ===== Study =====
    study_paths: detectStudyPath(text),
    fields_of_study: detectFields(text),
    study_destination: detectStudyDestination(text),
    study_path_strict: true,

    // ===== Demographic =====
    bumiputera_required: /bumiputera\s+(sahaja|only)/i.test(text),
    bumiputera_priority: /bumiputera/i.test(text),

    gender_requirement: detectGender(text),

    citizenship_required: /warganegara malaysia|malaysian citizen/i.test(text)
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
    match_all_criteria: true,
    priority_weight: 1,
    max_score: 100,
    notes: buildNotes(text)
  };
}

/* ================= HELPER FUNCTIONS ================= */

// ---------- ACADEMIC ----------

function detectMinAs(text) {
  // Tangkap hanya 1–10 A (elak CGPA 3.30 / 33)
  const match = text.match(/(\d{1,2})\s*a[s]?/i);
  if (match) {
    const val = parseInt(match[1], 10);
    if (val >= 1 && val <= 10) return val;
  }

  // JPA / excellence phrasing
  if (/pelajar terbaik|cemerlang|berprestasi tinggi|excellent/i.test(text)) {
    return 8;
  }

  return null;
}

function detectAcademicCategory(text, minAs) {
  if (/9\s*a|10\s*a|straight a/i.test(text)) return ['A4'];
  if (/7\s*a|8\s*a/i.test(text)) return ['A3'];
  if (/3\s*a|5\s*a/i.test(text)) return ['A2'];

  // fallback (conservative)
  if (minAs >= 9) return ['A4'];
  if (minAs >= 7) return ['A3'];

  return null;
}

function detectSubjects(text) {
  const subjects = [];

  if (/add(itional)? math|additional mathematics/i.test(text)) subjects.push('Mathematics');
  else if (/mathematics|math/i.test(text)) subjects.push('Mathematics');

  if (/physics|fizik/i.test(text)) subjects.push('Physics');
  if (/chemistry|kimia/i.test(text)) subjects.push('Chemistry');
  if (/biology|biologi/i.test(text)) subjects.push('Biology');

  return subjects.length ? subjects : null;
}

// ---------- INCOME ----------

function detectIncome(text) {
  if (/b40|berpendapatan rendah|low income/i.test(text)) return ['B1'];
  if (/m40/i.test(text)) return ['B3'];
  return null;
}

// ---------- STUDY ----------

function detectStudyPath(text) {
  const paths = [];

  if (/persediaan|foundation|asasi|pre[- ]?university|a[- ]?level|matrikulasi/i.test(text)) {
    paths.push('C1');
  }

  if (/diploma/i.test(text)) {
    paths.push('C2');
  }

  if (/ijazah|degree|sarjana muda|undergraduate/i.test(text)) {
    paths.push('C3');
  }

  if (/sarjana|master|phd|kedoktoran|postgraduate/i.test(text)) {
    paths.push('C4');
  }

  // JPA phrasing
  if (/hingga ke ijazah|hingga ijazah/i.test(text)) {
    return ['C1', 'C3'];
  }

  return paths.length ? [...new Set(paths)] : null;
}

function detectFields(text) {
  const fields = [];

  if (/engineering|kejuruteraan/i.test(text)) fields.push('Engineering');
  if (/medicine|perubatan/i.test(text)) fields.push('Medicine');
  if (/science|sains/i.test(text)) fields.push('Science');

  if (
    /\bit\b|computer|software|data science|computer science|teknologi maklumat/i.test(text)
  ) {
    fields.push('IT');
  }

  if (/finance|accounting|economics|perakaunan/i.test(text)) {
    fields.push('Finance');
  }

  return fields.length ? [...new Set(fields)] : null;
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
  const states = [
    'selangor','johor','kelantan','terengganu','kedah',
    'perak','pahang','negeri sembilan','melaka',
    'pulau pinang','sabah','sarawak','perlis',
    'kuala lumpur','putrajaya','labuan'
  ];

  const found = states.find(state => text.includes(state));
  return found ? capitalize(found) : null;
}

// ---------- AGE ----------

function detectMinAge(text) {
  const match = text.match(/minimum\s*(\d+)\s*(tahun|years?)/i);
  return match ? parseInt(match[1], 10) : null;
}

function detectMaxAge(text) {
  const match = text.match(
    /tidak melebihi\s*(\d+)\s*(tahun|years?)|not exceeding\s*(\d+)\s*(years?)/i
  );
  return match ? parseInt(match[1] || match[3], 10) : null;
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
    !/(\d+)\s*a|b40|m40|umur|age|ikatan|bond|cgpa/i.test(text)
  ) {
    return 'Eligibility details not explicitly stated on official page';
  }
  return 'Auto-parsed by ruleParser';
}

function capitalize(word) {
  return word.charAt(0).toUpperCase() + word.slice(1);
}


