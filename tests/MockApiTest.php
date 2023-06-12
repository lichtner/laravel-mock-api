<?php

use Illuminate\Support\Facades\Http;
use Lichtner\MockApi\MockApi;
use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;
use function PHPUnit\Framework\assertEquals;

it('log request to mock api tables', closure: function () {
    Http::fake([
        'users' => Http::response(
            '{"data":"users"}',
            200,
            ['content-type' => 'application/json; charset=utf-8']
        ),
    ]);
    assertDatabaseCount('mock_api_url', 0);
    assertDatabaseCount('mock_api_url_history', 0);

    MockApi::log('/users', Http::get('users'));

    assertDatabaseCount('mock_api_url', 1);
    assertDatabaseHas('mock_api_url', [
        'last_status' => 200,
        'mock' => 1,
        'method' => 'GET',
        'url' => '/users',
    ]);
    assertDatabaseCount('mock_api_url_history', 1);
    assertDatabaseHas('mock_api_url_history', [
        'status' => 200,
        'content_type' => 'application/json; charset=utf-8',
        'data' => '{"data":"users"}',
    ]);

    config([
        'mock-api.mock' => true,
    ]);

    MockApi::init('/users');

    $response = Http::get('/users');

    assertEquals('{"data":"users"}', $response->body());
    assertEquals('application/json; charset=utf-8', $response->header('content-type'));
});
