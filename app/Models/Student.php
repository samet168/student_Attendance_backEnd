<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    //
    // protected $fillable = ['classroom_id', 'student_id_card', 'name', 'gender', 'phone'];
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
