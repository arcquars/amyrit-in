<?php

namespace Amyrit\SiatBoliviaClient\Data\DTOs;

use stdClass;

/**
 * Data Transfer Object for a single "Actividad" (Economic Activity).
 * This is a nested DTO used by RespuestaSincronizacionActividades.
 */
class ListaActividadDto
{
    public function __construct(
        public readonly string $codigoCaeb,
        public readonly string $descripcion
    ) {
    }

    /**
     * Factory method to create DTO from raw stdClass.
     * @param stdClass $data
     * @return self
     */
    public static function fromStdClass(stdClass $data): self
    {
        return new self(
            codigoCaeb: $data->codigoCaeb,
            descripcion: $data->descripcion
        );
    }
}
