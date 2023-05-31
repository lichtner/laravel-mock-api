<?php

namespace Lichtner\MockApi;

use Carbon\Carbon;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Lichtner\MockApi\Models\MockApiUrl;
use Lichtner\MockApi\Models\MockApiUrlHistory;

class MockApi
{
    public static function init(string $url, string|array $data = null, string $method = 'GET'): void
    {
        if (config('app.env') !== config('mock-api.env')) {
            return;
        }

        if (! config('mock-api.mock')) {
            return;
        }

        $method = strtoupper($method);

        $mockApiUrl = MockApiUrl::where('url', $url)
            ->where('method', $method)
            ->firstWhere('mock', 1);
        if (! $mockApiUrl) {
            return;
        }

        $mockApiUrlHistory = MockApiUrlHistory::where('mock_api_url_id', $mockApiUrl->id);

        if ($mockApiUrl->mock_status) {
            $mockApiUrlHistory->where('status', $mockApiUrl->mock_status);
        } else {
            $mockApiUrlHistory->where('status', '<', config('mock-api.status'));
        }

        if ($mockApiUrl->mock_before) {
            $mockApiUrlHistory->where('created_at', '<', $mockApiUrl->mock_before);
        }

        $history = $mockApiUrlHistory->latest()->first();

        if (! $history) {
            return;
        }

        if (is_string($data)) {
            $data .= $history->data;
        }
        if (is_array($data) && str_contains($history->content_type, 'application/json')) {
            $merge = json_decode($history->data, true) ?? [];
            $data = array_merge_recursive($data, $merge);
        }

        Http::fake([
            $mockApiUrl->url => Http::response(
                $data,
                $history->status,
                [
                    'content-type' => $history->content_type,
                    'mock-api' => 'true',
                ]
            ),
        ]);
    }

    public static function log(string $url, Response $response, string $method = 'GET'): void
    {
        if (config('app.env') !== config('mock-api.env')) {
            return;
        }

        if ($response->header('mock-api') === 'true') {
            return;
        }

        $method = strtoupper($method);

        $mockApi = MockApiUrl::updateOrCreate(
            [
                'method' => $method,
                'url' => $url,
            ], [
                'last_status' => $response->status(),
                'updated_at' => Carbon::now(),
            ],
        );

        MockApiUrlHistory::create([
            'mock_api_url_id' => $mockApi->id,
            'status' => $response->status(),
            'content_type' => $response->header('content-type'),
            ...($method === 'GET' ? ['data' =>  $response->body()] : []),
        ]);
    }
}
