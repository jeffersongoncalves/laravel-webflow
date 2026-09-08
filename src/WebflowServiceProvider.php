<?php

namespace Jeffersongoncalves\Webflow;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class WebflowServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-webflow')
            ->hasConfigFile();
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(Webflow::class, fn () => new Webflow(
            token: (string) config('webflow.token'),
            baseUrl: (string) config('webflow.base_url'),
        ));
    }
}
