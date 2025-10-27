SIAT Bolivia Client

A modern, framework-agnostic PHP 8.1+ client for Bolivia's SIAT (Servicio de Impuestos Nacionales) SOAP services. This library provides a clean, object-oriented interface, abstracting the complexity of SoapClient and mapping all requests and responses to strongly-typed Data Transfer Objects (DTOs).

Features

Modern PHP: Built for PHP 8.1+ with strict types and modern syntax.

Framework Agnostic: Can be used in any PHP project.

Laravel Integration: Includes an optional Service Provider and Facade for seamless Laravel integration.

Strongly-Typed DTOs: All SOAP requests and responses are mapped to DTOs. No more dealing with stdClass or complex arrays.

Clean Interface: A simple client provides access to all SIAT services (Invoicing, Synchronization, Operations, etc.).

Exception Handling: Wraps SoapFault exceptions into a custom SiatException for cleaner error handling.

Installation

composer require amyr-it/siat-bolivia-client


Documentation

1. Configuration (DTO-based)

All client services are configured using a single SiatConfig object. This makes configuration explicit and easy to manage.

use Amyrit\SiatBoliviaClient\SiatConfig;

$config = new SiatConfig(
codigoSistema: 'YOUR_SYSTEM_CODE',
nit: 123456789,
apiKey: 'YOUR_API_KEY',
modalidad: SiatConfig::MODALIDAD_ELECTRONICA_EN_LINEA,
ambiente: SiatConfig::AMBIENTE_PRUEBAS,
// These credentials are obtained from other SIAT services
cuis: 'YOUR_CUIS',
cufd: 'YOUR_CUFD',
// ... other optional params
);


2. Framework-Agnostic Usage

You can instantiate the main client directly in any PHP application.

use Amyrit\SiatBoliviaClient\SiatClient;
use Amyrit\SiatBoliviaClient\Data\Requests\SolicitudRecepcionFactura;
use Amyrit\SiatBoliviaClient\SiatConfig;

// 1. Create Config
$config = new SiatConfig(/* ... */);

// 2. Create Client
$client = new SiatClient($config);

// 3. Create your request DTO
$facturaRequest = new SolicitudRecepcionFactura(
codigoDocumentoSector: 1,
// ... other invoice data
);

try {
// 4. Access a service and call a method
$response = $client->facturacion()->recepcionFactura($facturaRequest);

    // $response is a strongly-typed DTO!
    echo "Success: " . $response->codigoDescripcion;
    echo "Transaction ID: " . $response->transaccion;

} catch (\Amyrit\SiatBoliviaClient\Exceptions\SiatException $e) {
// Handle specific SIAT errors
echo "SOAP Error: " . $e->getMessage();
}


3. Laravel Usage

This package provides a Service Provider and Facade for easy integration.

1. Publish Configuration (Optional):

php artisan vendor:publish --provider="Amyrit\SiatBoliviaClient\Laravel\SiatServiceProvider"


This will create a config/siat.php file. Fill in your credentials, preferably from .env.

2. Use the Facade:

The Service Provider automatically configures the SiatClient and injects it into the container. You can use the Siat facade anywhere in your app.

use Amyrit\SiatBoliviaClient\Laravel\SiatFacade as Siat;
use Amyrit\SiatBoliviaClient\Data\Requests\SolicitudRecepcionFactura;

// The Facade resolves the client from the container
$response = Siat::facturacion()->recepcionFactura(new SolicitudRecepcionFactura(/* ... */));

echo $response->codigoDescripcion;


Note on Authentication (Sanctum)

You mentioned Sanctum. It's important to understand the two authentication layers:

SOAP Authentication (This Library): This library handles authentication with the SIAT SOAP services using your apiKey, CUIS, and CUFD.

Your API Authentication (Sanctum): You are building a REST API that consumes this library. Sanctum is the correct tool to protect your new API endpoints.

Example (Laravel Controller):

This is how you would use Sanctum to protect your endpoint, which in turn uses this library.

// app/Http/Controllers/Api/InvoiceController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Amyrit\SiatBoliviaClient\Laravel\SiatFacade as Siat;
use Amyrit\SiatBoliviaClient\Data\Requests\SolicitudRecepcionFactura;

class InvoiceController extends Controller
{
/**
* Create a new invoice.
* This route should be protected by Sanctum middleware.
*/
public function createInvoice(Request $request)
{
// 1. Validate $request data from your user
$validatedData = $request->validate([
'customerName' => 'required|string',
'items' => 'required|array',
// ...
]);

        // 2. Map your validated data to the SIAT Request DTO
        $facturaRequest = new SolicitudRecepcionFactura(
            codigoDocumentoSector: 1,
            // ... map $validatedData
        );

        // 3. Call the SIAT service using this library
        try {
            $response = Siat::facturacion()->recepcionFactura($facturaRequest);
            
            // 4. Return a JSON response to your API consumer
            if ($response->transaccion) {
                return response()->json(['success' => true, 'message' => $response->codigoDescripcion]);
            } else {
                return response()->json(['success' => false, 'message' => $response->codigoDescripcion], 400);
            }
            
        } catch (\Amyrit\SiatBoliviaClient\Exceptions\SiatException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}


// routes/api.php

use App\Http\Controllers\Api\InvoiceController;

// This route is protected by Sanctum
Route::middleware('auth:sanctum')->group(function () {
Route::post('/invoices', [InvoiceController::class, 'createInvoice']);
});
