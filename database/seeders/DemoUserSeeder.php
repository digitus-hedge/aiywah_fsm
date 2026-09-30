<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DemoUserSeeder extends Seeder
{
   
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@mattermind.ae'],
            [
                'name'     => 'Super Admin',
                'password' => Hash::make('service@mattermind'),
                'role_id'  => 1,
            ]
        );

        User::updateOrCreate(
            ['email' => 'shilpavava998@gmail.com'],   
            [
                'name'     => 'Shilpa',            
                'password' => Hash::make('password'),
                'role_id'  => 2,                     
            ]
        );
    }
}