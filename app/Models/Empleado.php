<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Empleado extends Model
{
    protected $table = 'cadena.empleado';
    public $timestamps = false;

    protected $fillable = [
        'nombres',
        'apellidos',
        'telefono',
        'fecha_ingreso',
        'estatus',
        'puesto',
    ];

    protected $casts = [
        'fecha_ingreso' => 'date',
        'created_at' => 'datetime',
    ];

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'empleado_id');
    }

    public function sucursales(): BelongsToMany
    {
        return $this->belongsToMany(Sucursal::class, 'cadena.empleado_sucursal', 'empleado_id', 'sucursal_id');
    }

    public function notasReservacion(): HasMany
    {
        return $this->hasMany(NotaReservacion::class, 'empleado_id');
    }

    public function getNombreCompletoAttribute(): string
    {
        return "{$this->nombres} {$this->apellidos}";
    }
}