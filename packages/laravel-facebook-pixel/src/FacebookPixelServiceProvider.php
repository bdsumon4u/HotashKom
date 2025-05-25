<?php

namespace Hotash\FacebookPixel;

use Hotash\FacebookPixel\Components\Body;
use Hotash\FacebookPixel\Components\Head;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FacebookPixelServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('meta-pixel')
            ->hasConfigFile('meta-pixel')
            ->hasViewComponents(
                'metapixel',
                Head::class,
                Body::class
            )
            ->hasViews();
    }

    public function packageBooted(): void {}

    public function registeringPackage(): void
    {
        $this->app->singleton(MetaPixel::class, function () {
            return new MetaPixel;
        });
    }
}
