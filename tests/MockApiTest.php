<?php

use Illuminate\Support\Facades\Http;
use Lichtner\MockApi\MockApi;
use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;
use function PHPUnit\Framework\assertEquals;

it('log request to mock api tables', function () {
    Http::fake([
        'users' => Http::response('users', 200, ['content-type' => 'application/json; charset=utf-8']),
    ]);
    assertDatabaseCount('mock_api_url', 0);
    assertDatabaseCount('mock_api_url_history', 0);

    (new class
    {
 use MockApi;
 })::mockApiLog('/users', Http::get('users'));

    assertDatabaseCount('mock_api_url', 1);
    assertDatabaseHas('mock_api_url', [
        'status' => 200,
        'use' => 1,
        'url' => '/users',
    ]);
    assertDatabaseCount('mock_api_url_history', 1);
    assertDatabaseHas('mock_api_url_history', [
        'status' => 200,
        'content_type' => 'application/json; charset=utf-8',
        'data' => 'users',
    ]);

    config([
        'mock-api.use' => true,
    ]);

    (new class
    {
 use MockApi;
 })::mockApiUse('/users');

    $response = Http::get('/users');

    assertEquals('users', $response->body());
    assertEquals('application/json; charset=utf-8', $response->header('content-type'));
});
