<?php

namespace Amyrit\SiatBoliviaClient\Laravel;

use Illuminate\Support\Facades\Facade;
use Amyrit\SiatBoliviaClient\SiatClient;

/**
 * @method static \Amyrit\SiatBoliviaClient\Services\ServicioFacturacion facturacion()
 * @method static \Amyrit\SiatBoliviaClient\Services\ServicioSincronizacion sincronizacion()
 * @method static \Amyrit\SiatBoliviaClient\SiatConfig getConfig()
 *
 * @see \Amyrit\SiatBoliviaClient\SiatClient
 */
class SiatFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return SiatClient::class; // Binds to the class registration
    }
}
