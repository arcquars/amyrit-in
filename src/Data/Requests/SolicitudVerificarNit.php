<?php

namespace Amyrit\SiatBoliviaClient\Data\Requests;

/**
 * DTO for the 'verificarNit' operation request.
 * Defines the customer NIT to be verified.
 */
class SolicitudVerificarNit
{
    /**
     * @param string $nitParaVerificacion The customer's NIT you want to check.
     */
    public function __construct(
        public string $nitParaVerificacion
    ) {
    }
}