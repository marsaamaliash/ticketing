<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            CategorySeeder::class,
            UserSeeder::class,
        ]);

        if (app()->environment('local', 'testing')) {
            $this->call([
                DemoTicketSeeder::class,
            ]);
        }
    }
}
