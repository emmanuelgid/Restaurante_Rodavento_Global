<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class NivelLealtad extends Model
{
    protected $table = 'cadena.nivel_lealtad';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'puntos_minimos',
    ];

    protected $casts = [
        'puntos_minimos' => 'integer',
    ];

    public function clientes(): HasMany
    {
        return $this->hasMany(Cliente::class, 'nivel_lealtad_id');
    }

    public function beneficios(): BelongsToMany
    {
        return $this->belongsToMany(Beneficio::class, 'cadena.nivel_beneficio', 'nivel_lealtad_id', 'beneficio_id');
    }
}