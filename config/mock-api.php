<?php

return [
    'use' => env('MOCK_API', false),
    'until' => env('MOCK_API_UNTIL'),
    'status' => env('MOCK_API_STATUS', 300),
];
