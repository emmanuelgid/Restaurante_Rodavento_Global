<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Temporada extends Model
{
    protected $table = 'cadena.temporada';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'fecha_inicio',
        'fecha_final',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_final' => 'date',
    ];

    public function platillos(): HasMany
    {
        return $this->hasMany(Platillo::class, 'temporada_id');
    }
}