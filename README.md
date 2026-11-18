# Environment Indicator for Filament

[![Latest Version on Packagist](https://img.shields.io/packagist/v/gesnovasalud/environment-indicator.svg?style=flat-square)](https://packagist.org/packages/gesnovasalud/environment-indicator)
[![Tests](https://img.shields.io/github/actions/workflow/status/gesnovasalud/environment-indicator/tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/gesnovasalud/environment-indicator/actions/workflows/tests.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/gesnovasalud/environment-indicator.svg?style=flat-square)](https://packagist.org/packages/gesnovasalud/environment-indicator)

A Filament plugin that shows a thin colored bar at the top of your panel and a badge with the current environment, so nobody mistakes staging for production. The badge can also show the application version and, in local, the git branch.

- Colors per environment, using Filament's color palette
- Version shown from `APP_VERSION`
- Git branch read from `.git/HEAD` (no shell commands), shown only in `local` by default
- Visible only to authenticated users by default

## Requirements

- PHP 8.3+
- Filament 5

## Installation

```bash
composer require gesnovasalud/environment-indicator
```

Register the plugin in your panel provider:

```php
use Gesnovasalud\EnvironmentIndicator\EnvironmentIndicatorPlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        ->plugin(EnvironmentIndicatorPlugin::make());
}
```

The environment comes from `APP_ENV`. To show a version, set `APP_VERSION` (for example `v1.4.2`) in your `.env` or let your deployment pipeline write it from the release tag. If it is not set, only the environment name is displayed.

## Configuration

Publish the config file if you want to change the defaults:

```bash
php artisan vendor:publish --tag="environment-indicator-config"
```

```php
return [
    'colors' => [
        'production' => 'blue',
        'staging' => 'orange',
        'development' => 'yellow',
        'demo' => 'cyan',
        'local' => 'purple',
    ],
    'default_color' => 'purple',
    'show_version_in' => ['production', 'staging'],
    'show_branch_in' => ['local'],
    'version' => env('APP_VERSION'),
    'hidden_in' => [],
];
```

In `production` the version replaces the environment name in the badge. In the other environments listed in `show_version_in` it is shown next to it.

## Plugin options

Options set on the plugin take priority over the config file and can use closures:

```php
EnvironmentIndicatorPlugin::make()
    ->colors(['staging' => 'amber'])
    ->version('2.0.0')
    ->showBranch()
    ->visible(fn () => auth()->user()?->isAdmin());
```

| Method | Description |
|---|---|
| `colors(array)` | Filament color name per environment |
| `version(string\|Closure\|null)` | Overrides the version |
| `showBranch()` / `hideBranch()` | Forces the git branch on or off in the current environment |
| `visible(bool\|Closure)` | Replaces the default "authenticated users only" rule |

> Showing the branch outside `local` exposes internal information to every user who sees the badge. Enable it only where that is acceptable.

## Translations

The badge tooltip is translated (`en`, `es`). Publish them with:

```bash
php artisan vendor:publish --tag="environment-indicator-translations"
```

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

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
