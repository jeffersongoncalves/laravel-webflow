<div class="filament-hidden">

![Laravel Webflow](https://raw.githubusercontent.com/jeffersongoncalves/laravel-webflow/main/art/jeffersongoncalves-laravel-webflow.png)

</div>

# Laravel Webflow

[![Latest Version on Packagist](https://img.shields.io/packagist/v/jeffersongoncalves/laravel-webflow.svg?style=flat-square)](https://packagist.org/packages/jeffersongoncalves/laravel-webflow)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/jeffersongoncalves/laravel-webflow/tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/jeffersongoncalves/laravel-webflow/actions?query=workflow%3Atests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/jeffersongoncalves/laravel-webflow/pint.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/jeffersongoncalves/laravel-webflow/actions?query=workflow%3A%22Fix+PHP+code+styling%22+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/jeffersongoncalves/laravel-webflow.svg?style=flat-square)](https://packagist.org/packages/jeffersongoncalves/laravel-webflow)
[![License](https://img.shields.io/packagist/l/jeffersongoncalves/laravel-webflow.svg?style=flat-square)](LICENSE.md)

A Laravel wrapper for the [Webflow REST API v2](https://developers.webflow.com/data/reference/rest-introduction). Covers sites, CMS collections/items, forms and publishing through a simple, typed API built on Laravel's `Http` client — no Webflow SDK dependency.

## Features

- Sites: `listSites`, `getSite`, `publishSite`
- Collections: `listCollections`
- Collection items: `listCollectionItems`, `getCollectionItem`, `createCollectionItem`, `updateCollectionItem`, `publishCollectionItems` (bulk)
- Forms: `listForms`, `listFormSubmissions`
- Throws `WebflowException` (with the HTTP status and the original API error body) on any non-2xx response

## Installation

You can install the package via composer:

```bash
composer require jeffersongoncalves/laravel-webflow
```

Publish the config file with:

```bash
php artisan vendor:publish --tag="laravel-webflow-config"
```

This is the contents of the published config file:

```php
return [
    'token' => env('WEBFLOW_API_TOKEN'),
    'base_url' => env('WEBFLOW_BASE_URL', 'https://api.webflow.com/v2'),
];
```

Add your Webflow [API token](https://developers.webflow.com/data/reference/authentication) to your `.env`:

```env
WEBFLOW_API_TOKEN=your-webflow-api-token
```

## Usage

You can use the `Webflow` facade, or inject `Jeffersongoncalves\Webflow\Webflow` wherever you need it. Every method returns the decoded JSON response body as an array.

### Sites

```php
use Jeffersongoncalves\Webflow\Facades\Webflow;

$sites = Webflow::listSites();

$site = Webflow::getSite('siteId');

Webflow::publishSite('siteId', customDomains: ['www.example.com'], publishToWebflowSubdomain: true);
```

### Collections

```php
$collections = Webflow::listCollections('siteId');
```

### Collection items

```php
$items = Webflow::listCollectionItems('collectionId', limit: 10, offset: 0);

$item = Webflow::getCollectionItem('collectionId', 'itemId');

Webflow::createCollectionItem('collectionId', fieldData: [
    'name' => 'My New Item',
    'slug' => 'my-new-item',
], isDraft: true);

Webflow::updateCollectionItem('collectionId', 'itemId', fieldData: [
    'name' => 'Updated Item',
]);

Webflow::publishCollectionItems('collectionId', ['itemId1', 'itemId2']);
```

### Forms

```php
$forms = Webflow::listForms('siteId');

$submissions = Webflow::listFormSubmissions('formId', limit: 25, offset: 0);
```

### Error handling

Any non-2xx API response throws `Jeffersongoncalves\Webflow\Exceptions\WebflowException`, which exposes the decoded error body:

```php
use Jeffersongoncalves\Webflow\Exceptions\WebflowException;

try {
    Webflow::publishSite('siteId');
} catch (WebflowException $e) {
    logger()->error($e->getMessage(), $e->errorBody());
}
```

### Rate limits

Webflow enforces 60 requests/minute per site for general endpoints and 10 requests/minute for publishing endpoints. This package does not throttle requests client-side — a `WebflowException` is thrown on `429 Too Many Requests` like any other non-2xx response, so handle it (e.g. retry with backoff) if you publish in bulk.

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](.github/SECURITY.md) on how to report security vulnerabilities.

## Credits

- [Jefferson Simão Gonçalves](https://github.com/jeffersongoncalves)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
