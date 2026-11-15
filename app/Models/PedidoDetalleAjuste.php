<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PedidoDetalleAjuste extends Model
{
    protected $table = 'cadena.pedido_detalle_ajuste';
    public $timestamps = false;

    protected $fillable = [
        'pedido_detalle_id',
        'tipo',
        'cantidad',
        'motivo',
        'descripcion',
        'destino',
        'usuario_id',
        'fecha_hora',
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'fecha_hora' => 'datetime',
    ];

    public function detalle(): BelongsTo
    {
        return $this->belongsTo(PedidoDetalle::class, 'pedido_detalle_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}