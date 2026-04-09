<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ScholarshipRuleSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('scholarship_rules')->truncate();

        $now = now();
        $rules = [];

        /* =========================
           1️⃣ ACADEMIC RULES
           A1–A4 (0–12 A)
        ========================= */
        $rules[] = $this->rule('academic', null, 0, 3, 'A1');
        $rules[] = $this->rule('academic', null, 4, 6, 'A2');
        $rules[] = $this->rule('academic', null, 7, 9, 'A3');
        $rules[] = $this->rule('academic', null, 10, 12, 'A4');

        /* =========================
           2️⃣ INCOME RULES
        ========================= */
        foreach ([
            'b40' => 'B1',
            'low income' => 'B1',
            'm40' => 'B3',
            'middle income' => 'B3',
            't20' => 'B4',
            'high income' => 'B4',
        ] as $keyword => $result) {
            $rules[] = $this->rule('income', $keyword, null, null, $result);
        }

        /* =========================
           3️⃣ STUDY PATH RULES
        ========================= */
        foreach ([
            'foundation' => 'C1',
            'a-level' => 'C1',
            'diploma' => 'C2',
            'degree' => 'C3',
            'matriculation' => 'C3',
            'tvet' => 'C4',
            'vocational' => 'C4',
        ] as $keyword => $result) {
            $rules[] = $this->rule('study', $keyword, null, null, $result);
        }

        /* =========================
           4️⃣ FULL BIASISWA RULES (48)
        ========================= */
        $map = [
            'A1' => [
                'B1' => ['C1'=>'Biasiswa 1','C2'=>'Biasiswa 1','C3'=>'Biasiswa 1','C4'=>'Biasiswa 2'],
                'B3' => ['C1'=>'Biasiswa 1','C2'=>'Biasiswa 2','C3'=>'Biasiswa 2','C4'=>'Biasiswa 3'],
                'B4' => ['C1'=>'Biasiswa 2','C2'=>'Biasiswa 2','C3'=>'Biasiswa 3','C4'=>'Biasiswa 3'],
            ],
            'A2' => [
                'B1' => ['C1'=>'Biasiswa 1','C2'=>'Biasiswa 1','C3'=>'Biasiswa 2','C4'=>'Biasiswa 2'],
                'B3' => ['C1'=>'Biasiswa 2','C2'=>'Biasiswa 2','C3'=>'Biasiswa 3','C4'=>'Biasiswa 3'],
                'B4' => ['C1'=>'Biasiswa 3','C2'=>'Biasiswa 3','C3'=>'Biasiswa 4','C4'=>'Biasiswa 4'],
            ],
            'A3' => [
                'B1' => ['C1'=>'Biasiswa 2','C2'=>'Biasiswa 2','C3'=>'Biasiswa 3','C4'=>'Biasiswa 3'],
                'B3' => ['C1'=>'Biasiswa 3','C2'=>'Biasiswa 3','C3'=>'Biasiswa 4','C4'=>'Biasiswa 4'],
                'B4' => ['C1'=>'Biasiswa 4','C2'=>'Biasiswa 4','C3'=>'Biasiswa 4','C4'=>'Biasiswa 4'],
            ],
            'A4' => [
                'B1' => ['C1'=>'Biasiswa 3','C2'=>'Biasiswa 3','C3'=>'Biasiswa 3','C4'=>'Biasiswa 4'],
                'B3' => ['C1'=>'Biasiswa 4','C2'=>'Biasiswa 4','C3'=>'Biasiswa 4','C4'=>'Biasiswa 4'],
                'B4' => ['C1'=>'Biasiswa 4','C2'=>'Biasiswa 4','C3'=>'Biasiswa 4','C4'=>'Biasiswa 4'],
            ],
        ];

        foreach ($map as $a => $bSet) {
            foreach ($bSet as $b => $cSet) {
                foreach ($cSet as $c => $biasiswa) {
                    $rules[] = $this->rule(
                        'biasiswa_full',
                        "{$a}|{$b}|{$c}",
                        null,
                        null,
                        $biasiswa
                    );
                }
            }
        }

        /* =========================
           5️⃣ PARTIAL & SINGLE RULES
        ========================= */
        foreach (['A1'=>'Biasiswa 1','A2'=>'Biasiswa 2','A3'=>'Biasiswa 3','A4'=>'Biasiswa 4'] as $a=>$b) {
            $rules[] = $this->rule('biasiswa_single', $a, null, null, $b);
        }

        foreach (['B1'=>'Biasiswa 1','B3'=>'Biasiswa 3','B4'=>'Biasiswa 4'] as $i=>$b) {
            $rules[] = $this->rule('biasiswa_single', $i, null, null, $b);
        }

        foreach (['C1','C2','C3','C4'] as $c) {
            $rules[] = $this->rule('biasiswa_single', $c, null, null, 'Biasiswa 2');
        }

        DB::table('scholarship_rules')->insert($rules);
    }

    private function rule($type, $keyword, $min, $max, $result)
    {
        return [
            'rule_type' => $type,
            'keyword' => $keyword,
            'min_value' => $min,
            'max_value' => $max,
            'result' => $result,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
