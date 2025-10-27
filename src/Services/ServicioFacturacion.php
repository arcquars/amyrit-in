<?php

namespace Amyrit\SiatBoliviaClient\Services;

use Amyrit\SiatBoliviaClient\Data\Requests\SolicitudRecepcionFactura;
use Amyrit\SiatBoliviaClient\Data\Responses\RespuestaRecepcionFactura;
use Amyrit\SiatBoliviaClient\Exceptions\SiatException;
use SoapFault;
use stdClass;

/**
 * Client for the 'ServicioFacturacionSiat'
 *
 * This version includes improved validation and robust error handling.
 */
class ServicioFacturacion extends BaseSiatService
{
    protected string $serviceName = 'ServicioFacturacionSiat';

    /**
     * Receive an invoice (factura).
     *
     * @param SolicitudRecepcionFactura $request A DTO containing all invoice data.
     * @return RespuestaRecepcionFactura A DTO containing the response from SIAT.
     * @throws SiatException
     */
    public function recepcionFactura(SolicitudRecepcionFactura $request): RespuestaRecepcionFactura
    {
        // --- VALIDATION 1: Check for required credentials ---
        // This operation requires CUIS and CUFD, which are dynamic.
        if (empty($this->config->cuis) || empty($this->config->cufd)) {
            throw new SiatException(
                'CUIS and CUFD are required for invoicing. Please fetch them (e.g., using ServicioOperaciones) and set them in SiatConfig before calling this method.'
            );
        }

        // 1. Get the configured SoapClient for this service
        $client = $this->clientFactory->getClient($this->serviceName);

        // 2. Build the request arguments array
        $params = [
            'SolicitudServicioRecepcionFactura' => [
                'codigoSistema'         => $this->config->codigoSistema,
                'nit'                   => $this->config->nit,
                'cuis'                  => $this->config->cuis,
                'cufd'                  => $this->config->cufd,
                'codigoModalidad'       => $this->config->modalidad,
                'codigoAmbiente'        => $this->config->ambiente,
                'codigoDocumentoSector' => $request->codigoDocumentoSector,
                'tipoFacturaDocumento'  => $request->tipoFacturaDocumento,
                'codigoPuntoVenta'      => $request->codigoPuntoVenta,

                // The request DTO prepares the raw XML/Base64 content
                'archivo'               => $request->getArchivoXmlBase64(),
                'fechaEnvio'            => $request->fechaEnvio,
                'hashArchivo'           => $request->hashArchivo,
            ]
        ];

        try {
            // 3. Call the SOAP method
            $response = $client->recepcionFactura($params);

            // --- VALIDATION 2: Check for a valid response structure ---
            if (
                !$response instanceof stdClass ||
                !property_exists($response, 'RespuestaServicioFacturacion') ||
                !$response->RespuestaServicioFacturacion instanceof stdClass
            ) {
                // This handles cases where SIAT returns a non-standard response
                // (e.g., empty response or different error structure) without throwing a SoapFault.
                throw new SiatException(
                    'Invalid response structure received from SIAT service. Expected "RespuestaServicioFacturacion" object.',
                    0,
                    null,
                    $client->__getLastRequest(), // Pass raw XML request for debugging
                    $client->__getLastResponse() // Pass raw XML response for debugging
                );
            }

            // 4. Map the stdClass response to our strongly-typed DTO
            return RespuestaRecepcionFactura::fromStdClass($response->RespuestaServicioFacturacion);

        } catch (SoapFault $e) {
            // 5. Wrap SoapFault in our custom exception
            throw new SiatException(
                "Error in recepcionFactura (SoapFault): " . $e->getMessage(),
                (int)$e->getCode(),
                $e,
                $client->__getLastRequest(),
                $client->__getLastResponse()
            );
        } catch (\Exception $e) {
            // Catch any other unexpected errors (like our own SiatException)
            throw new SiatException(
                "An unexpected error occurred: " . $e->getMessage(),
                $e->getCode(),
                $e instanceof SoapFault ? $e : null,
                $client->__getLastRequest() ?? null,
                $client->__getLastResponse() ?? null
            );
        }
    }

    // ... Implement other methods from this service
    // public function anulacionFactura(SolicitudAnulacion $request): RespuestaAnulacion
    // { ... }
}

