<?php

namespace Qlixea\PaymentHub;

use Illuminate\Support\ServiceProvider;
use Qlixea\PaymentHub\Commands\InstallQlixeaCommand;

class QlixeaServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/qlixea.php', 'qlixea');
    }

    public function boot()
    {
        // Publicar configuración
        $this->publishes([
            __DIR__ . '/../config/qlixea.php' => config_path('qlixea.php'),
        ], 'qlixea-config');

        // Publicar vistas (Botón y Modal)
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'qlixea');
        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/qlixea'),
        ], 'qlixea-views');

        // Registrar comando de instalación
        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallQlixeaCommand::class,
            ]);
        }
    }
}