<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Preferencia extends Model
{
    protected $table = 'cadena.preferencia';
    public $timestamps = false;

    protected $fillable = [
        'tipo',
        'descripcion',
    ];

    public function clientes(): BelongsToMany
    {
        return $this->belongsToMany(Cliente::class, 'cadena.cliente_preferencia', 'preferencia_id', 'cliente_id');
    }
}