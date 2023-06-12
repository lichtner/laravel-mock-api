# Laravel MockApi

*Laravel MockAPI is a powerful yet lightweight library designed for mocking external APIs and web services.*

[![Latest Version on Packagist](https://img.shields.io/packagist/v/lichtner/laravel-mock-api.svg?style=flat-square)](https://packagist.org/packages/lichtner/laravel-mock-api)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/lichtner/laravel-mock-api/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/lichtner/laravel-mock-api/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/lichtner/laravel-mock-api/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/lichtner/laravel-mock-api/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/lichtner/laravel-mock-api.svg?style=flat-square)](https://packagist.org/packages/lichtner/laravel-mock-api)

## Why?

Are you using external APIs and web services during development that are also undergoing development? Are they unstable, slow, occasionally returning incorrect results, or unexpectedly unavailable? Are they causing you headaches? Me too! That was the reason why I created MockApi.

After installation and setup, MockApi will save all requests from your external web services in the background, and when they are unavailable, return them just like real APIs.

## Installation

```bash
composer require lichtner/laravel-mock-api
```

Now you can publish config file and run the migrations with:

```bash
php artisan mock-api:install
```

## Setup

### Mocking GET request

Make a simple class that wrap all your `Http::get()` requests  e.g.:

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

Then everywhere in your code replace:

```php
Http::get($url);
```

with: 

```php
HttpMock::get($url);
```

It is done! Now you can start mocking all your external APIs GET requests (and maybe colleagues who are developing them ;-)

### Mocking POST, PUT, PATCH, DELETE requests

You can also mock mutation requests too, but it is not necessary if you don't need to. 

E.g. for mocking POST requests add this in `app/HttpMock.php`

```php
class HttpMock
{
    /* ... here is function get() */

    public static function post(string $url, array $data): Response
    {
        MockApi::init($url, $data, 'POST');

        $response = Http::post($url, $data);

        MockApi::log($url, $response, 'POST');

        return $response;
    }
}
```

Then everywhere in your code replace:

```php
Http::post($url, $data);
```

with:

```php
HttpMock::post($url, $data);
```

Mocking other HTTP methods are very similar. Check in example application file [/app/HttpMock.php](https://github.com/lichtner/laravel-mock-api-example/blob/main/app/HttpMock.php) 


## Example application

You can check full usage of MockApi in [example application](https://github.com/lichtner/laravel-mock-api-example)

## Security 

By default, MockApi works only on the `local` environment! It does not affect the other ones!

## Usage

After you did the changes described in [Setup](#setup), all HTTP requests will be saved in MockApi tables. But they won't be used.

### Mock all resources

For returning mocked data add this in the `.env` file:

```yaml
MOCK_API=true
```

You can set `MOCK_API=true` immediately after installation. From that moment all external resources will return the last saved successful responses. 

If you try to request resource which has not been saved yet, first a real request is made and saved in mock api tables, and then returned.

After your web services are back, you can change it to:

```yaml
MOCK_API=false
```

## Mock management

You can manage your mocks in tables `mock_api_url` and `mock_api_url_history`.

### Mock only some resources

By default, is in table set `mock_api_url.mock = 1`. It means resource is mocked. If you want to mock only some of them, set the others to `0`. 

### Mock data from the past

By default, MockApi returns the last saved successful responses (status < 300). If some of the resources returns status 2000 with some data, but they are incorrect, and yesterday's were fine, set in table `mock_api_url.mock_before` datetime for all incorrect resources.

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

After setting in table `mock_api_url.mock_status = 404` for that resource you will get this 404 response.

### Mock mutation requests

Mutation requests like POST, PUT, PATCH, DELETE don't put anything in `mock_api_url_history.data` field. Without any changes they returns same data, you send them. E.g. for:

```php
$response = HttpMock::post("$api/articles", [
        'userId' => 5,
        'title' => 'title 1',
        'body' => 'body 1',
    ]);
```

response is:

```json
{
    "userId": 5,
    "title": "title 1",
    "body": "body 1"
}
```

Especially for POST request your real API probably add `id` field. To simulate this behaviour you can update field `data` for specific row:

```mysql
UPDATE mock_api_url_history SET data='{"id": 1234}' WHERE id = 777;
```

Then same requests response will be:

```json
{
    "userId": 5,
    "title": "title 1",
    "body": "body 1",
    "id": 1234
}
```

You can add anything in mutation responses (e.g. uuid, etc.). These fields will be merged recursively with your json POST data. 

### Mock two requests with same url and method

In table `mock_api_url` is set unique key for (method, url) so you are not able to mock two request with same method and url which is expected behavior. But for specific situation you want to. Maybe you want to mock two different articles with different titles with resource `POST /articles`. To do this you can create special class for that purpose.

```php
class HttpMockArticles
{
    public static function post(string $url, array $data): Response
    {
        MockApi::init("$url/$data[title]", $data, 'POST');
        
        $response = Http::post($url, $data);
        
        MockApi::log("$url/$data[title]", $response, 'POST');

        return $response;
    }
}
```

As you can see you can modify `$url` parameter only for `MockApi::init()` and `MockApi::log()` functions, but not for real request `Http::post()`. So two articles with different titles will be saved.

## Config 

For more information about configuration check [config/mock-api.php](https://github.com/lichtner/laravel-mock-api/blob/main/config/mock-api.php)

## Testing

```bash
composer test
```

## Changelog

For changelog check [releases](https://github.com/lichtner/laravel-mock-api/releases).

## Credits

- [Marek Lichtner](https://github.com/lichtner)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
