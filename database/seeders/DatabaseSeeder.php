<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Call all seeders
        $this->call([
            AdminUserSeeder::class,
            ScholarshipSeeder::class,
        ]);
    }
}