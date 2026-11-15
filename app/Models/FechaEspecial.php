<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FechaEspecial extends Model
{
    protected $table = 'cadena.fecha_especial';
    public $timestamps = false;

    protected $fillable = [
        'cliente_id',
        'tipo',
        'fecha',
        'descripcion',
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }
}