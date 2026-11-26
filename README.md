# Environment Indicator for Filament

[![Latest Version on Packagist](https://img.shields.io/packagist/v/gesnovasalud/environment-indicator.svg?style=flat-square)](https://packagist.org/packages/gesnovasalud/environment-indicator)
[![Tests](https://img.shields.io/github/actions/workflow/status/gesnovasalud/environment-indicator/tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/gesnovasalud/environment-indicator/actions/workflows/tests.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/gesnovasalud/environment-indicator.svg?style=flat-square)](https://packagist.org/packages/gesnovasalud/environment-indicator)

A Filament plugin that shows a thin colored bar at the top of your panel and a badge with the current environment, so nobody mistakes staging for production. Depending on the environment, the badge shows the application version, the commit hash or the git branch.

- Colors per environment, using Filament's color palette
- Version shown from `APP_VERSION`
- Git branch read from `.git/HEAD` (no shell commands), shown by default only outside deployed environments (such as `local`)
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
        'staging' => 'amber',
        'qa' => 'amber',
        'development' => 'green',
        'dev' => 'green',
        'demo' => 'green',
        'local' => 'purple',
    ],
    'default_color' => 'purple',
    'shades' => ['green' => 700],
    'dark_text_on' => ['amber', 'yellow', 'lime'],
    'show_version_in' => ['production'],
    'version_prefixes' => ['development' => 'dev', 'dev' => 'dev', 'demo' => 'demo', 'staging' => 'qa', 'qa' => 'qa'],
    'hash_length' => 5,
    'show_only_branch_in' => ['local'],
    'show_branch_in' => null,
    'hide_branch_in' => ['production', 'staging', 'qa', 'development', 'dev', 'demo'],
    'version' => env('APP_VERSION'),
    'hidden_in' => [],
];
```

The git branch is shown in every environment not listed in `hide_branch_in` (for example `local` or `testing`). If `show_branch_in` is an array, the branch is shown only in those environments and `hide_branch_in` is ignored.

| Environment | Color | Badge |
|---|---|---|
| `local` | purple | `feature/my-branch` |
| `dev`, `development` | dark green | `dev-1a2b3` |
| `demo` | dark green | `demo-1a2b3` |
| `staging`, `qa` | amber | `qa-1.2.3-rc.0` |
| `production` | blue | `1.2.3` |

For `dev` and `demo`, set `APP_VERSION` to the commit hash, with or without the prefix. Git hashes are shortened to `hash_length` characters. A leading `v` is removed from release tags.

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

> The branch is internal information visible to every user who sees the badge. Environments not listed in `hide_branch_in` show it, including custom ones.

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
