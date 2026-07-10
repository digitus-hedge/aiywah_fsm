<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

     public function run(): void
    {
        // Roles MUST seed before permissions (pivot references role ids).
        $this->call([
            RoleSeeder::class,
            Permissionseeder::class,
            DemoUserSeeder::class,
        ]);

        $this->call(WhatsappTemplateSeeder::class);
    }
}
