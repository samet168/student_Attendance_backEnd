<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    //
   protected $fillable = ['name', 'room_number'];
   public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }


    public function student()
    {
        return $this->belongsTo(Student::class);
    }
    
}
