# Laravel MockApi

*Easy to use, but the powerful micro library for mocking external API*

[![Latest Version on Packagist](https://img.shields.io/packagist/v/lichtner/laravel-mock-api.svg?style=flat-square)](https://packagist.org/packages/lichtner/laravel-mock-api)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/lichtner/laravel-mock-api/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/lichtner/laravel-mock-api/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/lichtner/laravel-mock-api/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/lichtner/laravel-mock-api/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/lichtner/laravel-mock-api.svg?style=flat-square)](https://packagist.org/packages/lichtner/laravel-mock-api)

## Why?

Are you using external APIs and web services during development that are also in the development process? Are they unstable, slow, sometimes returning incorrect results, or unexpectedly unavailable? Are they causing you headaches? Me too! That was the reason why I created MockApi.

MockApi solves these problems for you. It saves all GET requests from your external web services, and when they are unavailable, it returns saved data like real API. You no longer have to worry about it.

## Installation

You can install the package via composer:

```bash
composer require lichtner/laravel-mock-api
```

Now you can publish config and run the migrations with:

```bash
php artisan mock-api:install
```

## Setup

Make simple class which wrap all your `Http::get()` request. Create file e.g.: 

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

Now everywhere in your code where you call:

```php
Http::get($url);
```

replace it with: 

```php
HttpMock::get($url);
```

It is done! Now you can start mocking all your external API (and maybe colleagues who are developing them ;-)

## Security 

**Don't worry. By default MockApi works only on `local` environment! It has no effect on other ones!**

## Usage

After you did the changes described in [Setup](#setup) all HTTP GET request will be saved in MockApi tables. But they will not be used. 

You can manage mocking via environment variables in `.env` file.

### Mock all webservices

When something went wrong and your webservices become unavailable, put in `.env`:

```yaml
MOCK_API=true
```

from that moment all webservices start returning last saved responses. 

After your webservices are back, and you want to get real responses just change it to:

```yaml
MOCK_API=false
```

### Mock only some of webservices

In db table `mock_api_url` all rows have in column `mock` default value `1`. If you want to mock only 1 or more webservices, set other to `0`. 

### Mock data from the past

By default, MockApi returns last saved success responses (<300). But maybe all today's responses are messed up because on the testing server of your webservice was installed code with some bug. But you know yesterday result were fine. So use this:

```yaml
MOCK_API_DATETIME_IS_LESS_THAN="YYYY-MM-DD HH:mm:ss"
```
you get results that are less than given datetime.

### Mock error requests

Sometimes you may want to develop how your app reacts to error API responses. Check table `mock_api_url_history` if there is such a response. If not add desired error response for API e.g:

```mysql
INSERT INTO mock_api_url_history SET 
    mock_api_url_id=<some-url-id>,
    status=404,
    content_type='application/json',
    data='{"code": 404, "message": "User not found"}'                                  
```

Then in `mock_api_url` set `mock_status` = 404.

###

For more information about configuration check [config/mock-api.php](https://github.com/lichtner/laravel-mock-api/blob/main/config/mock-api.php)

## FAQ

*Why does MockApi manage only GET requests?*

Because simply I don't know how to deal with the others ;-) Imagine app call (e.g. `PUT /users/7`) and now what? Probably you have to find all GET request at least `GET /users/7` and `GET /users` and update them. But information about that user can also be part of other responses e.g. part of the `GET /profile`, or there are many users and users resource is paginated etc. If you have any suggestion how to deal with POST, PUT, PATCH, DELETE requests start [discussion](https://github.com/lichtner/laravel-mock-api/discussions/new?category=ideas). 

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Credits

- [Marek Lichtner](https://github.com/lichtner)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
