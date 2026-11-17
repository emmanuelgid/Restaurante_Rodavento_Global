<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $table = 'cadena.users';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'empleado_id',
        'rol_id',
        'activo',
        'ultimo_acceso',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'activo' => 'boolean',
            'ultimo_acceso' => 'datetime',
        ];
    }

    public function empleado(): BelongsTo
    {
        return $this->belongsTo(Empleado::class, 'empleado_id');
    }

    public function rol(): BelongsTo
    {
        return $this->belongsTo(Rol::class, 'rol_id');
    }

    public function bitacoras(): HasMany
    {
        return $this->hasMany(Bitacora::class, 'usuario_id');
    }

    //Metodos para roles y permisos para cada rol

    /**
     * Verifica si el usuario está activo y si su rol tiene el permiso solicitado.
     */
    public function tienePermiso(string $codigoPermiso): bool
    {
        // Si el usuario está desactivado o no tiene rol asignado, denegar acceso
        if (!$this->activo || !$this->rol) {
            return false;
        }

        // Carga los permisos del rol una sola vez en memoria (evita consultas repetidas)
        $this->rol->loadMissing('permisos');

        return $this->rol->permisos->contains('codigo', $codigoPermiso);
    }

    /**
     * (Opcional) Verifica si el usuario tiene alguno de los roles indicados por nombre.
     */
    public function tieneRol(array $roles): bool
    {
        if (!$this->activo || !$this->rol) {
            return false;
        }

        return in_array($this->rol->nombre, $roles);
    }

}
