<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Create Roles
        $roles = [
            ['name' => 'admin', 'display_name' => 'Administrator', 'description' => 'Full access to all system features'],
            ['name' => 'orangtua', 'display_name' => 'Orang Tua', 'description' => 'Can view and pay student bills'],
            ['name' => 'kepala_sekolah', 'display_name' => 'Kepala Sekolah', 'description' => 'Can view all reports'],
            ['name' => 'bendahara', 'display_name' => 'Bendahara', 'description' => 'Manage bills and payments'],
        ];

        foreach ($roles as $roleData) {
            Role::updateOrCreate(['name' => $roleData['name']], $roleData);
        }

        // Create Permissions
        $permissions = [
            // User Management
            ['name' => 'view_users', 'display_name' => 'Lihat User', 'module' => 'users'],
            ['name' => 'create_users', 'display_name' => 'Tambah User', 'module' => 'users'],
            ['name' => 'edit_users', 'display_name' => 'Edit User', 'module' => 'users'],
            ['name' => 'delete_users', 'display_name' => 'Hapus User', 'module' => 'users'],

            // Role Management
            ['name' => 'view_roles', 'display_name' => 'Lihat Role', 'module' => 'roles'],
            ['name' => 'create_roles', 'display_name' => 'Tambah Role', 'module' => 'roles'],
            ['name' => 'edit_roles', 'display_name' => 'Edit Role', 'module' => 'roles'],
            ['name' => 'delete_roles', 'display_name' => 'Hapus Role', 'module' => 'roles'],

            // Student Management
            ['name' => 'view_students', 'display_name' => 'Lihat Siswa', 'module' => 'students'],
            ['name' => 'create_students', 'display_name' => 'Tambah Siswa', 'module' => 'students'],
            ['name' => 'edit_students', 'display_name' => 'Edit Siswa', 'module' => 'students'],
            ['name' => 'delete_students', 'display_name' => 'Hapus Siswa', 'module' => 'students'],

            // Bill Management
            ['name' => 'view_bills', 'display_name' => 'Lihat Tagihan', 'module' => 'bills'],
            ['name' => 'create_bills', 'display_name' => 'Buat Tagihan', 'module' => 'bills'],
            ['name' => 'edit_bills', 'display_name' => 'Edit Tagihan', 'module' => 'bills'],
            ['name' => 'delete_bills', 'display_name' => 'Hapus Tagihan', 'module' => 'bills'],

            // Payment Management
            ['name' => 'view_payments', 'display_name' => 'Lihat Pembayaran', 'module' => 'payments'],
            ['name' => 'process_payments', 'display_name' => 'Proses Pembayaran', 'module' => 'payments'],

            // Reports
            ['name' => 'view_reports', 'display_name' => 'Lihat Laporan', 'module' => 'reports'],
            ['name' => 'export_reports', 'display_name' => 'Export Laporan', 'module' => 'reports'],

            // Reconciliation
            ['name' => 'view_reconciliation', 'display_name' => 'Lihat Rekonsiliasi', 'module' => 'reconciliation'],
            ['name' => 'run_reconciliation', 'display_name' => 'Jalankan Rekonsiliasi', 'module' => 'reconciliation'],

            // Settings
            ['name' => 'manage_settings', 'display_name' => 'Kelola Pengaturan', 'module' => 'settings'],
        ];

        foreach ($permissions as $permData) {
            Permission::updateOrCreate(['name' => $permData['name']], $permData);
        }

        // Assign permissions to roles
        $admin = Role::where('name', 'admin')->first();
        $admin->permissions()->sync(Permission::all()->pluck('id'));

        $bendahara = Role::where('name', 'bendahara')->first();
        $bendahara->permissions()->sync(
            Permission::whereIn('module', ['bills', 'payments', 'reports', 'reconciliation', 'students'])
                ->pluck('id')
        );

        $kepalaSekolah = Role::where('name', 'kepala_sekolah')->first();
        $kepalaSekolah->permissions()->sync(
            Permission::whereIn('name', ['view_reports', 'export_reports', 'view_payments', 'view_students'])
                ->pluck('id')
        );

        $orangtua = Role::where('name', 'orangtua')->first();
        $orangtua->permissions()->sync(
            Permission::whereIn('name', ['view_payments', 'process_payments'])
                ->pluck('id')
        );
    }
}
