<?php

namespace Amyrit\SiatBoliviaClient\Internal;

use SoapClient;
use SoapFault;
use Amyrit\SiatBoliviaClient\Exceptions\SiatException;
use Amyrit\SiatBoliviaClient\SiatConfig;

/**
 * Handles the creation and configuration of SoapClient instances.
 * This class is for internal use by the library.
 */
class SoapClientFactory
{
    /**
     * A cache for instantiated SoapClient objects.
     * @var SoapClient[]
     */
    private array $clientCache = [];

    public function __construct(
        private readonly SiatConfig $config
    ) {
    }

    /**
     * Get or create a SoapClient for a specific SIAT service.
     *
     * @param string $serviceName The class name of the service (e.g., 'ServicioFacturacionSiat')
     * @return SoapClient
     * @throws SiatException
     */
    public function getClient(string $serviceName): SoapClient
    {
        if (isset($this->clientCache[$serviceName])) {
            return $this->clientCache[$serviceName];
        }

        $wsdlUrl = $this->config->getWsdlUrl($serviceName);
        if (!$wsdlUrl) {
            throw new SiatException("WSDL URL for service '$serviceName' not found in configuration.");
        }

        try {
            $client = new SoapClient($wsdlUrl, [
                'stream_context' => stream_context_create([
                    'http' => [
                        'header' => "apikey: {$this->config->apiKey}",
                        'timeout' => $this->config->soapTimeout,
                    ]
                ]),
                'cache_wsdl'   => WSDL_CACHE_NONE,
                'compression'  => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP | SOAP_COMPRESSION_DEFLATE,
                'encoding'     => 'UTF-8',
                'soap_version' => SOAP_1_1, // SIAT uses SOAP 1.1
                'exceptions'   => true, // Throw SoapFault exceptions
                'trace'        => $this->config->ambiente === SiatConfig::AMBIENTE_PRUEBAS, // Enable trace only in tests
            ]);

            $this->clientCache[$serviceName] = $client;
            return $client;

        } catch (SoapFault $e) {
            throw new SiatException("Failed to create SoapClient for '$serviceName': " . $e->getMessage(), $e->getCode(), $e);
        }
    }
}
