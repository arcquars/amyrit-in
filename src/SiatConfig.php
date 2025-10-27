<?php

namespace Amyrit\SiatBoliviaClient;

/**
 * Configuration DTO for the SIAT Client.
 * This object holds all credentials and environment settings.
 */
class SiatConfig
{
    public const AMBIENTE_PRUEBAS = 2;
    public const AMBIENTE_PRODUCCION = 1;

    public const MODALIDAD_ELECTRONICA_EN_LINEA = 1;
    public const MODALIDAD_COMPUTARIZADA_EN_LINEA = 2;

    /**
     * WSDL URLs for SIAT services.
     * These are based on the official documentation from impuestos.gob.bo
     */
    protected const WSDL = [
        self::AMBIENTE_PRUEBAS => [
            'ServicioFacturacionSiat' => 'https://pilotosiatservicios.impuestos.gob.bo/v2/ServicioFacturacionSiat?wsdl',
            'ServicioSincronizacion' => 'https://pilotosiatservicios.impuestos.gob.bo/v2/ServicioSincronizacion?wsdl',
            'ServicioOperaciones' => 'https://pilotosiatservicios.impuestos.gob.bo/v2/ServicioOperaciones?wsdl',
            // ... Add all other WSDLs
        ],
        self::AMBIENTE_PRODUCCION => [
            'ServicioFacturacionSiat' => 'https://siatrest.impuestos.gob.bo/v2/ServicioFacturacionSiat?wsdl',
            'ServicioSincronizacion' => 'https://siatrest.impuestos.gob.bo/v2/ServicioSincronizacion?wsdl',
            'ServicioOperaciones' => 'https://siatrest.impuestos.gob.bo/v2/ServicioOperaciones?wsdl',
            // ... Add all other WSDLs
        ],
    ];

    /**
     * @param string $codigoSistema System Code provided by SIAT.
     * @param int $nit Your company's NIT.
     * @param string $apiKey Your SIAT API Key (Token).
     * @param int $modalidad Service Modality (e.g., MODALIDAD_ELECTRONICA_EN_LINEA).
     * @param int $ambiente Environment (e.g., AMBIENTE_PRUEBAS).
     * @param string|null $cuis CUIS code obtained from SIAT.
     * @param string|null $cufd CUFD code obtained from SIAT.
     * @param int $soapTimeout SOAP client timeout in seconds.
     */
    public function __construct(
        public readonly string $codigoSistema,
        public readonly int $nit,
        public readonly string $apiKey,
        public readonly int $modalidad = self::MODALIDAD_ELECTRONICA_EN_LINEA,
        public readonly int $ambiente = self::AMBIENTE_PRUEBAS,
        public readonly ?string $cuis = null,
        public readonly ?string $cufd = null,
        public readonly int $soapTimeout = 5
    ) {
    }

    /**
     * Get the WSDL URL for a specific service.
     *
     * @param string $serviceClassName (e.g., 'ServicioFacturacionSiat')
     * @return string|null
     */
    public function getWsdlUrl(string $serviceClassName): ?string
    {
        return self::WSDL[$this->ambiente][$serviceClassName] ?? null;
    }
}
