<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mesa extends Model
{
    protected $table = 'cadena.mesa';
    public $timestamps = false;

    protected $fillable = [
        'codigo_mesa',
        'capacidad',
        'activa',
    ];

    protected $casts = [
        'capacidad' => 'integer',
        'activa' => 'boolean',
    ];

    public function mesasSucursal(): HasMany
    {
        return $this->hasMany(MesaSucursal::class, 'mesa_id');
    }
}