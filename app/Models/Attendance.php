<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Model;
use MongoDB\Laravel\Eloquent\Model;
use MongoDB\Laravel\Relations\BelongsTo;
use MongoDB\Laravel\Relations\HasMany;
class Attendance extends Model

{
    protected $connection = "mongodb";
    // protected $collection = "attendances";
    protected $fillable = [
        'student_id',
        'classroom_id',      
        'user_id',
        'subject_id',
        'attendance_date',
        'status',
        'remarks',
        'time_in',           
        'time_out'          
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    
    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
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