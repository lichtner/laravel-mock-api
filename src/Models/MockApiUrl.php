<?php

namespace Lichtner\MockApi\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $status
 * @property int $use
 * @property string $url
 *
 * @method static updateOrCreate(string[] $array, array $array1)
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
