<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Mock API use
    |--------------------------------------------------------------------------
    |
    | Turn on / off Mock API.
    |  - false - Http makes real request to external API and log them to mock_api tables
    |  - true - Http makes fake request and returns mock data from mock_api tables
    */

    'mock' => env('MOCK_API', false),

    /*
    |--------------------------------------------------------------------------
    | Mock API status is less than
    |--------------------------------------------------------------------------
    |
    | 300 means return mocked request with status code less than 300 (200-299)
    */

    'status' => env('MOCK_API_STATUS', 300),

    /*
    |--------------------------------------------------------------------------
    | Mock API environment
    |--------------------------------------------------------------------------
    |
    | Use Mock API only on this environment
    */

    'env' => env('MOCK_API_ENV', 'local'),
];
