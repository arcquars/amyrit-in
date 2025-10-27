<?php

namespace Amyrit\SiatBoliviaClient;

use Amyrit\SiatBoliviaClient\Internal\SoapClientFactory;
use Amyrit\SiatBoliviaClient\Services\ServicioFacturacion;
use Amyrit\SiatBoliviaClient\Services\ServicioSincronizacion;
// ... Import other services

/**
 * Main Client for SIAT services.
 * Acts as a facade/factory to access different SIAT service clients.
 */
class SiatClient
{
    protected SoapClientFactory $clientFactory;

    /**
     * @param SiatConfig $config The configuration DTO.
     */
    public function __construct(
        public readonly SiatConfig $config
    ) {
        $this->clientFactory = new SoapClientFactory($this->config);
    }

    /**
     * Access the Invoicing Service client.
     *
     * @return ServicioFacturacion
     */
    public function facturacion(): ServicioFacturacion
    {
        // Lazily instantiates the service client
        return new ServicioFacturacion($this->config, $this->clientFactory);
    }

    /**
     * Access the Synchronization Service client.
     *
     * @return ServicioSincronizacion
     */
    public function sincronizacion(): ServicioSincronizacion
    {
        return new ServicioSincronizacion($this->config, $this->clientFactory);
    }

    // ... Add factory methods for all other services
    // public function operaciones(): ServicioOperaciones
    // { ... }
}

