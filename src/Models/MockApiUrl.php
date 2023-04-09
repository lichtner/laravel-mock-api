<?php

namespace Lichtner\MockApi\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MockApiUrl extends Model
{
    use HasFactory;

    protected $table = 'mock_api_url';

    protected $guarded = [];

    public function history(): HasMany
    {
        return $this->hasMany(MockApiUrlHistory::class);
    }
}
