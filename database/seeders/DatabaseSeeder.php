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
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. បង្កើត Users (Admin និង គ្រូ)
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Teacher Sok',
            'email' => 'sok@gmail.com',
            'password' => Hash::make('password123'),
            'role' => 'teacher',
        ]);

        // 2. បង្កើត Classrooms (ថ្នាក់រៀន)
        $class1 = Classroom::create(['name' => 'Year 3 - IT', 'room_number' => 'A-101']);
        $class2 = Classroom::create(['name' => 'Year 3 - MIS', 'room_number' => 'A-102']);

        // 3. បង្កើត Subjects (មុខវិជ្ជា)
        Subject::create(['subject_name' => 'System Analysis and Design', 'subject_code' => 'SAD101']);
        Subject::create(['subject_name' => 'Web Development (Laravel)', 'subject_code' => 'WEB202']);
        Subject::create(['subject_name' => 'Network Configuration', 'subject_code' => 'NET303']);

        // 4. បង្កើត Students (សិស្ស)
        // សិស្សក្នុងថ្នាក់ទី១
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

        // សិស្សក្នុងថ្នាក់ទី២
        Student::create([
            'classroom_id' => $class2->id,
            'student_id_card' => 'ST003',
            'name' => 'Vannak Mongkul',
            'gender' => 'Male',
            'phone' => '011223344',
        ]);
    }
}