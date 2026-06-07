<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Buat roles Spatie
        $roleAdmin     = Role::firstOrCreate(['name' => 'admin',     'guard_name' => 'web']);
        $rolePetugas   = Role::firstOrCreate(['name' => 'petugas',   'guard_name' => 'web']);
        $rolePelanggan = Role::firstOrCreate(['name' => 'pelanggan', 'guard_name' => 'web']);

        $users = [
            [
                'name'     => 'Administrator',
                'email'    => 'admin@toyaamerta.id',
                'phone'    => '081234567890',
                'role'     => 'admin',
                'spatie'   => $roleAdmin,
            ],
            [
                'name'     => 'Budi Santoso',
                'email'    => 'petugas@toyaamerta.id',
                'phone'    => '081234567891',
                'role'     => 'petugas',
                'spatie'   => $rolePetugas,
            ],
            [
                'name'     => 'Siti Rahayu',
                'email'    => 'pelanggan@toyaamerta.id',
                'phone'    => '081234567892',
                'role'     => 'pelanggan',
                'spatie'   => $rolePelanggan,
            ],
        ];

        foreach ($users as $data) {
            $user = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name'              => $data['name'],
                    'phone'             => $data['phone'],
                    'role'              => $data['role'],
                    'is_active'         => 1,
                    'password'          => bcrypt('password'),
                    'email_verified_at' => now(),
                ]
            );
            $user->syncRoles($data['spatie']);
        }

        $this->command->info('✅ Users seeded:');
        $this->command->table(
            ['Name', 'Email', 'Role', 'Password'],
            [
                ['Administrator', 'admin@toyaamerta.id',     'admin',     'password'],
                ['Budi Santoso',  'petugas@toyaamerta.id',   'petugas',   'password'],
                ['Siti Rahayu',   'pelanggan@toyaamerta.id', 'pelanggan', 'password'],
            ]
        );
    }
}
