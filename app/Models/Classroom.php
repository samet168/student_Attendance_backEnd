<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Model;
use MongoDB\Laravel\Eloquent\Model;
use MongoDB\Laravel\Relations\BelongsTo;
use MongoDB\Laravel\Relations\HasMany;
class Classroom extends Model

{
    //
    protected $connection = "mongodb";
    // protected $collection = "classrooms";

    protected $fillable = [
        'name',
        'room_number',
        'user_id',
        'teacher_id'
    ];

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }
    // 👤 Owner
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    // 👥 Students
    public function students()
    {
        return $this->hasMany(Student::class);
    }
    public function teachers()
    {
        return $this->belongsToMany(User::class, 'classroom_teacher', 'classroom_id', 'teacher_id');
    }
}
        

    

