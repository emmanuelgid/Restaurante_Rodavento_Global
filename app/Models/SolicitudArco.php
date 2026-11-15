<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SolicitudArco extends Model
{
    protected $table = 'cadena.solicitud_arco';
    public $timestamps = false;

    protected $fillable = [
        'cliente_id',
        'tipo',
        'estatus',
        'fecha_solicitud',
        'fecha_resolucion',
        'notas',
    ];

    protected $casts = [
        'fecha_solicitud' => 'datetime',
        'fecha_resolucion' => 'datetime',
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function historial(): HasMany
    {
        return $this->hasMany(SolicitudArcoHistorial::class, 'solicitud_id');
    }
}