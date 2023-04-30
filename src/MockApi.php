<?php

namespace Lichtner\MockApi;

use Carbon\Carbon;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Lichtner\MockApi\Models\MockApiUrl;
use Lichtner\MockApi\Models\MockApiUrlHistory;

class MockApi
{
    public static function init(string $url): void
    {
        if (config('app.env') !== 'local') {
            return;
        }

        if (! config('mock-api.mock')) {
            return;
        }

        $mockApiUrl = MockApiUrl::where('url', $url)
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

        if (config('mock-api.datetime')) {
            $mockApiUrlHistory->where('created_at', '<', config('mock-api.datetime'));
        }

        $history = $mockApiUrlHistory->latest()->first();

        if (! $history) {
            return;
        }

        Http::fake([
            $mockApiUrl->url => Http::response(
                $history->data,
                $history->status,
                [
                    'content-type' => $history->content_type,
                    'mock-api' => 'true',
                ]
            ),
        ]);
    }

    public static function log(string $url, Response $response): void
    {
        if (config('app.env') !== 'local') {
            return;
        }

        if ($response->header('mock-api') === 'true') {
            return;
        }

        $mockApi = MockApiUrl::updateOrCreate(
            [
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
            'data' => $response->body(),
        ]);
    }
}
