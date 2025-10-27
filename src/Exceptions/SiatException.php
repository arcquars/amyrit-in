<?php

namespace Amyrit\SiatBoliviaClient\Exceptions;

use SoapFault;
use Throwable;

/**
 * Custom Exception for SIAT-specific errors.
 * This wraps native SoapFault exceptions for cleaner error handling
 * and provides a consistent exception type to catch.
 */
class SiatException extends \Exception
{
    /**
     * @var SoapFault|null The original SoapFault, if one occurred.
     */
    protected ?SoapFault $soapFault;

    /**
     * SiatException constructor.
     *
     * @param string $message The exception message.
     * @param int $code The exception code.
     * @param SoapFault|null $previous The previous throwable (SoapFault).
     */
    public function __construct(string $message = "", int $code = 0, ?SoapFault $previous = null)
    {
        $this->soapFault = $previous;
        parent::__construct($message, $code, $previous);
    }

    /**
     * Get the original SoapFault if it exists.
     *
     * @return SoapFault|null
     */
    public function getSoapFault(): ?SoapFault
    {
        return $this->soapFault;
    }

    /**
     * Get the SOAP fault code.
     *
     * @return string|null
     */
    public function getFaultCode(): ?string
    {
        return $this->soapFault?->faultcode;
    }

    /**
     * Get the SOAP fault string.
     *
     * @return string|null
     */
    public function getFaultString(): ?string
    {
        return $this->soapFault?->faultstring;
    }
}
