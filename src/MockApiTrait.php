<?php

namespace Lichtner\MockApi;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;
use Lichtner\MockApi\Models\MockApi;
use Lichtner\MockApi\Models\MockApiHistory;

trait MockApiTrait
{

    private static function mockApiUse(string $url): void
    {
        if (!App::environment('local')) {
            return;
        }

        if (!config('mock-api.use')) {
            return;
        }

        $mockApi = MockApi::with(['logs' => function ($query) {
            $query->where('status', '<', config('mock-api.status'))->limit(1)->latest();
            if (config('mock-api.until')) {
                $query->where('created_at', '<', config('mock-api.until'));
            }
        }])->where([
            'url' => $url,
            'use' => 1,
        ])->first();

        if (!$mockApi) {
            return;
        }

        Http::fake([
            $mockApi->url => Http::response(
                $mockApi->logs->first()->data,
                200,
                ['mock-api' => 'true']
            ),
        ]);
    }

    private static function mockApiLog(string $url, Response $response): void
    {
        if (!App::environment('local')) {
            return;
        }

        if ($response->header('mock-api') === 'true') {
            return;
        }

        $mockApi = MockApi::updateOrCreate(
            ['url' => $url],
            ['status' => $response->status()],
        );

        MockApiHistory::create([
            'mock_api_id' => $mockApi->id,
            'status' => $response->status(),
            'data' => $response->body(),
        ]);
    }
}

