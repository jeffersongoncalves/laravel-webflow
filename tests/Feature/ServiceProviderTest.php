<?php

use Jeffersongoncalves\Webflow\Facades\Webflow as WebflowFacade;
use Jeffersongoncalves\Webflow\Webflow;

it('registers the webflow singleton', function () {
    expect(app(Webflow::class))->toBeInstanceOf(Webflow::class);
    expect(app(Webflow::class))->toBe(app(Webflow::class));
});

it('resolves the facade to the webflow class', function () {
    expect(WebflowFacade::getFacadeRoot())->toBeInstanceOf(Webflow::class);
});

it('merges the package config', function () {
    expect(config('webflow.base_url'))->toBe('https://api.webflow.com/v2');
});
