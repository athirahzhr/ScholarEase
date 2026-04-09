import { parseRules } from './utils/ruleParser.js';

const testText = `
Minimum 8A dalam SPM.
Pelajar daripada keluarga B40 diberi keutamaan.
Program peringkat Degree dalam bidang Engineering.
Mempunyai pengalaman kepimpinan.
Umur tidak melebihi 19 tahun.
Ikatan perkhidmatan selama 5 tahun.
`;

const rules = parseRules(testText);

console.log('=== PARSED RULES ===');
console.log(rules);
