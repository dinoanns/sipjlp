<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CsPermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // Lembar Kerja CS
            'lembar-kerja-cs.create',
            'lembar-kerja-cs.view-self',
            'lembar-kerja-cs.view-unit',
            'lembar-kerja-cs.view-all',
            'lembar-kerja-cs.validate',

            // Jadwal CS (shift & bulanan)
            'jadwal-cs.manage',

            // Master Pekerjaan CS
            'cs.pekerjaan.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $pjlp = Role::findByName('pjlp');
        $pjlp->givePermissionTo([
            'lembar-kerja-cs.create',
            'lembar-kerja-cs.view-self',
        ]);

        $koordinator = Role::findByName('koordinator');
        $koordinator->givePermissionTo([
            'lembar-kerja-cs.view-unit',
            'lembar-kerja-cs.validate',
            'jadwal-cs.manage',
            'cs.pekerjaan.manage',
        ]);

        $admin = Role::findByName('admin');
        $admin->givePermissionTo([
            'lembar-kerja-cs.create',
            'lembar-kerja-cs.view-all',
            'lembar-kerja-cs.validate',
            'jadwal-cs.manage',
            'cs.pekerjaan.manage',
        ]);

        $manajemen = Role::findByName('manajemen');
        $manajemen->givePermissionTo([
            'lembar-kerja-cs.view-all',
        ]);

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
