<?php

namespace Database\Seeders;

use App\Models\ProjectTrust;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Project;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create an Admin User
        User::updateOrCreate(
            ['email' => 'admin@test.com',],
            [
            'name' => 'System Admin',
            'password' => Hash::make('secret'),
            'role' => 'admin',
        ]);

        // 2. Create an Evaluator User
        $evaluator = User::updateOrCreate(
            ['email' => 'evaluator@test.com'],
            [
            'name' => 'John Evaluator',
            'password' => Hash::make('secret'),
            'role' => 'evaluator',
        ]);
//        $thrusts = [
//            'Food Security & Sustainable Agriculture',
//            'Environmental Management & Climate Change',
//            'Renewable Energy & Technology Innovation',
//            'Socio-Economic & Educational Development Innovation'
//        ];
//
//        //Transform the flat array into database rows mapping to your column name
//        $insertData = array_map(function ($thrust) {
//            return [
//                'name'       => $thrust,  // 🌟 Replace 'name' with your actual table column name (e.g., 'title')
//                'created_at' => now(),    // Raw inserts require explicit timestamps
//                'updated_at' => now(),
//            ];
//        }, $thrusts);
//
//        ProjectTrust::query()->in($insertData);

    }
}
