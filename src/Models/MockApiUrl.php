<?php

namespace Lichtner\MockApi\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property mixed $id
 * @property int $mock
 * @property Carbon|null $mock_before
 * @property int $mock_status
 * @property int $last_status
 * @property string $method
 * @property string $url
 *
 * @method static updateOrCreate(string[] $array, array $array1)
 * @method static where(string $string, string $url)
 * @method MockApiUrl firstWhere(string $string, string $url)
 */
class MockApiUrl extends Model
{
    protected $table = 'mock_api_url';

    protected $guarded = [];

    public function history(): HasMany
    {
        return $this->hasMany(MockApiUrlHistory::class);
    }
}
