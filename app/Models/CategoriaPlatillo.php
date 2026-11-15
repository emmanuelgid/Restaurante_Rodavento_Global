<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CategoriaPlatillo extends Model
{
    protected $table = 'cadena.categoria_platillo';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    public function platillos(): HasMany
    {
        return $this->hasMany(Platillo::class, 'categoria_id');
    }
}