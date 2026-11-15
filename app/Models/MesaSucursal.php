<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MesaSucursal extends Model
{
    protected $table = 'cadena.mesa_sucursal';
    public $timestamps = false;

    protected $fillable = [
        'sucursal_id',
        'mesa_id',
    ];

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_id');
    }

    public function mesa(): BelongsTo
    {
        return $this->belongsTo(Mesa::class, 'mesa_id');
    }

    public function reservacionesMesa(): HasMany
    {
        return $this->hasMany(ReservacionMesa::class, 'mesa_sucursal_id');
    }
}