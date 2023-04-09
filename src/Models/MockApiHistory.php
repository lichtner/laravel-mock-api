<?php

namespace Lichtner\MockApi\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MockApiHistory extends Model
{
    use HasFactory;

    protected $table = 'mock_api_history';

    protected $guarded = [];

    const UPDATED_AT = null;
}
