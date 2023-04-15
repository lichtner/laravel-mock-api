<?php

namespace Lichtner\MockApi\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $mock_api_url_id
 * @property int $status
 * @property string $content_type
 * @property string $data
 *
 * @method static create(array $array)
 */
class MockApiUrlHistory extends Model
{
    protected $table = 'mock_api_url_history';

    protected $guarded = [];

    const UPDATED_AT = null;
}
