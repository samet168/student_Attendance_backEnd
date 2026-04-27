<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'student_id',
        'classroom_id',      // ← ប្តូរពី subject_id ទៅ classroom_id
        'user_id',
        'attendance_date',
        'status',
        'remarks',
        'time_in',           // បើប្រើ
        'time_out'           // បើប្រើ
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    // ប្តូរពី subject() ទៅ classroom()
    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    // រក្សាទុក subject() ចាស់បើចង់ប្រើប្រាស់ទិន្នន័យចាស់ (ជម្រើស)
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}