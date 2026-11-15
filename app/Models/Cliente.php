<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Cliente extends Model
{
    protected $table = 'cadena.cliente';
    public $timestamps = false;

    protected $fillable = [
        'nombres',
        'apellidos',
        'telefono',
        'correo',
        'fecha_nacimiento',
        'fecha_alta',
        'sucursal_alta_id',
        'nivel_lealtad_id',
        'puntos_saldo',
        'puntos_acumulados',
        'tratamiento_bloqueado',
        'activo',
        'updated_at',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
        'fecha_alta' => 'datetime',
        'updated_at' => 'datetime',
        'puntos_saldo' => 'integer',
        'puntos_acumulados' => 'integer',
        'tratamiento_bloqueado' => 'boolean',
        'activo' => 'boolean',
    ];

    public function sucursalAlta(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_alta_id');
    }

    public function nivelLealtad(): BelongsTo
    {
        return $this->belongsTo(NivelLealtad::class, 'nivel_lealtad_id');
    }

    public function reservaciones(): HasMany
    {
        return $this->hasMany(Reservacion::class, 'cliente_id');
    }

    public function movimientosPuntos(): HasMany
    {
        return $this->hasMany(MovimientoPuntos::class, 'cliente_id');
    }

    public function fechasEspeciales(): HasMany
    {
        return $this->hasMany(FechaEspecial::class, 'cliente_id');
    }

    public function consentimientos(): HasMany
    {
        return $this->hasMany(Consentimiento::class, 'cliente_id');
    }

    public function solicitudesArco(): HasMany
    {
        return $this->hasMany(SolicitudArco::class, 'cliente_id');
    }

    public function alergias(): BelongsToMany
    {
        return $this->belongsToMany(Alergia::class, 'cadena.cliente_alergia', 'cliente_id', 'alergia_id')
            ->withPivot('severidad_id', 'notas');
    }

    public function preferencias(): BelongsToMany
    {
        return $this->belongsToMany(Preferencia::class, 'cadena.cliente_preferencia', 'cliente_id', 'preferencia_id')
            ->withPivot('notas');
    }

    public function getNombreCompletoAttribute(): string
    {
        return "{$this->nombres} {$this->apellidos}";
    }
}