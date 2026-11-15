<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Reservacion extends Model
{
    protected $table = 'cadena.reservacion';
    public $timestamps = false;

    protected $fillable = [
        'cliente_id',
        'fecha_hora_inicio',
        'fecha_hora_final',
        'cantidad_personas',
        'estatus',
        'notas',
    ];

    protected $casts = [
        'fecha_hora_inicio' => 'datetime',
        'fecha_hora_final' => 'datetime',
        'cantidad_personas' => 'integer',
        'created_at' => 'datetime',
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function mesasAsignadas(): HasMany
    {
        return $this->hasMany(ReservacionMesa::class, 'reservacion_id');
    }

    public function nota(): HasOne
    {
        return $this->hasOne(NotaReservacion::class, 'reservacion_id');
    }
}