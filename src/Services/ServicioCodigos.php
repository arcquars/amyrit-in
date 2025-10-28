<?php

namespace Amyrit\SiatBoliviaClient\Services;

use Amyrit\SiatBoliviaClient\Data\Requests\SolicitudCuis;
use Amyrit\SiatBoliviaClient\Data\Requests\SolicitudVerificarNit;
use Amyrit\SiatBoliviaClient\Data\Responses\RespuestaCuis;
use Amyrit\SiatBoliviaClient\Data\Responses\RespuestaVerificarNit;
use Amyrit\SiatBoliviaClient\Exceptions\SiatException;
use SoapFault;
use stdClass;

/**
 * Client for the 'ServicioFacturacionCodigos'
 * This service is used to verify NITs and get massive CUIS codes.
 */
class ServicioCodigos extends BaseSiatService
{
    /**
     * The WSDL service name, must match the .wsdl file name.
     * @var string
     */
    protected string $serviceName = 'FacturacionCodigos';

    /**
     * Verifies if a customer's NIT is active and valid in SIAT.
     * This method REQUIRES a valid CUIS to be present in the SiatConfig.
     *
     * @param SolicitudVerificarNit $request DTO with the NIT to verify.
     * @return RespuestaVerificarNit DTO with the validation result.
     * @throws SiatException If SOAP call fails or SIAT rejects the transaction.
     */
    public function verificarNit(SolicitudVerificarNit $request): RespuestaVerificarNit
    {
        // Ensure we have a CUIS, as this service requires it.
        if (empty($this->config->cuis)) {
            throw new SiatException("Cannot call 'verificarNit': CUIS is missing from configuration.");
        }

        $client = $this->clientFactory->getClient($this->serviceName);

        // Build the parameters array matching the WSDL structure
        $params = [
            'SolicitudVerificarNit' => [
                'codigoSistema'    => $this->config->codigoSistema,
                'nit'              => $this->config->nit,
                'codigoAmbiente'   => $this->config->ambiente,
                'codigoModalidad'  => $this->config->modalidad,
                'cuis'             => $this->config->cuis, // <-- CUIS is mandatory here
                'codigoSucursal'   => $this->config->codigoSucursal,
                'nitParaVerificacion' => $request->nitParaVerificacion,
            ]
        ];

        try {
            // Make the actual SOAP call (method 'verificarNit' comes from the WSDL)
            $response = $client->verificarNit($params);

            // Validate the response structure
            if (
                !$response instanceof stdClass ||
                !property_exists($response, 'RespuestaVerificarNit') ||
                !$response->RespuestaVerificarNit instanceof stdClass
            ) {
                throw new SiatException(
                    'Invalid response structure from "verificarNit".',
                    0, null,
                    $client->__getLastRequest(),
                    $client->__getLastResponse()
                );
            }

            // Map the raw stdClass to our clean DTO
            $dtoResponse = RespuestaVerificarNit::fromStdClass($response->RespuestaVerificarNit);

            // Check if SIAT rejected the transaction (e.g., NIT doesn't exist)
            if ($dtoResponse->transaccion === false) {
                $errorMessage = "SIAT rejected the 'verificarNit' request:";
                foreach ($dtoResponse->mensajesList as $mensaje) {
                    $errorMessage .= " (Code: {$mensaje->codigo}) {$mensaje->descripcion}";
                }
                // Throw an exception with the specific SIAT error
                throw new SiatException(
                    $errorMessage,
                    0, null,
                    $client->__getLastRequest(),
                    $client->__getLastResponse()
                );
            }

            // Return the successful response DTO
            return $dtoResponse;

        } catch (SoapFault $e) {
            // Catch low-level SOAP errors
            throw new SiatException(
                "SOAP Fault in 'verificarNit': " . $e->getMessage(),
                (int)$e->getCode(),
                $e,
                $client->__getLastRequest(),
                $client->__getLastResponse()
            );
        }
    }

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
            'SolicitudCuis' => [
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
                if($dtoResponse->codigoCuis != null){
                    return $dtoResponse;
                }

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