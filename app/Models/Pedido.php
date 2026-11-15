<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pedido extends Model
{
    protected $table = 'cadena.pedido';
    public $timestamps = false;

    protected $fillable = [
        'nota_reservacion_id',
        'estatus',
        'fecha_hora',
    ];

    protected $casts = [
        'fecha_hora' => 'datetime',
    ];

    public function notaReservacion(): BelongsTo
    {
        return $this->belongsTo(NotaReservacion::class, 'nota_reservacion_id');
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(PedidoDetalle::class, 'pedido_id');
    }
}