<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $superAdminRole = Role::where('name', 'super_admin')->first();
        $adminRole = Role::where('name', 'admin')->first();
        $judgeRole = Role::where('name', 'judge')->first();
        $advocateRole = Role::where('name', 'advocate')->first();
        $studentRole = Role::where('name', 'student')->first();
        $publicRole = Role::where('name', 'public')->first();

        // 1. Super Admin
        $superAdmin = User::updateOrCreate(
            ['email' => 'admin@lawuniverse.com'],
            [
                'user_code' => '012205243',
                'first_name' => 'Global Matrix',
                'last_name' => 'Solution Inc.',
                'name' => 'Global Matrix Solution Inc.',
                'password' => Hash::make('password'),
                'dob' => '1985-05-24',
                'education' => 'Master of Laws (LL.M.)',
                'role' => 'super_admin',
                'status' => 'active',
                'country' => 'India',
                'address' => 'Corporate Legal Tower, New Delhi',
                'mobile_number' => '8965896698',
                'about' => 'Chief Platform Administrator for Law Universe Portal.',
                'receive_welcome_letter' => true,
                'terms_accepted' => true,
                'enable_2fa' => false,
                'enable_instant_otp' => true,
                'email_verified_at' => now(),
                'approved_at' => now(),
            ]
        );
        if ($superAdminRole) {
            $superAdmin->roles()->syncWithoutDetaching([$superAdminRole->id]);
        }

        // 2. Active Judge
        $judgeActive = User::updateOrCreate(
            ['email' => 'judge@lawuniverse.com'],
            [
                'user_code' => '0516092411',
                'first_name' => 'Justice',
                'last_name' => 'R. Sharma',
                'name' => 'Justice R. Sharma',
                'password' => Hash::make('password'),
                'dob' => '1975-08-16',
                'education' => 'Ph.D. in Law',
                'role' => 'judge',
                'status' => 'active',
                'country' => 'India',
                'address' => 'High Court Judicial Enclave',
                'mobile_number' => '9811223344',
                'about' => 'Senior Judicial Officer and Legal Jurisprudence Consultant.',
                'receive_welcome_letter' => true,
                'terms_accepted' => true,
                'enable_2fa' => false,
                'enable_instant_otp' => true,
                'email_verified_at' => now(),
                'approved_at' => now(),
                'approved_by' => $superAdmin->id,
            ]
        );
        if ($judgeRole) {
            $judgeActive->roles()->syncWithoutDetaching([$judgeRole->id]);
        }

        // 3. Pending Approval Judge
        $judgePending = User::updateOrCreate(
            ['email' => 'pending.judge@lawuniverse.com'],
            [
                'user_code' => '0516092412',
                'first_name' => 'Virendra',
                'last_name' => 'Verma',
                'name' => 'Virendra Verma',
                'password' => Hash::make('password'),
                'dob' => '1980-04-12',
                'education' => 'Master of Laws (LL.M.)',
                'role' => 'judge',
                'status' => 'pending',
                'country' => 'India',
                'address' => 'District Court Complex, Lucknow',
                'mobile_number' => '9877001122',
                'about' => 'District Magistrate & Additional Sessions Judge applicant.',
                'receive_welcome_letter' => true,
                'terms_accepted' => true,
                'enable_2fa' => false,
                'enable_instant_otp' => true,
                'email_verified_at' => now(),
                'approved_at' => null,
            ]
        );
        if ($judgeRole) {
            $judgePending->roles()->syncWithoutDetaching([$judgeRole->id]);
        }

        // 4. Active Advocate
        $advocateActive = User::updateOrCreate(
            ['email' => 'advocate@lawuniverse.com'],
            [
                'user_code' => '0509092411',
                'first_name' => 'Sohan Singh',
                'last_name' => 'Chandel',
                'name' => 'Sohan Singh Chandel',
                'password' => Hash::make('password'),
                'dob' => '1990-09-05',
                'education' => 'Bachelor of Laws (LL.B.)',
                'role' => 'advocate',
                'status' => 'active',
                'country' => 'India',
                'address' => 'Chamber 402, Supreme Court Bar Association',
                'mobile_number' => '9988776655',
                'about' => 'Supreme Court Advocate specializing in Constitutional and Criminal Law.',
                'receive_welcome_letter' => true,
                'terms_accepted' => true,
                'enable_2fa' => false,
                'enable_instant_otp' => true,
                'email_verified_at' => now(),
                'approved_at' => now(),
                'approved_by' => $superAdmin->id,
            ]
        );
        if ($advocateRole) {
            $advocateActive->roles()->syncWithoutDetaching([$advocateRole->id]);
        }

        // 5. Pending Approval Advocate
        $advocatePending = User::updateOrCreate(
            ['email' => 'pending.advocate@lawuniverse.com'],
            [
                'user_code' => '0509092412',
                'first_name' => 'Aarav',
                'last_name' => 'Mehta',
                'name' => 'Aarav Mehta',
                'password' => Hash::make('password'),
                'dob' => '1994-06-18',
                'education' => 'Bachelor of Laws (LL.B.)',
                'role' => 'advocate',
                'status' => 'pending',
                'country' => 'India',
                'address' => 'Civil Lines, Prayagraj',
                'mobile_number' => '9822334455',
                'about' => 'High Court Bar enrolled advocate awaiting panel verification.',
                'receive_welcome_letter' => true,
                'terms_accepted' => true,
                'enable_2fa' => false,
                'enable_instant_otp' => true,
                'email_verified_at' => now(),
                'approved_at' => null,
            ]
        );
        if ($advocateRole) {
            $advocatePending->roles()->syncWithoutDetaching([$advocateRole->id]);
        }

        // 6. Active Student (Auto-approved)
        $student = User::updateOrCreate(
            ['email' => 'student@lawuniverse.com'],
            [
                'user_code' => '0509092413',
                'first_name' => 'Ishu',
                'last_name' => 'Gupta',
                'name' => 'Ishu Gupta',
                'password' => Hash::make('password'),
                'dob' => '2002-03-21',
                'education' => 'Undergraduate Law (BA LL.B.)',
                'role' => 'student',
                'status' => 'active',
                'country' => 'India',
                'address' => 'National Law University Campus',
                'mobile_number' => '8965896698',
                'about' => 'Aspiring Judicial Services candidate preparing for competitive exams.',
                'receive_welcome_letter' => true,
                'terms_accepted' => true,
                'enable_2fa' => false,
                'enable_instant_otp' => true,
                'email_verified_at' => now(),
                'approved_at' => now(),
            ]
        );
        if ($studentRole) {
            $student->roles()->syncWithoutDetaching([$studentRole->id]);
        }

        // 7. Active Public User (Auto-approved)
        $public = User::updateOrCreate(
            ['email' => 'public@lawuniverse.com'],
            [
                'user_code' => '0509092414',
                'first_name' => 'Khushi',
                'last_name' => 'Sharma',
                'name' => 'Khushi Sharma',
                'password' => Hash::make('password'),
                'dob' => '1998-12-14',
                'education' => 'Graduate',
                'role' => 'public',
                'status' => 'active',
                'country' => 'India',
                'address' => 'Sector 62, Noida',
                'mobile_number' => '9518290198',
                'about' => 'Legal enthusiast exploring constitutional rights and consumer protection.',
                'receive_welcome_letter' => true,
                'terms_accepted' => true,
                'enable_2fa' => false,
                'enable_instant_otp' => true,
                'email_verified_at' => now(),
                'approved_at' => now(),
            ]
        );
        if ($publicRole) {
            $public->roles()->syncWithoutDetaching([$publicRole->id]);
        }
    }
}
