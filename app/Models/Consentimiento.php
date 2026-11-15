<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Consentimiento extends Model
{
    protected $table = 'cadena.consentimiento';
    public $timestamps = false;

    protected $fillable = [
        'cliente_id',
        'tipo',
        'aceptado',
        'fecha',
        'version_aviso',
    ];

    protected $casts = [
        'aceptado' => 'boolean',
        'fecha' => 'datetime',
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }
}