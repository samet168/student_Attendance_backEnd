<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    //
protected $fillable = [
    'student_id',
    'subject_id',
    'user_id',
    'attendance_date',
    'status',
    'remarks'
];

    // public function student() {
    // return $this->belongsTo(Student::class);
    // }

    // public function subject() {
    //     return $this->belongsTo(Subject::class);
    // }
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
