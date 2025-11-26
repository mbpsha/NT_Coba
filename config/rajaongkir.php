<?php

return [
    /*
    |--------------------------------------------------------------------------
    | RajaOngkir API Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for RajaOngkir shipping cost calculation API.
    | Get your API key from: https://rajaongkir.com
    |
    */

    'api_key' => env('RAJAONGKIR_API_KEY', ''),

    'base_url' => env('RAJAONGKIR_BASE_URL', 'https://rajaongkir.komerce.id/api/v1'),

    'origin_city_id' => env('RAJAONGKIR_ORIGIN_CITY_ID', 501), // Surakarta

    'default_courier' => env('RAJAONGKIR_DEFAULT_COURIER', 'jne'),

    'fallback_cost' => env('RAJAONGKIR_FALLBACK_COST', 20000),

    'default_item_weight' => env('RAJAONGKIR_DEFAULT_ITEM_WEIGHT', 1000), // gram

    'admin_fee' => env('CHECKOUT_ADMIN_FEE', 5000),
];
