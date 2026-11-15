<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReglaPuntos extends Model
{
    protected $table = 'cadena.regla_puntos';
    public $timestamps = false;

    protected $fillable = [
        'monto_por_punto',
        'vigencia_inicio',
        'vigencia_fin',
        'activa',
    ];

    protected $casts = [
        'monto_por_punto' => 'decimal:2',
        'vigencia_inicio' => 'date',
        'vigencia_fin' => 'date',
        'activa' => 'boolean',
    ];

    public function movimientos(): HasMany
    {
        return $this->hasMany(MovimientoPuntos::class, 'regla_id');
    }
}