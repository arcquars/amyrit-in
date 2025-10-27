<?php

namespace Amyrit\SiatBoliviaClient\Laravel;

use Illuminate\Support\ServiceProvider;
use Amyrit\SiatBoliviaClient\SiatClient;
use Amyrit\SiatBoliviaClient\SiatConfig;

class SiatServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Define the config file path
        $configPath = __DIR__ . '/../../config/siat.php';

        // Merge config
        $this->mergeConfigFrom($configPath, 'siat');

        // Bind the SiatClient as a singleton in the service container
        $this->app->singleton(SiatClient::class, function ($app) {
            $config = $app['config']['siat'];

            // Create the SiatConfig DTO from Laravel's config
            $siatConfig = new SiatConfig(
                codigoSistema: $config['codigo_sistema'],
                nit: $config['nit'],
                apiKey: $config['api_key'],
                modalidad: $config['modalidad'],
                ambiente: $config['ambiente'],
                cuis: $config['cuis'] ?? null, // Allow dynamic credentials
                cufd: $config['cufd'] ?? null, // Allow dynamic credentials
                soapTimeout: $config['soap_timeout'] ?? 5
            );

            return new SiatClient($siatConfig);
        });

        // Register the Facade alias
        $this->app->alias(SiatClient::class, 'siat');
    }

    public function boot(): void
    {
        // Allow publishing the config file
        $this->publishes([
            __DIR__ . '/../../config/siat.php' => config_path('siat.php'),
        ], 'config');
    }
}
