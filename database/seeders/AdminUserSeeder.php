<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        // Create admin user
        User::create([
            'name' => 'Admin',
            'email' => 'admin@scholarease.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin'
        ]);
        
        // Create regular user
        User::create([
            'name' => 'User',
            'email' => 'user@scholarease.com',
            'password' => Hash::make('user123'),
            'role' => 'user'
        ]);
        
        $this->command->info('Admin user created: admin@scholarease.com / admin123');
        $this->command->info('Regular user created: user@scholarease.com / user123');
    }
}