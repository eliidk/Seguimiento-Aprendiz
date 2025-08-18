<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FichaInstructor extends Model
{
    use HasFactory;

    protected $table = 'ficha_instructor';

    protected $fillable = [
        'ficha_id',
        'instructor_id',
    ];
}
