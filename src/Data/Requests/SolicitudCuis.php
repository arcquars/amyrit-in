<?php

namespace Amyrit\SiatBoliviaClient\Data\Requests;

class SolicitudCuis
{
    /**
     * @param int $codigoPuntoVenta
     * @param int|null $codigoSucursal (Opcional, se tomará de SiatConfig si es null)
     */
    public function __construct(
        public int $codigoPuntoVenta,
        public ?int $codigoSucursal = null
    ) {
    }

}