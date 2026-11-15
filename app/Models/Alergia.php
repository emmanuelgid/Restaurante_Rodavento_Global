<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Alergia extends Model
{
    protected $table = 'cadena.alergia';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    public function clientes(): BelongsToMany
    {
        return $this->belongsToMany(Cliente::class, 'cadena.cliente_alergia', 'alergia_id', 'cliente_id');
    }

    public function platillos(): BelongsToMany
    {
        return $this->belongsToMany(Platillo::class, 'cadena.platillo_alergeno', 'alergia_id', 'platillo_id');
    }
}