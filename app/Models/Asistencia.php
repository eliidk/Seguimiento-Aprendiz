<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asistencia extends Model
{
    use HasFactory;
    protected $fillable = [
    'matricula_id',
    'instructor_id',
    'estado',
    'nota',
];
    public function detalles()
    {
        return $this->hasMany(Asistencia::class);
    }

    public function ficha()
    {
        return $this->belongsTo(Ficha::class);
    }

    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }
}
