<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('admin')->insert([
            'name' => 'Mark Reyes',
            'email' => 'markreyes@iskolarngbayan.edu.ph',
            'password_hash' => Hash::make('Admin123!') 
        ]);

        DB::table('section')->insert([
            ['section_name' => 'BSIT 3-1', 'admin_id' => 1],
            ['section_name' => 'BSIT 3-2', 'admin_id' => 1],
            ['section_name' => 'BSIT 3-3', 'admin_id' => 1],
            ['section_name' => 'BSIT 3-4', 'admin_id' => 1],
        ]);
    }
}