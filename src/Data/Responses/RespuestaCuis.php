<?php

namespace Amyrit\SiatBoliviaClient\Data\Responses;

use Amyrit\SiatBoliviaClient\Data\DTOs\RespuestaMensaje;
use stdClass;

class RespuestaCuis
{
    /**
     * @param bool $transaccion
     * @param string|null $codigoCuis
     * @param RespuestaMensaje[] $mensajesList
     */
    public function __construct(
        public bool $transaccion = false,
        public ?string $codigoCuis = null,
        public ?string $fechaVigencia = null,
        public array $mensajesList = []
    ) {
    }

    public static function fromStdClass(stdClass $object): self
    {
        $mensajesList = [];

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
            $object->codigo ?? null, // El WSDL de CUIS lo devuelve como 'codigo'
            $object->fechaVigencia ?? null, // El WSDL de CUIS lo devuelve como 'codigo'
            $mensajesList
        );
    }
}