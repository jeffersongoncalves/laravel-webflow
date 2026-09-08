<?php

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Jeffersongoncalves\Webflow\Exceptions\WebflowException;
use Jeffersongoncalves\Webflow\Facades\Webflow;

beforeEach(function () {
    Http::preventStrayRequests();
});

it('lists sites', function () {
    Http::fake(['api.webflow.com/v2/sites' => Http::response(['sites' => [['id' => 'site1']]])]);

    $result = Webflow::listSites();

    expect($result['sites'][0]['id'])->toBe('site1');
    Http::assertSent(fn (Request $request) => $request->method() === 'GET'
        && $request->hasHeader('Authorization', 'Bearer fake-api-token'));
});

it('gets a site', function () {
    Http::fake(['api.webflow.com/v2/sites/site1' => Http::response(['id' => 'site1', 'displayName' => 'My Site'])]);

    $result = Webflow::getSite('site1');

    expect($result['displayName'])->toBe('My Site');
});

it('publishes a site', function () {
    Http::fake(['api.webflow.com/v2/sites/site1/publish' => Http::response(['customDomains' => [], 'publishToWebflowSubdomain' => true])]);

    $result = Webflow::publishSite('site1', publishToWebflowSubdomain: true);

    expect($result['publishToWebflowSubdomain'])->toBeTrue();
    Http::assertSent(fn (Request $request) => $request->method() === 'POST'
        && $request['publishToWebflowSubdomain'] === true);
});

it('lists collections for a site', function () {
    Http::fake(['api.webflow.com/v2/sites/site1/collections' => Http::response(['collections' => [['id' => 'col1']]])]);

    $result = Webflow::listCollections('site1');

    expect($result['collections'][0]['id'])->toBe('col1');
});

it('lists collection items', function () {
    Http::fake(['api.webflow.com/v2/collections/col1/items*' => Http::response(['items' => [['id' => 'item1']]])]);

    $result = Webflow::listCollectionItems('col1', limit: 10, offset: 0);

    expect($result['items'][0]['id'])->toBe('item1');
    Http::assertSent(fn (Request $request) => $request['limit'] === 10 && $request['offset'] === 0);
});

it('gets a collection item', function () {
    Http::fake(['api.webflow.com/v2/collections/col1/items/item1' => Http::response(['id' => 'item1'])]);

    $result = Webflow::getCollectionItem('col1', 'item1');

    expect($result['id'])->toBe('item1');
});

it('creates a collection item', function () {
    Http::fake(['api.webflow.com/v2/collections/col1/items' => Http::response(['id' => 'item1'], 202)]);

    $result = Webflow::createCollectionItem('col1', ['name' => 'New Item'], isDraft: true);

    expect($result['id'])->toBe('item1');
    Http::assertSent(fn (Request $request) => $request->method() === 'POST'
        && $request['fieldData']['name'] === 'New Item'
        && $request['isDraft'] === true);
});

it('updates a collection item', function () {
    Http::fake(['api.webflow.com/v2/collections/col1/items/item1' => Http::response(['id' => 'item1'])]);

    $result = Webflow::updateCollectionItem('col1', 'item1', ['name' => 'Updated']);

    expect($result['id'])->toBe('item1');
    Http::assertSent(fn (Request $request) => $request->method() === 'PATCH'
        && $request['fieldData']['name'] === 'Updated');
});

it('publishes collection items in bulk', function () {
    Http::fake(['api.webflow.com/v2/collections/col1/items/publish' => Http::response(['itemIds' => ['item1', 'item2']])]);

    $result = Webflow::publishCollectionItems('col1', ['item1', 'item2']);

    expect($result['itemIds'])->toBe(['item1', 'item2']);
    Http::assertSent(fn (Request $request) => $request['itemIds'] === ['item1', 'item2']);
});

it('lists forms for a site', function () {
    Http::fake(['api.webflow.com/v2/sites/site1/forms' => Http::response(['forms' => [['id' => 'form1']]])]);

    $result = Webflow::listForms('site1');

    expect($result['forms'][0]['id'])->toBe('form1');
});

it('lists form submissions', function () {
    Http::fake(['api.webflow.com/v2/forms/form1/submissions*' => Http::response(['formSubmissions' => [['id' => 'sub1']]])]);

    $result = Webflow::listFormSubmissions('form1', limit: 5);

    expect($result['formSubmissions'][0]['id'])->toBe('sub1');
    Http::assertSent(fn (Request $request) => $request['limit'] === 5);
});

it('throws a WebflowException on a non-2xx response', function () {
    Http::fake(['api.webflow.com/v2/sites/missing' => Http::response(['message' => 'Site not found'], 404)]);

    Webflow::getSite('missing');
})->throws(WebflowException::class);

it('exposes the status code and decoded error body on failure', function () {
    Http::fake(['api.webflow.com/v2/sites/missing' => Http::response(['message' => 'Site not found'], 404)]);

    try {
        Webflow::getSite('missing');
    } catch (WebflowException $exception) {
        expect($exception->getCode())->toBe(404);
        expect($exception->getMessage())->toContain('404')->toContain('Site not found');
        expect($exception->errorBody())->toBe(['message' => 'Site not found']);

        return;
    }

    $this->fail('Expected WebflowException was not thrown.');
});
