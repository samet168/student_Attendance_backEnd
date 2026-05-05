<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ======================
        // 1. USERS
        // ======================
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        $teacher1 = User::create([
            'name' => 'Teacher Sok',
            'email' => 'sok@gmail.com',
            'password' => Hash::make('password123'),
            'role' => 'teacher',
        ]);

        $teacher2 = User::create([
            'name' => 'Teacher Lina',
            'email' => 'lina@gmail.com',
            'password' => Hash::make('password123'),
            'role' => 'teacher',
        ]);

        // ======================
        // 2. CLASSROOMS (NO teacher_id anymore)
        // ======================
        $class1 = Classroom::create([
            'name' => 'Year 3 - IT',
            'room_number' => 'A-101',
        ]);

        $class2 = Classroom::create([
            'name' => 'Year 3 - MIS',
            'room_number' => 'A-102',
        ]);

        $class3 = Classroom::create([
            'name' => 'Year 2 - Networking',
            'room_number' => 'B-201',
        ]);

        // ======================
        // 🔥 ATTACH TEACHERS (PIVOT TABLE)
        // ======================

        $class1->teachers()->attach([$teacher1->id, $teacher2->id]);
        $class2->teachers()->attach([$teacher2->id]);
        $class3->teachers()->attach([$teacher1->id]);

        // ======================
        // 3. SUBJECTS
        // ======================
        Subject::create([
            'subject_name' => 'Laravel Web',
            'subject_code' => 'WEB101',
        ]);

        Subject::create([
            'subject_name' => 'Database',
            'subject_code' => 'DB202',
        ]);

        // ======================
        // 4. STUDENTS
        // ======================
        Student::create([
            'classroom_id' => $class1->id,
            'student_id_card' => 'ST001',
            'name' => 'Chan Tola',
            'gender' => 'Male',
            'phone' => '012345678',
        ]);

        Student::create([
            'classroom_id' => $class1->id,
            'student_id_card' => 'ST002',
            'name' => 'Keo Sreyneang',
            'gender' => 'Female',
            'phone' => '098765432',
        ]);

        Student::create([
            'classroom_id' => $class2->id,
            'student_id_card' => 'ST003',
            'name' => 'Vannak Mongkul',
            'gender' => 'Male',
            'phone' => '011223344',
        ]);

        $this->command->info("✅ Seeder completed successfully");
    }
}