<?php

namespace Amyrit\SiatBoliviaClient\Data\Responses;

use stdClass;

/**
 * Response DTO for the 'recepcionFactura' service.
 * This object normalizes the response from SIAT.
 */
class RespuestaRecepcionFactura
{
    /**
     * @param string|null $codigoDescripcion Description of the response.
     * @param int|null $codigoEstado The status code from SIAT.
     * @param bool $transaccion Indicates if the transaction was successful.
     * @param array $mensajesList Optional list of messages (warnings/errors).
     */
    public function __construct(
        public ?string $codigoDescripcion = null,
        public ?int $codigoEstado = null,
        public bool $transaccion = false,
        public array $mensajesList = []
    ) {
    }

    /**
     * Static factory method to create the DTO from the raw stdClass SOAP response.
     *
     * @param stdClass $response The raw 'RespuestaServicioFacturacion' object from SOAP.
     * @return self
     */
    public static function fromStdClass(stdClass $response): self
    {
        return new self(
            codigoDescripcion: $response->codigoDescripcion ?? null,
            codigoEstado: $response->codigoEstado ?? null,
            transaccion: $response->transaccion ?? false,
            mensajesList: isset($response->mensajesList) ? (array) $response->mensajesList : []
        );
    }
}
