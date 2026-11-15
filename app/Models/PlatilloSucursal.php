<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlatilloSucursal extends Model
{
    protected $table = 'cadena.platillo_sucursal';
    public $timestamps = false;

    protected $fillable = [
        'sucursal_id',
        'platillo_id',
        'precio_local',
        'disponible',
    ];

    protected $casts = [
        'precio_local' => 'decimal:2',
        'disponible' => 'boolean',
    ];

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_id');
    }

    public function platillo(): BelongsTo
    {
        return $this->belongsTo(Platillo::class, 'platillo_id');
    }
}