<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Insert admin FIRST (section depends on admin)
        DB::table('admin')->insert([
            'name' => 'Mark Reyes',
            'email' => 'markreyes@iskolarngbayan.edu.ph',
            'password_hash' => '2y12$LQv3c1yqBWVHxkd0L6D3eOGWj1aXh9gK0K1w6Q4p4n5j4e6f8sQ2',
        ]);

        // Then sections
        DB::table('section')->insert([
            ['section_name' => 'BSIT 3-1', 'admin_id' => 1],
            ['section_name' => 'BSIT 3-2', 'admin_id' => 1],
            ['section_name' => 'BSIT 3-3', 'admin_id' => 1],
            ['section_name' => 'BSIT 3-4', 'admin_id' => 1],
        ]);
    }
}