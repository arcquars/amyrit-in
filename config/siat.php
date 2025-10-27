<?php

use Amyrit\SiatBoliviaClient\SiatConfig;

/**
 * Configuration file for the SIAT Bolivia Client.
 * Publish with: php artisan vendor:publish --provider="Amyrit\SiatBoliviaClient\Laravel\SiatServiceProvider"
 */
return [

    'codigo_sistema' => env('SIAT_CODIGO_SISTEMA', ''),
    'nit'            => env('SIAT_NIT', 0),
    'api_key'        => env('SIAT_API_KEY', ''),

    'modalidad' => (int) env('SIAT_MODALIDAD', SiatConfig::MODALIDAD_ELECTRONICA_EN_LINEA),
    'ambiente'  => (int) env('SIAT_AMBIENTE', SiatConfig::AMBIENTE_PRUEBAS),

    /*
    |--------------------------------------------------------------------------
    | Dynamic Credentials (CUIS, CUFD)
    |--------------------------------------------------------------------------
    |
    | It is recommended to leave these as 'null' and fetch them dynamically
    | from the SIAT services, then store them in your application's cache
    | or database. You can then pass them to the config at runtime or
    | create a new client instance.
    |
    */
    'cuis' => null,
    'cufd' => null,

    'soap_timeout' => (int) env('SIAT_SOAP_TIMEOUT', 5),
];
