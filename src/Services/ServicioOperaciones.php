<?php

namespace Amyrit\SiatBoliviaClient\Services;

use Amyrit\SiatBoliviaClient\Data\Requests\SolicitudCuis;
use Amyrit\SiatBoliviaClient\Data\Responses\RespuestaCuis;
use Amyrit\SiatBoliviaClient\Exceptions\SiatException;
use SoapFault;
use stdClass;

/**
 * Client for the 'ServicioOperaciones'
 * This service is used to get CUIS, CUFD, and other operational codes.
 */
class ServicioOperaciones extends BaseSiatService
{
    protected string $serviceName = 'FacturacionOperaciones';

    /**
     * Gets the CUIS (Código Único de Inicio de Sistema)
     *
     * @param SolicitudCuis $request
     * @return RespuestaCuis
     * @throws SiatException
     */
    public function cuis(SolicitudCuis $request): RespuestaCuis
    {
        $client = $this->clientFactory->getClient($this->serviceName);

        $params = [
            'SolicitudOperaciones' => [
                'codigoSistema'    => $this->config->codigoSistema,
                'nit'              => $this->config->nit,
                'codigoAmbiente'   => $this->config->ambiente,
                'codigoModalidad'  => $this->config->modalidad,
                'codigoSucursal'   => $request->codigoSucursal ?? $this->config->codigoSucursal,
                'codigoPuntoVenta' => $request->codigoPuntoVenta,
            ]
        ];

        try {
            // El WSDL define la operación como 'cuis'
            $response = $client->cuis($params);

            if (
                !$response instanceof stdClass ||
                !property_exists($response, 'RespuestaCuis') ||
                !$response->RespuestaCuis instanceof stdClass
            ) {
                throw new SiatException('Invalid response structure from "cuis".', 0, null, $client->__getLastRequest(), $client->__getLastResponse());
            }

            $dtoResponse = RespuestaCuis::fromStdClass($response->RespuestaCuis);

            if ($dtoResponse->transaccion === false) {
                $errorMessage = "SIAT rejected the 'cuis' request:";
                foreach ($dtoResponse->mensajesList as $mensaje) {
                    $errorMessage .= " (Code: {$mensaje->codigo}) {$mensaje->descripcion}";
                }
                throw new SiatException($errorMessage, 0, null, $client->__getLastRequest(), $client->__getLastResponse());
            }

            return $dtoResponse;

        } catch (SoapFault $e) {
            throw new SiatException("SOAP Fault in 'cuis': " . $e->getMessage(), (int)$e->getCode(), $e, $client->__getLastRequest(), $client->__getLastResponse());
        }
    }
}