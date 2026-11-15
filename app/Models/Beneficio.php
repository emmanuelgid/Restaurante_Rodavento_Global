<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Beneficio extends Model
{
    protected $table = 'cadena.beneficio';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'descripcion',
        'vigencia_ini',
        'vigencia_fin',
    ];

    protected $casts = [
        'vigencia_ini' => 'date',
        'vigencia_fin' => 'date',
    ];

    public function nivelesLealtad(): BelongsToMany
    {
        return $this->belongsToMany(NivelLealtad::class, 'cadena.nivel_beneficio', 'beneficio_id', 'nivel_lealtad_id');
    }
}