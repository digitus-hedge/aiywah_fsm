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
            ['email' => 'admin@mattermind.ae'],
            [
                'name' => 'Super Admin',
                'email' => 'admin@mattermind.ae',
                'password' => Hash::make('service@mattermind'),
                'role_id'   => '1',
            ]
        );
    }
}