<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SolicitudArcoHistorial extends Model
{
    protected $table = 'cadena.solicitud_arco_historial';
    public $timestamps = false;

    protected $fillable = [
        'solicitud_id',
        'usuario_id',
        'estatus_anterior',
        'estatus_nuevo',
        'fecha',
    ];

    protected $casts = [
        'fecha' => 'datetime',
    ];

    public function solicitud(): BelongsTo
    {
        return $this->belongsTo(SolicitudArco::class, 'solicitud_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}