<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActividadAprendiz extends Model
{
    use HasFactory;
    protected $fillable = [
        'actividad_id',
        'aprendiz_id',
    ];

    // Relación con Actividad
    public function actividad()
    {
        return $this->belongsTo(Actividad::class, 'actividad_id');
    }

    // Relación con Aprendiz (User)
    public function aprendiz()
    {
        return $this->belongsTo(User::class, 'aprendiz_id');
    }
}
