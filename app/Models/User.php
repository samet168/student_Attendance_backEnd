<?php

namespace App\Models;

// use Illuminate\Foundation\Auth\User as Authenticatable;
use MongoDB\Laravel\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $connection = 'mongodb';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'token'
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'token'
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function classrooms()
    {
        return $this->belongsToMany(
            Classroom::class,
            'classroom_teacher',
            'teacher_id',
            'classroom_id'
        );
    }
}