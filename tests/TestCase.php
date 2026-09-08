<?php

namespace Jeffersongoncalves\Webflow\Tests;

use Jeffersongoncalves\Webflow\WebflowServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            WebflowServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('webflow.token', 'fake-api-token');
        $app['config']->set('webflow.base_url', 'https://api.webflow.com/v2');
    }
}
