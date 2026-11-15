<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeveridadAlergia extends Model
{
    protected $table = 'cadena.severidad_alergia';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'prioridad',
    ];

    protected $casts = [
        'prioridad' => 'integer',
    ];
}