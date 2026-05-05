<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Model;

use MongoDB\Laravel\Eloquent\Model;
use MongoDB\Laravel\Relations\BelongsTo;
use MongoDB\Laravel\Relations\HasMany;
class Student extends Model
{
    protected $connection = "mongodb";
    protected $collection = "students";
    protected $fillable = [
        'classroom_id',
        'student_id_card',
        'name',
        'gender',
        'phone'
    ];
    public function attendances() {
        return $this->hasMany(Attendance::class);
    }
   
    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }
}
