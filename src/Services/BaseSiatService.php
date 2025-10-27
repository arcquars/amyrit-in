<?php

namespace Amyrit\SiatBoliviaClient\Services;

use Amyrit\SiatBoliviaClient\Internal\SoapClientFactory;
use Amyrit\SiatBoliviaClient\SiatConfig;

/**
 * Abstract base class for all SIAT service clients.
 * It provides shared dependencies like the configuration and the client factory.
 */
abstract class BaseSiatService
{
    /**
     * The specific WSDL service name for this class.
     * @var string
     */
    protected string $serviceName = '';

    /**
     * @param SiatConfig $config The shared configuration DTO.
     * @param SoapClientFactory $clientFactory The factory for creating SoapClient instances.
     */
    public function __construct(
        protected SiatConfig $config,
        protected SoapClientFactory $clientFactory
    ) {
    }

    /**
     * Gets the WSDL service name.
     * @return string
     */
    public function getServiceName(): string
    {
        return $this->serviceName;
    }
}
