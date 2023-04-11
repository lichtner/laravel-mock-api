<?php

namespace Lichtner\MockApi;

use Carbon\Carbon;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Lichtner\MockApi\Models\MockApiUrl;
use Lichtner\MockApi\Models\MockApiUrlHistory;

trait MockApi
{
    public static function mockApiUse(string $url): void
    {
        if (config('app.env') !== 'local') {
            return;
        }

        if (! config('mock-api.use')) {
            return;
        }

        $mockApi = MockApiUrl::with(['history' => function ($query) {
            $query->where('status', '<', config('mock-api.status'))->limit(1)->latest();

            if (config('mock-api.datetime')) {
                $query->where('created_at', '<', config('mock-api.datetime'));
            }
        }])->where([
            'url' => $url,
            'use' => 1,
        ])->first();

        if (! $mockApi) {
            return;
        }

        Http::fake([
            $mockApi->url => Http::response(
                $mockApi->history->first()->data,
                200,
                [
                    'content-type' => $mockApi->history->first()->content_type,
                    'mock-api' => 'true',
                ]
            ),
        ]);
    }

    public static function mockApiLog(string $url, Response $response): void
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
                'status' => $response->status(),
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
