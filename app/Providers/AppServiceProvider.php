<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Connectors\PostgresConnector;

use App\Models\User;
use Illuminate\Support\Facades\Gate;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Solución definitiva para inyectar el Endpoint de Neon en Windows
        DB::resolverFor('pgsql', function ($connection, $database, $prefix, $config) {
            $connector = new class extends PostgresConnector {
                protected function getDsn(array $config) {
                    $dsn = parent::getDsn($config);
                    if (!str_contains($dsn, 'options=')) {
                        $dsn .= ";options='endpoint=ep-crimson-darkness-b8n99ji8'";
                    }
                    return $dsn;
                }
            };

            $pdo = $connector->connect($config);
            return new \Illuminate\Database\PostgresConnection($pdo, $database, $prefix, $config);
        });

        Gate::before(function (User $user, string $ability) {
            if ($user->tienePermiso($ability)) {
                return true;
            }

            return null;
        });

    }
}
