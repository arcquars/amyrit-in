<?php

namespace Amyrit\SiatBoliviaClient\Data\Requests;

/**
 * A generic Request DTO for most synchronization methods.
 * These typically only require the Point of Sale code.
 */
class SolicitudSincronizacion
{
    /**
     * @param int $codigoPuntoVenta Point of Sale code. Use 0 if not applicable (e.g., for CUIS).
     */
    public function __construct(
        public int $codigoPuntoVenta = 0
    ) {
    }
}
