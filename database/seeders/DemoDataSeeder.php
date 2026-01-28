<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use App\Models\ParentModel;
use App\Models\Student;
use App\Models\BillType;
use App\Models\Bill;
use App\Models\SchoolSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // Create demo users
        $adminRole = Role::where('name', 'admin')->first();
        $orangtuaRole = Role::where('name', 'orangtua')->first();
        $kepalaSekolahRole = Role::where('name', 'kepala_sekolah')->first();
        $bendaharaRole = Role::where('name', 'bendahara')->first();

        // Admin
        $admin = User::updateOrCreate(
            ['email' => 'admin@bundakasih.sch.id'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
                'phone' => '081234567890',
                'is_active' => true,
            ]
        );
        $admin->roles()->sync([$adminRole->id]);

        // Kepala Sekolah
        $kepsek = User::updateOrCreate(
            ['email' => 'kepsek@bundakasih.sch.id'],
            [
                'name' => 'Drs. Ahmad Sudrajat, M.Pd.',
                'password' => Hash::make('password'),
                'phone' => '081234567891',
                'is_active' => true,
            ]
        );
        $kepsek->roles()->sync([$kepalaSekolahRole->id]);

        // Bendahara
        $bendahara = User::updateOrCreate(
            ['email' => 'bendahara@bundakasih.sch.id'],
            [
                'name' => 'Siti Rahayu, S.E.',
                'password' => Hash::make('password'),
                'phone' => '081234567892',
                'is_active' => true,
            ]
        );
        $bendahara->roles()->sync([$bendaharaRole->id]);

        // Create demo students
        $students = [
            ['nis' => '2024001', 'nisn' => '0012345678', 'name' => 'Ahmad Rizki', 'gender' => 'L', 'place_of_birth' => 'Makassar', 'date_of_birth' => '2010-05-15', 'address' => 'Jl. Sudiang Raya No. 10', 'class' => 'VII-A', 'status' => 'active'],
            ['nis' => '2024002', 'nisn' => '0012345679', 'name' => 'Putri Amelia', 'gender' => 'P', 'place_of_birth' => 'Makassar', 'date_of_birth' => '2010-08-22', 'address' => 'Jl. Perintis Kemerdekaan No. 25', 'class' => 'VII-A', 'status' => 'active'],
            ['nis' => '2024003', 'nisn' => '0012345680', 'name' => 'Budi Santoso', 'gender' => 'L', 'place_of_birth' => 'Makassar', 'date_of_birth' => '2010-03-10', 'address' => 'Jl. Urip Sumoharjo No. 55', 'class' => 'VII-B', 'status' => 'active'],
            ['nis' => '2024004', 'nisn' => '0012345681', 'name' => 'Dewi Lestari', 'gender' => 'P', 'place_of_birth' => 'Makassar', 'date_of_birth' => '2009-11-08', 'address' => 'Jl. Veteran Selatan No. 12', 'class' => 'VIII-A', 'status' => 'active'],
            ['nis' => '2024005', 'nisn' => '0012345682', 'name' => 'Rendi Pratama', 'gender' => 'L', 'place_of_birth' => 'Makassar', 'date_of_birth' => '2009-07-20', 'address' => 'Jl. Rappocini Raya No. 33', 'class' => 'VIII-B', 'status' => 'active'],
        ];

        foreach ($students as $studentData) {
            Student::updateOrCreate(['nis' => $studentData['nis']], $studentData);
        }

        // Create parent users and link to students
        $parentData = [
            ['email' => 'orangtua1@gmail.com', 'name' => 'Bapak Ahmad', 'student_nis' => '2024001', 'relationship' => 'ayah'],
            ['email' => 'orangtua2@gmail.com', 'name' => 'Ibu Amelia', 'student_nis' => '2024002', 'relationship' => 'ibu'],
            ['email' => 'orangtua3@gmail.com', 'name' => 'Bapak Santoso', 'student_nis' => '2024003', 'relationship' => 'ayah'],
        ];

        foreach ($parentData as $data) {
            $user = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('password'),
                    'phone' => '08' . rand(1000000000, 9999999999),
                    'is_active' => true,
                ]
            );
            $user->roles()->sync([$orangtuaRole->id]);

            $parent = ParentModel::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'name' => $data['name'],
                    'relationship' => $data['relationship'],
                    'phone' => $user->phone,
                    'address' => 'Makassar',
                ]
            );

            $student = Student::where('nis', $data['student_nis'])->first();
            if ($student) {
                $parent->students()->syncWithoutDetaching([$student->id]);
            }
        }

        // Create bill types
        $billTypes = [
            ['name' => 'SPP Bulanan', 'description' => 'Sumbangan Pembinaan Pendidikan bulanan', 'amount' => 500000, 'is_recurring' => true, 'recurring_period' => 'monthly', 'is_active' => true],
            ['name' => 'Uang Bangunan', 'description' => 'Biaya pengembangan fasilitas sekolah', 'amount' => 2500000, 'is_recurring' => false, 'is_active' => true],
            ['name' => 'Uang Seragam', 'description' => 'Biaya seragam sekolah lengkap', 'amount' => 750000, 'is_recurring' => false, 'is_active' => true],
            ['name' => 'Uang Kegiatan', 'description' => 'Biaya kegiatan ekstrakurikuler', 'amount' => 200000, 'is_recurring' => true, 'recurring_period' => 'semester', 'is_active' => true],
            ['name' => 'Uang Ujian', 'description' => 'Biaya pelaksanaan ujian', 'amount' => 150000, 'is_recurring' => true, 'recurring_period' => 'semester', 'is_active' => true],
        ];

        foreach ($billTypes as $typeData) {
            BillType::updateOrCreate(['name' => $typeData['name']], $typeData);
        }

        // Create sample bills for students
        $sppType = BillType::where('name', 'SPP Bulanan')->first();
        $allStudents = Student::where('status', 'active')->get();
        $months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni'];

        foreach ($allStudents as $student) {
            foreach ($months as $index => $month) {
                $dueDate = now()->startOfYear()->addMonths($index)->endOfMonth();
                $status = $index < 3 ? 'paid' : ($index < 5 ? 'pending' : 'overdue');
                $paidAmount = $status === 'paid' ? $sppType->amount : 0;

                Bill::updateOrCreate(
                    [
                        'student_id' => $student->id,
                        'bill_type_id' => $sppType->id,
                        'month' => $month,
                        'academic_year' => '2025/2026',
                    ],
                    [
                        'invoice_number' => 'INV' . date('Ymd') . str_pad($student->id, 3, '0', STR_PAD_LEFT) . str_pad($index + 1, 2, '0', STR_PAD_LEFT),
                        'amount' => $sppType->amount,
                        'total_amount' => $sppType->amount,
                        'paid_amount' => $paidAmount,
                        'due_date' => $dueDate,
                        'status' => $status,
                        'created_by' => $bendahara->id,
                    ]
                );
            }
        }

        // Create school settings
        $settings = [
            ['key' => 'school_name', 'value' => 'SMP Bunda Kasih Sudiang', 'type' => 'string', 'group' => 'general'],
            ['key' => 'school_address', 'value' => 'Jl. Sudiang Raya No. 100, Makassar', 'type' => 'string', 'group' => 'general'],
            ['key' => 'school_phone', 'value' => '(0411) 123456', 'type' => 'string', 'group' => 'general'],
            ['key' => 'school_email', 'value' => 'info@bundakasih.sch.id', 'type' => 'string', 'group' => 'general'],
            ['key' => 'academic_year', 'value' => '2025/2026', 'type' => 'string', 'group' => 'academic'],
            ['key' => 'late_fee_percentage', 'value' => '5', 'type' => 'integer', 'group' => 'payment'],
            ['key' => 'grace_period_days', 'value' => '7', 'type' => 'integer', 'group' => 'payment'],
        ];

        foreach ($settings as $setting) {
            SchoolSetting::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
