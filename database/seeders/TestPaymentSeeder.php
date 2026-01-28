<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use App\Models\ParentModel;
use App\Models\Student;
use App\Models\BillType;
use App\Models\Bill;
use App\Models\Payment;
use App\Models\Notification;
use App\Models\ActivityLog;
use App\Models\PaymentReconciliation;
use App\Models\ReconciliationItem;
use App\Models\SchoolSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class TestPaymentSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing data except roles and permissions
        $this->command->info('Menghapus data lama...');
        
        // Disable foreign key checks
        DB::statement('PRAGMA foreign_keys = OFF');
        
        // Delete in order of dependencies
        ReconciliationItem::truncate();
        PaymentReconciliation::truncate();
        Payment::truncate();
        Bill::truncate();
        Notification::truncate();
        ActivityLog::truncate();
        
        // Delete parent-student pivot
        DB::table('parent_student')->truncate();
        
        // Delete parents
        ParentModel::truncate();
        
        // Delete students
        Student::truncate();
        
        // Delete users except keep roles intact
        // Keep only admin, kepala_sekolah, bendahara
        $orangtuaRole = Role::where('name', 'orangtua')->first();
        if ($orangtuaRole) {
            User::whereHas('roles', function($q) use ($orangtuaRole) {
                $q->where('role_id', $orangtuaRole->id);
            })->delete();
        }
        
        // Re-enable foreign key checks
        DB::statement('PRAGMA foreign_keys = ON');

        $this->command->info('Membuat data testing...');

        // Get roles
        $orangtuaRole = Role::where('name', 'orangtua')->first();
        $bendaharaRole = Role::where('name', 'bendahara')->first();
        $bendahara = User::whereHas('roles', fn($q) => $q->where('role_id', $bendaharaRole->id))->first();

        // Create 4 students with different education levels
        $students = [
            [
                'nis' => 'TK2024001',
                'nisn' => '0098765001',
                'name' => 'Aisyah Putri',
                'gender' => 'P',
                'place_of_birth' => 'Makassar',
                'date_of_birth' => '2020-03-15',
                'address' => 'Jl. Sudiang Raya No. 10, Makassar',
                'class' => 'TK-A',
                'education_level' => 'TK',
                'status' => 'active',
            ],
            [
                'nis' => 'SD2024001',
                'nisn' => '0098765002',
                'name' => 'Muhammad Rizky',
                'gender' => 'L',
                'place_of_birth' => 'Makassar',
                'date_of_birth' => '2016-07-22',
                'address' => 'Jl. Perintis Kemerdekaan No. 25, Makassar',
                'class' => 'III-A',
                'education_level' => 'SD',
                'status' => 'active',
            ],
            [
                'nis' => 'SMP2024001',
                'nisn' => '0098765003',
                'name' => 'Dewi Safitri',
                'gender' => 'P',
                'place_of_birth' => 'Makassar',
                'date_of_birth' => '2012-11-08',
                'address' => 'Jl. Urip Sumoharjo No. 55, Makassar',
                'class' => 'VII-A',
                'education_level' => 'SMP',
                'status' => 'active',
            ],
            [
                'nis' => 'SMA2024001',
                'nisn' => '0098765004',
                'name' => 'Rendi Pratama',
                'gender' => 'L',
                'place_of_birth' => 'Makassar',
                'date_of_birth' => '2009-05-20',
                'address' => 'Jl. Veteran Selatan No. 12, Makassar',
                'class' => 'X-IPA',
                'education_level' => 'SMA',
                'status' => 'active',
            ],
        ];

        // Create parents for each student
        $parentsData = [
            [
                'email' => 'orangtua.aisyah@gmail.com',
                'name' => 'Ibu Fatimah',
                'phone' => '081234567001',
                'relationship' => 'ibu',
            ],
            [
                'email' => 'orangtua.rizky@gmail.com',
                'name' => 'Bapak Ahmad',
                'phone' => '081234567002',
                'relationship' => 'ayah',
            ],
            [
                'email' => 'orangtua.dewi@gmail.com',
                'name' => 'Ibu Sari',
                'phone' => '081234567003',
                'relationship' => 'ibu',
            ],
            [
                'email' => 'orangtua.rendi@gmail.com',
                'name' => 'Bapak Budiman',
                'phone' => '081234567004',
                'relationship' => 'ayah',
            ],
        ];

        foreach ($students as $index => $studentData) {
            // Create student
            $student = Student::create($studentData);
            $this->command->info("Siswa dibuat: {$student->name} ({$student->education_level} - {$student->class})");

            // Create parent user
            $parentInfo = $parentsData[$index];
            $user = User::create([
                'name' => $parentInfo['name'],
                'email' => $parentInfo['email'],
                'password' => Hash::make('password'),
                'phone' => $parentInfo['phone'],
                'is_active' => true,
            ]);
            $user->roles()->attach($orangtuaRole->id);

            // Create parent profile
            $parent = ParentModel::create([
                'user_id' => $user->id,
                'name' => $parentInfo['name'],
                'relationship' => $parentInfo['relationship'],
                'phone' => $parentInfo['phone'],
                'address' => $studentData['address'],
            ]);

            // Link parent to student
            $parent->students()->attach($student->id);

            $this->command->info("  -> Orang tua: {$parent->name} ({$user->email})");
        }

        // Create/ensure bill types exist
        $billTypes = [
            ['name' => 'SPP Bulanan', 'description' => 'Sumbangan Pembinaan Pendidikan bulanan', 'amount' => 500000, 'is_recurring' => true, 'recurring_period' => 'monthly', 'is_active' => true],
            ['name' => 'Uang Bangunan', 'description' => 'Biaya pengembangan fasilitas sekolah', 'amount' => 2500000, 'is_recurring' => false, 'is_active' => true],
        ];

        foreach ($billTypes as $typeData) {
            BillType::updateOrCreate(['name' => $typeData['name']], $typeData);
        }

        // Create bills for each student (SPP for current month only)
        $sppType = BillType::where('name', 'SPP Bulanan')->first();
        $allStudents = Student::where('status', 'active')->get();

        foreach ($allStudents as $student) {
            Bill::create([
                'student_id' => $student->id,
                'bill_type_id' => $sppType->id,
                'invoice_number' => 'INV' . date('Ymd') . str_pad($student->id, 4, '0', STR_PAD_LEFT),
                'amount' => $sppType->amount,
                'total_amount' => $sppType->amount,
                'paid_amount' => 0,
                'month' => 'Januari',
                'academic_year' => '2025/2026',
                'due_date' => now()->addDays(7),
                'status' => 'pending',
                'created_by' => $bendahara ? $bendahara->id : 1,
            ]);
            $this->command->info("  -> Tagihan SPP dibuat untuk: {$student->name}");
        }

        // Summary
        $this->command->info('');
        $this->command->info('===========================================');
        $this->command->info('DATA TESTING BERHASIL DIBUAT');
        $this->command->info('===========================================');
        $this->command->info('');
        $this->command->info('Akun Orang Tua (password: password):');
        $this->command->info('  1. orangtua.aisyah@gmail.com (TK)');
        $this->command->info('  2. orangtua.rizky@gmail.com (SD)');
        $this->command->info('  3. orangtua.dewi@gmail.com (SMP)');
        $this->command->info('  4. orangtua.rendi@gmail.com (SMA)');
        $this->command->info('');
        $this->command->info('Masing-masing memiliki 1 tagihan SPP Rp 500.000');
        $this->command->info('===========================================');
    }
}
