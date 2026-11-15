<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Ingrediente extends Model
{
    protected $table = 'cadena.ingrediente';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    public function platillos(): BelongsToMany
    {
        return $this->belongsToMany(Platillo::class, 'cadena.platillo_ingrediente', 'ingrediente_id', 'platillo_id');
    }
}