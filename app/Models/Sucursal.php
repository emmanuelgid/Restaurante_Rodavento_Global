<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sucursal extends Model
{
    protected $table = 'cadena.sucursal';
    public $timestamps = true;

    protected $fillable = [
        'nombre',
        'direccion',
        'telefono',
        'activa',
    ];

    protected $casts = [
        'activa' => 'boolean',
    ];

    public function mesas(): HasMany
    {
        return $this->hasMany(MesaSucursal::class, 'sucursal_id');
    }

    public function empleados(): HasMany
    {
        return $this->hasMany(EmpleadoSucursal::class, 'sucursal_id');
    }

    public function platillos(): HasMany
    {
        return $this->hasMany(PlatilloSucursal::class, 'sucursal_id');
    }

    public function notasReservacion(): HasMany
    {
        return $this->hasMany(NotaReservacion::class, 'sucursal_id');
    }
}