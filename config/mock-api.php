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

    'use' => env('MOCK_API', false),

    /*
    |--------------------------------------------------------------------------
    | Mock API datetime is less than
    |--------------------------------------------------------------------------
    |
    | Without setting this value get the latest request
    | Setting 'YYYY-MM-DD HH:mm:ss" means return mocked
    | the latest requests before this datetime
    */

    'datetime' => env('MOCK_API_DATETIME_IS_LESS_THAN'),

    /*
    |--------------------------------------------------------------------------
    | Mock API status is less than
    |--------------------------------------------------------------------------
    |
    | Default 300 means return mocked request with status code 200-299
    */

    'status' => env('MOCK_API_STATUS_IS_LESS_THAN', 300),
];
