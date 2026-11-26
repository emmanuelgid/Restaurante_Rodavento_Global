<?php

namespace App\Support;

use App\Models\Sucursal;

class SucursalActiva
{
    public static function id(): ?int
    {
        $user = auth()->user();

        if (!$user || !$user->can('sucursales.admin')) {
            return null;
        }

        $id = session('admin_sucursal_id');

        if (!$id) {
            return null;
        }

        $existe = Sucursal::where('id', (int) $id)
            ->where('activa', true)
            ->exists();

        if (!$existe) {
            session()->forget('admin_sucursal_id');
            return null;
        }

        return (int) $id;
    }

    public static function actual(): ?Sucursal
    {
        $id = self::id();
        return $id ? Sucursal::find($id) : null;
    }

    public static function nombre(): string
    {
        return self::actual()?->nombre ?? 'Todas las sucursales';
    }
}
