<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ficha extends Model
{
    use HasFactory;

    protected $fillable = ['nombre','numero_ficha','duracion_meses'];

    protected $casts = [
        'duracion_meses' => 'integer',
    ];
}