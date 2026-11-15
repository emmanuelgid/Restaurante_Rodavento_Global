<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NotaReservacion extends Model
{
    protected $table = 'cadena.nota_reservacion';
    public $timestamps = false;

    protected $fillable = [
        'sucursal_id',
        'reservacion_id',
        'empleado_id',
        'subtotal',
        'descuento',
        'impuestos',
        'propina',
        'total',
        'calificacion',
        'comentarios',
        'fecha_emision',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'descuento' => 'decimal:2',
        'impuestos' => 'decimal:2',
        'propina' => 'decimal:2',
        'total' => 'decimal:2',
        'calificacion' => 'integer',
        'fecha_emision' => 'datetime',
    ];

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_id');
    }

    public function reservacion(): BelongsTo
    {
        return $this->belongsTo(Reservacion::class, 'reservacion_id');
    }

    public function empleado(): BelongsTo
    {
        return $this->belongsTo(Empleado::class, 'empleado_id');
    }

    public function pedidos(): HasMany
    {
        return $this->hasMany(Pedido::class, 'nota_reservacion_id');
    }

    public function movimientosPuntos(): HasMany
    {
        return $this->hasMany(MovimientoPuntos::class, 'nota_reservacion_id');
    }
}