<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Platillo extends Model
{
    protected $table = 'cadena.platillo';
    public $timestamps = true;

    protected $fillable = [
        'categoria_id',
        'temporada_id',
        'nombre',
        'descripcion',
        'precio',
        'imagen_url',
        'estatus',
        'disponible',
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'disponible' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(CategoriaPlatillo::class, 'categoria_id');
    }

    public function temporada(): BelongsTo
    {
        return $this->belongsTo(Temporada::class, 'temporada_id');
    }

    public function ingredientes(): BelongsToMany
    {
        return $this->belongsToMany(Ingrediente::class, 'cadena.platillo_ingrediente', 'platillo_id', 'ingrediente_id');
    }

    public function alergenos(): BelongsToMany
    {
        return $this->belongsToMany(Alergia::class, 'cadena.platillo_alergeno', 'platillo_id', 'alergia_id');
    }

    public function sucursales(): HasMany
    {
        return $this->hasMany(PlatilloSucursal::class, 'platillo_id');
    }

    public function detallesPedido(): HasMany
    {
        return $this->hasMany(PedidoDetalle::class, 'platillo_id');
    }
}