<?php

namespace Amyrit\SiatBoliviaClient\Data\DTOs;

use stdClass;

/**
 * DTO for standard SIAT error messages
 */
class RespuestaMensaje
{
    public function __construct(
        public ?int $codigo = null,
        public ?string $descripcion = null
    ) {
    }

    /**
     * Map a stdClass from the SOAP response to this DTO.
     *
     * @param stdClass $object
     * @return self
     */
    public static function fromStdClass(stdClass $object): self
    {
        return new self(
            $object->codigo ?? null,
            $object->descripcion ?? null,
        );
    }
}
