<?php

namespace Gesnovasalud\EnvironmentIndicator;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class EnvironmentIndicatorServiceProvider extends PackageServiceProvider
{
    public static string $name = 'environment-indicator';

    public function configurePackage(Package $package): void
    {
        $package
            ->name(static::$name)
            ->hasConfigFile()
            ->hasTranslations()
            ->hasViews(static::$name);
    }
}
