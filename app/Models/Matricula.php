<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Matricula extends Model
{
    use HasFactory;

    // Aseguramos el nombre de la tabla (por si acaso)
    protected $table = 'matriculas';

    protected $fillable = [
        'ficha_id',
        'aprendiz_id',
        'estado',
    ];

    /* Relaciones */
    public function ficha()
    {
        return $this->belongsTo(Ficha::class, 'ficha_id');
    }

    public function aprendiz()
    {
        return $this->belongsTo(User::class, 'aprendiz_id');
    }
}