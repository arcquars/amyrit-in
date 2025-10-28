<?php

namespace Amyrit\SiatBoliviaClient\Data\Responses;

use Amyrit\SiatBoliviaClient\Data\DTOs\RespuestaMensaje;
use stdClass;
use Amyrit\SiatBoliviaClient\Data\DTOs\ListaActividadDto;

/**
 * Response DTO for the 'sincronizarActividades' service.
 */
class RespuestaSincronizacionActividades
{
    /**
     * @param bool $transaccion
     * @param ListaActividadDto[] $listaActividades
     * @param RespuestaMensaje[] $mensajesList
     */
    public function __construct(
        public bool $transaccion = false,
        public array $listaActividades = [],
        public array $mensajesList = []
    ) {
    }

    /**
     * Map a stdClass from the SOAP response to this DTO.
     *
     * @param stdClass $object (This is $response->RespuestaListaActividades)
     * @return self
     */
    public static function fromStdClass(stdClass $object): self
    {
        $listaActividades = [];
        $mensajesList = [];

        // Safely map 'listaActividades' (can be array, object, or null)
        if (property_exists($object, 'listaActividades') && is_array($object->listaActividades)) {
            foreach ($object->listaActividades as $actividad) {
                if ($actividad instanceof stdClass) {
                    $listaActividades[] = ListaActividadDto::fromStdClass($actividad);
                }
            }
        } elseif (property_exists($object, 'listaActividades') && $object->listaActividades instanceof stdClass) {
            // Handle case where 1 item is returned as object, not array
            $listaActividades[] = ListaActividadDto::fromStdClass($object->listaActividades);
        }

        // Safely map 'mensajesList' (can be array, object, or null)
        if (property_exists($object, 'mensajesList') && is_array($object->mensajesList)) {
            foreach ($object->mensajesList as $mensaje) {
                if ($mensaje instanceof stdClass) {
                    $mensajesList[] = RespuestaMensaje::fromStdClass($mensaje);
                }
            }
        } elseif (property_exists($object, 'mensajesList') && $object->mensajesList instanceof stdClass) {
            // Handle case where 1 message is returned as object
            $mensajesList[] = RespuestaMensaje::fromStdClass($object->mensajesList);
        }

        return new self(
            $object->transaccion ?? false,
            $listaActividades,
            $mensajesList
        );
    }
}
