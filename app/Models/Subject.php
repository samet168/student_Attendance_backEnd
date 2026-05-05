<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Model;
use MongoDB\Laravel\Eloquent\Model;
use MongoDB\Laravel\Relations\BelongsTo;
use MongoDB\Laravel\Relations\HasMany;
class Subject extends Model
{
    //
    protected $connection = "mongodb";
    // protected $collection = "subjects";
    protected $fillable = [
    'subject_name',
    'subject_code'
];
}
