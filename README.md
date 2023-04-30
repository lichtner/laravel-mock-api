# Laravel MockApi

*Easy to use, but the powerful micro library for mocking external API*

[![Latest Version on Packagist](https://img.shields.io/packagist/v/lichtner/laravel-mock-api.svg?style=flat-square)](https://packagist.org/packages/lichtner/laravel-mock-api)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/lichtner/laravel-mock-api/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/lichtner/laravel-mock-api/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/lichtner/laravel-mock-api/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/lichtner/laravel-mock-api/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/lichtner/laravel-mock-api.svg?style=flat-square)](https://packagist.org/packages/lichtner/laravel-mock-api)

## Why?

Are you using external APIs and web services during development that are also in the development process? Are they unstable, slow, sometimes returning incorrect results, or unexpectedly unavailable? Are they causing you headaches? Me too! That was the reason why I created MockApi. It is less than 100 rows but so helpful.

MockApi solves these problems for you. It saves all GET requests from your external web services, and when they are unavailable, you can return saved data like real API.

## Installation

```bash
composer require lichtner/laravel-mock-api
```

Now you can publish config file and run the migrations with:

```bash
php artisan mock-api:install
```

## Setup

Make a simple class that wrap all your `Http::get()` requests. Create file e.g.:

`app/HttpMock.php`

```php
<?php

namespace App;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Lichtner\MockApi\MockApi;

class HttpMock
{
    public static function get($url): Response
    {
        MockApi::init($url);

        $response = Http::get($url);

        MockApi::log($url, $response);

        return $response;
    }
}
```

Everywhere in your code replace :

```php
Http::get($url);
```

with: 

```php
HttpMock::get($url);
```

It is done! Now you can start mocking all your external APIs (and maybe colleagues who are developing them ;-)

## Security 

**By default, MockApi works only on the `local` environment! It does not affect the other ones!**

## Usage

After you did the changes described in [Setup](#setup) all HTTP GET requests will be saved in MockApi tables. But they won't be used.

### Mock all webservices

For returning mocked data add in the `.env` file:

```yaml
MOCK_API=true
```

From that moment all external resources will return the last saved successful responses.

After your web services are back, change it to:

```yaml
MOCK_API=false
```

### Mock only some of webservices

`mock_api_url.mock = 1` means resource is mocked. If you want to mock only some of them, set the others to `0`. 

### Mock data from the past

By default, MockApi returns the last saved successful responses (<300). If some of the resources are wrong, and you know that yesterday's were fine, set in `mock_api_url.mock_before` datetime when they were fine.

### Mock error requests

Maybe you want to improve how your app deals with external API errors. You can mock error responses too. Check table `mock_api_url_history` if there is saved such a response from the past. If not, add desired error response for the resource e.g.:

```mysql
INSERT INTO mock_api_url_history SET 
    mock_api_url_id=12345,
    status=404,
    content_type='application/json',
    data='{"code": 404, "message": "Resource not found"}',
    created_at=NOW()
```

After setting `mock_api_url.mock_status = 404` for that resource you will get this 404 response.

###

For more information about configuration check [config/mock-api.php](https://github.com/lichtner/laravel-mock-api/blob/main/config/mock-api.php)

## FAQ

*Why does MockApi manage only GET requests?*

Because I don't need the other ones ;-) and I don't know how to deal with them ;-) If you have any suggestions on how to deal with POST, PUT, PATCH, DELETE requests start [discussion](https://github.com/lichtner/laravel-mock-api/discussions/new?category=ideas). 

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Credits

- [Marek Lichtner](https://github.com/lichtner)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
