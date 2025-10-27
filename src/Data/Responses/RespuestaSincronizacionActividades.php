<?php

namespace Amyrit\SiatBoliviaClient\Data\Responses;

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
     */
    public function __construct(
        public readonly bool $transaccion = false,
        public readonly array $listaActividades = []
    ) {
    }

    /**
     * Static factory method to create the DTO from the raw stdClass SOAP response.
     *
     * @param stdClass $response The raw 'RespuestaListaActividades' object from SOAP.
     * @return self
     */
    public static function fromStdClass(stdClass $response): self
    {
        $actividades = [];
        if (isset($response->listaActividades)) {
            // Handle SOAP quirk: single item is not an array
            $list = is_array($response->listaActividades)
                ? $response->listaActividades
                : [$response->listaActividades];

            foreach ($list as $item) {
                if ($item instanceof stdClass) {
                    $actividades[] = ListaActividadDto::fromStdClass($item);
                }
            }
        }

        return new self(
            transaccion: $response->transaccion ?? false,
            listaActividades: $actividades
        );
    }
}
