<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReservacionMesa extends Model
{
    protected $table = 'cadena.reservacion_mesa';
    public $timestamps = false;

    protected $fillable = [
        'reservacion_id',
        'mesa_sucursal_id',
        'periodo',
        'bloquea',
    ];

    protected $casts = [
        'bloquea' => 'boolean',
    ];

    public function reservacion(): BelongsTo
    {
        return $this->belongsTo(Reservacion::class, 'reservacion_id');
    }

    public function mesaSucursal(): BelongsTo
    {
        return $this->belongsTo(MesaSucursal::class, 'mesa_sucursal_id');
    }
}