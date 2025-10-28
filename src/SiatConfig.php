<?php

namespace Amyrit\SiatBoliviaClient;

/**
 * Configuration DTO for the SIAT Client.
 * This object holds all credentials and environment settings.
 */
class SiatConfig
{
    // --- Ambientes ---
    const AMBIENTE_PRODUCCION = 1;
    const AMBIENTE_PRUEBAS    = 2;

    // --- Modalidades ---
    const MODALIDAD_ELECTRONICA_EN_LINEA = 1;
    const MODALIDAD_COMPUTARIZADA_EN_LINEA = 2;

    // --- URLs Base ---
    const URL_PRUEBAS    = 'https://pilotosiatservicios.impuestos.gob.bo/v2/';
    const URL_PRODUCCION = 'https://siatservicios.impuestos.gob.bo/v2/';

    // --- Propiedades de Configuración ---
    public readonly string $codigoSistema;
    public readonly int $nit;
    public readonly string $apiKey;
    public readonly int $ambiente;
    public readonly int $modalidad;
    public readonly int $codigoSucursal;
    public readonly int $codigoPuntoVenta;
    public readonly int $soapTimeout;

    /**
     * The absolute path to the directory containing local .wsdl files.
     * @var string
     */
    public string $wsdlBasePath;

    // --- Propiedades de Runtime (Credenciales dinámicas) ---
    // Quitamos 'readonly' para poder establecerlos después de la inicialización.
    public ?string $cuis = null;
    public ?string $cufd = null;

    /**
     * @param array $config Array of configuration values.
     * @throws \InvalidArgumentException
     */
    public function __construct(array $config)
    {
        // Asignación de credenciales estáticas
        $this->codigoSistema    = $config['codigoSistema'] ?? throw new \InvalidArgumentException('Missing required config: codigoSistema');
        $this->nit              = (int)($config['nit'] ?? throw new \InvalidArgumentException('Missing required config: nit'));
        $this->apiKey           = $config['apiKey'] ?? throw new \InvalidArgumentException('Missing required config: apiKey');

        // Configuración de entorno
        $this->ambiente         = (int)($config['ambiente'] ?? self::AMBIENTE_PRUEBAS);
        $this->modalidad        = (int)($config['modalidad'] ?? self::MODALIDAD_ELECTRONICA_EN_LINEA);
        $this->codigoSucursal   = (int)($config['codigoSucursal'] ?? 0);
        $this->codigoPuntoVenta = (int)($config['codigoPuntoVenta'] ?? 0);

        // Configuración técnica
        $this->soapTimeout = (int)($config['soapTimeout'] ?? 5);

        // Configuración de WSDLs locales
        $this->wsdlBasePath = $config['wsdlBasePath']
            ?? dirname(__DIR__) . '/wsdls/'; // Asume que 'SiatConfig.php' está en 'src/'
        $this->wsdlBasePath = rtrim($this->wsdlBasePath, '/') . '/';

        // Asignación de credenciales dinámicas (si se proveen)
        $this->cuis = $config['cuis'] ?? null;
        $this->cufd = $config['cufd'] ?? null;
    }

    /**
     * Gets the full, absolute file path for a specific service WSDL.
     *
     * @param string $serviceName (e.g., "ServicioSincronizacion")
     * @return string
     */
    public function getWsdlPath(string $serviceName): string
    {
        return $this->wsdlBasePath . $serviceName . '.wsdl';
    }

    /**
     * Gets the real service endpoint URL for SoapClient::__setLocation
     *
     * @param string $serviceName
     * @return string
     */
    public function getEndpointUrl(string $serviceName): string
    {
        $baseUrl = ($this->ambiente == self::AMBIENTE_PRODUCCION)
            ? self::URL_PRODUCCION
            : self::URL_PRUEBAS;

        return $baseUrl . $serviceName;
    }
}
