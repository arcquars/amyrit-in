<?php

namespace Amyrit\SiatBoliviaClient\Data\Responses;

use Amyrit\SiatBoliviaClient\Data\DTOs\RespuestaMensaje;
use stdClass;

/**
 * DTO for the response of 'verificarNit' operation.
 */
class RespuestaVerificarNit
{
    /**
     * @param bool $transaccion Was the operation successful? (true means NIT is valid)
     * @param RespuestaMensaje[] $mensajesList List of messages (errors or info)
     */
    public function __construct(
        public bool $transaccion = false,
        public array $mensajesList = []
    ) {
    }

    /**
     * Factory method to map a stdClass from the SOAP response to this DTO.
     *
     * @param stdClass $object The raw $response->RespuestaVerificarNit object
     * @return self
     */
    public static function fromStdClass(stdClass $object): self
    {
        $mensajesList = [];

        // Safely map 'mensajesList' (can be array, object, or null)
        if (property_exists($object, 'mensajesList') && is_array($object->mensajesList)) {
            foreach ($object->mensajesList as $mensaje) {
                if ($mensaje instanceof stdClass) {
                    $mensajesList[] = RespuestaMensaje::fromStdClass($mensaje);
                }
            }
        } elseif (property_exists($object, 'mensajesList') && $object->mensajesList instanceof stdClass) {
            $mensajesList[] = RespuestaMensaje::fromStdClass($object->mensajesList);
        }

        return new self(
            $object->transaccion ?? false,
            $mensajesList
        );
    }
}