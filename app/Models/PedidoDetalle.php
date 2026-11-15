<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PedidoDetalle extends Model
{
    protected $table = 'cadena.pedido_detalle';
    public $timestamps = false;

    protected $fillable = [
        'pedido_id',
        'platillo_id',
        'cantidad',
        'cantidad_cancelada',
        'precio_unitario',
        'es_cortesia',
        'notas',
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'cantidad_cancelada' => 'integer',
        'precio_unitario' => 'decimal:2',
        'es_cortesia' => 'boolean',
    ];

    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Pedido::class, 'pedido_id');
    }

    public function platillo(): BelongsTo
    {
        return $this->belongsTo(Platillo::class, 'platillo_id');
    }

    public function ajustes(): HasMany
    {
        return $this->hasMany(PedidoDetalleAjuste::class, 'pedido_detalle_id');
    }

    public function getSubtotalAttribute(): float
    {
        $efectiva = max(0, $this->cantidad - $this->cantidad_cancelada);
        return $this->es_cortesia ? 0.0 : (float) ($efectiva * $this->precio_unitario);
    }
}