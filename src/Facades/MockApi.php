<?php

namespace Lichtner\MockApi\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Lichtner\MockApi\MockApiTrait
 */
class MockApi extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Lichtner\MockApi\MockApiTrait::class;
    }
}
