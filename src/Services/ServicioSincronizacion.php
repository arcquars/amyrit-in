<?php

namespace Amyrit\SiatBoliviaClient\Services;

use Amyrit\SiatBoliviaClient\Data\Requests\SolicitudSincronizacion;
use Amyrit\SiatBoliviaClient\Data\Responses\RespuestaSincronizacionActividades;
use Amyrit\SiatBoliviaClient\Exceptions\SiatException;
use SoapFault;

/**
 * Client for the 'ServicioSincronizacion'
 * This service is used to get catalogs and parameters from SIAT.
 */
class ServicioSincronizacion extends BaseSiatService
{
    /**
     * The WSDL service name.
     * @var string
     */
//    protected string $serviceName = 'ServicioSincronizacion';
    protected string $serviceName = 'FacturacionSincronizacion';

    /**
     * Synchronizes the list of economic activities (CAEB).
     *
     * @param SolicitudSincronizacion $request
     * @return RespuestaSincronizacionActividades
     * @throws SiatException
     */
    public function sincronizarActividades(SolicitudSincronizacion $request): RespuestaSincronizacionActividades
    {
        // 1. Get the configured SoapClient for this service
        $client = $this->clientFactory->getClient($this->serviceName);

        // 2. Build the request arguments array
        $params = [
            'SolicitudSincronizacion' => [
                'codigoSistema'    => $this->config->codigoSistema,
                'nit'              => $this->config->nit,
                'cuis'             => $this->config->cuis,
                'codigoSucursal'   => 0,
                'codigoPuntoVenta' => $request->codigoPuntoVenta,
                'codigoAmbiente'   => $this->config->ambiente,
                ''
            ]
        ];

        try {
            // 3. Call the SOAP method
            $response = $client->sincronizarActividades($params);

            // 4. Map the stdClass response to our strongly-typed DTO
            return RespuestaSincronizacionActividades::fromStdClass($response->RespuestaListaActividades);

        } catch (SoapFault $e) {
            // 5. Wrap SoapFault in our custom exception
            throw new SiatException(
                "Error in sincronizarActividades: " . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }

    // ... Implement other methods from this service following the same pattern:
    // public function sincronizarListaLeyendasFactura(SolicitudSincronizacion $request): RespuestaListaLeyendas
    // { ... }

    // public function sincronizarParametricaTipoMetodoPago(SolicitudSincronizacion $request): RespuestaParametrica
    // { ... }
}
