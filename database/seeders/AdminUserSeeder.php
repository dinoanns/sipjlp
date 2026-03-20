<?php

namespace Database\Seeders;

use App\Enums\UnitType;
use App\Models\Pjlp;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Create Admin
        $admin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@sipjlp.rsudcipayung.id',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $admin->assignRole('admin');

        // Create Manajemen
        $manajemen = User::create([
            'name' => 'Kepala RS',
            'email' => 'manajemen@sipjlp.rsudcipayung.id',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $manajemen->assignRole('manajemen');

        // Create Koordinator Security
        $koordinatorSecurity = User::create([
            'name' => 'Koordinator Security',
            'email' => 'koordinator.security@sipjlp.rsudcipayung.id',
            'password' => Hash::make('password'),
            'unit' => UnitType::SECURITY,
            'is_active' => true,
        ]);
        $koordinatorSecurity->assignRole('koordinator');

        // Create Koordinator Cleaning
        $koordinatorCleaning = User::create([
            'name' => 'Koordinator Cleaning Service',
            'email' => 'koordinator.cs@sipjlp.rsudcipayung.id',
            'password' => Hash::make('password'),
            'unit' => UnitType::CLEANING,
            'is_active' => true,
        ]);
        $koordinatorCleaning->assignRole('koordinator');

        // Create sample PJLP Security
        $pjlpSecurity1 = User::create([
            'name' => 'Ahmad Security',
            'email' => 'ahmad.security@sipjlp.rsudcipayung.id',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $pjlpSecurity1->assignRole('pjlp');

        Pjlp::create([
            'user_id' => $pjlpSecurity1->id,
            'nip' => 'SEC-001',
            'nama' => 'Ahmad Security',
            'unit' => UnitType::SECURITY,
            'jabatan' => 'Security Guard',
            'no_telp' => '08123456789',
            'tanggal_bergabung' => '2023-01-01',
            'status' => 'aktif',
        ]);

        $pjlpSecurity2 = User::create([
            'name' => 'Budi Security',
            'email' => 'budi.security@sipjlp.rsudcipayung.id',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $pjlpSecurity2->assignRole('pjlp');

        Pjlp::create([
            'user_id' => $pjlpSecurity2->id,
            'nip' => 'SEC-002',
            'nama' => 'Budi Security',
            'unit' => UnitType::SECURITY,
            'jabatan' => 'Security Guard',
            'no_telp' => '08123456790',
            'tanggal_bergabung' => '2023-02-01',
            'status' => 'aktif',
        ]);

        // Create sample PJLP Cleaning
        $pjlpCleaning1 = User::create([
            'name' => 'Siti Cleaning',
            'email' => 'siti.cleaning@sipjlp.rsudcipayung.id',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $pjlpCleaning1->assignRole('pjlp');

        Pjlp::create([
            'user_id' => $pjlpCleaning1->id,
            'nip' => 'CS-001',
            'nama' => 'Siti Cleaning',
            'unit' => UnitType::CLEANING,
            'jabatan' => 'Cleaning Staff',
            'no_telp' => '08123456791',
            'tanggal_bergabung' => '2023-01-15',
            'status' => 'aktif',
        ]);

        $pjlpCleaning2 = User::create([
            'name' => 'Dewi Cleaning',
            'email' => 'dewi.cleaning@sipjlp.rsudcipayung.id',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $pjlpCleaning2->assignRole('pjlp');

        Pjlp::create([
            'user_id' => $pjlpCleaning2->id,
            'nip' => 'CS-002',
            'nama' => 'Dewi Cleaning',
            'unit' => UnitType::CLEANING,
            'jabatan' => 'Cleaning Staff',
            'no_telp' => '08123456792',
            'tanggal_bergabung' => '2023-03-01',
            'status' => 'aktif',
        ]);
    }
}
