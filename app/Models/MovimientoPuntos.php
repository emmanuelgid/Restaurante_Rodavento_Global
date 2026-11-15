<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MovimientoPuntos extends Model
{
    protected $table = 'cadena.movimiento_puntos';
    public $timestamps = false;

    protected $fillable = [
        'cliente_id',
        'nota_reservacion_id',
        'regla_id',
        'puntos',
        'tipo',
        'fecha',
    ];

    protected $casts = [
        'puntos' => 'integer',
        'fecha' => 'datetime',
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function notaReservacion(): BelongsTo
    {
        return $this->belongsTo(NotaReservacion::class, 'nota_reservacion_id');
    }

    public function regla(): BelongsTo
    {
        return $this->belongsTo(ReglaPuntos::class, 'regla_id');
    }
}