<?php

namespace Amyrit\SiatBoliviaClient\Data\Requests;

use DateTime;

/**
 * Request DTO for the 'recepcionFactura' service.
 * This object encapsulates all data needed for the request.
 */
class SolicitudRecepcionFactura
{
    /**
     * @param int $codigoDocumentoSector Document Sector Code (e.g., 1 for Factura Compra-Venta).
     * @param int $tipoFacturaDocumento Invoice Type (e.g., 1 for Factura con Derecho a Crédito Fiscal).
     * @param string $archivoXml The raw XML content of the invoice.
     * @param string $fechaEnvio Date/Time of sending (format: 'Y-m-d\TH:i:s.v').
     * @param string $hashArchivo SHA256 hash of the gzipped XML file.
     * @param int $codigoPuntoVenta Point of Sale code (0 if not applicable).
     */
    public function __construct(
        public int $codigoDocumentoSector,
        public int $tipoFacturaDocumento,
        public string $archivoXml,
        public string $fechaEnvio,
        public string $hashArchivo,
        public int $codigoPuntoVenta = 0
    ) {
    }

    /**
     * Gets the gzipped and Base64-encoded XML file.
     *
     * @return string
     */
    public function getArchivoXmlBase64(): string
    {
        // SIAT requires the XML to be gzipped and then encoded in Base64
        return base64_encode(gzencode($this->archivoXml, 9));
    }

    /**
     * Helper to create the request with automatic hashing and date formatting.
     *
     * @param int $codigoDocumentoSector
     * @param int $tipoFacturaDocumento
     * @param string $archivoXml Raw XML string.
     * @param int $codigoPuntoVenta
     * @return self
     */
    public static function create(
        int $codigoDocumentoSector,
        int $tipoFacturaDocumento,
        string $archivoXml,
        int $codigoPuntoVenta = 0
    ): self {
        $gzippedData = gzencode($archivoXml, 9);
        $hash = hash('sha256', $gzippedData);
        $fechaEnvio = (new DateTime())->format('Y-m-d\TH:i:s.v');

        return new self(
            codigoDocumentoSector: $codigoDocumentoSector,
            tipoFacturaDocumento: $tipoFacturaDocumento,
            archivoXml: $archivoXml, // We store the raw XML
            fechaEnvio: $fechaEnvio,
            hashArchivo: $hash,
            codigoPuntoVenta: $codigoPuntoVenta
        );
    }
}
