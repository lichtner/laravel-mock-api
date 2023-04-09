<?php

namespace Lichtner\MockApi\Commands;

use Illuminate\Console\Command;

class MockApiCommand extends Command
{
    public $signature = 'laravel-mock-api';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
