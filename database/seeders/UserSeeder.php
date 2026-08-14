<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Admin Sistem',
                'email' => 'admin@ticketing.test',
                'phone' => '6281111111111',
                'role' => 'admin',
            ],
            [
                'name' => 'Citra (CS)',
                'email' => 'cs@ticketing.test',
                'phone' => '6281222222222',
                'role' => 'cs',
            ],
            [
                'name' => 'Budi (Manager)',
                'email' => 'manager@ticketing.test',
                'phone' => '6281333333333',
                'role' => 'manager',
            ],
            [
                'name' => 'Andi (Teknisi)',
                'email' => 'teknisi@ticketing.test',
                'phone' => '6281444444444',
                'role' => 'teknisi',
            ],
            [
                'name' => 'Rudi (Teknisi)',
                'email' => 'teknisi2@ticketing.test',
                'phone' => '6281555555555',
                'role' => 'teknisi',
            ],
        ];

        foreach ($users as $u) {
            $user = User::firstOrCreate(
                ['email' => $u['email']],
                [
                    'name' => $u['name'],
                    'phone' => $u['phone'],
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ]
            );

            if (! $user->hasRole($u['role'])) {
                $user->assignRole($u['role']);
            }
        }
    }
}
