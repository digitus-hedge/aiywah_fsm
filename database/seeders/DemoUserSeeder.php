<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DemoUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@smartsr.test'],
            [
                'name' => 'Super Admin',
                'email' => 'admin@smartsr.test',
                'password' => Hash::make('password'),
            ]
        );
    }
}