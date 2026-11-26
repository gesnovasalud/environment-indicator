<?php

use Filament\Support\Colors\Color;
use Gesnovasalud\EnvironmentIndicator\EnvironmentIndicator;
use Gesnovasalud\EnvironmentIndicator\Support\BranchResolver;

function indicator(?string $branch = 'feature/x'): EnvironmentIndicator
{
    $resolver = Mockery::mock(BranchResolver::class);
    $resolver->shouldReceive('resolve')->andReturn($branch);

    return new EnvironmentIndicator($resolver);
}

it('uses the color mapped to the environment', function () {
    expect(indicator()->resolve('staging')['color'])->toBe(Color::Amber[500]);
});

it('falls back to the default color for unknown environments', function () {
    expect(indicator()->resolve('sandbox')['color'])->toBe(Color::Purple[500]);
});

it('shows only the branch in local', function () {
    expect(indicator()->resolve('local'))
        ->primary->toBe('feature/x')
        ->secondary->toBeNull();
});

it('shows the environment name in local when there is no branch', function () {
    expect(indicator(branch: null)->resolve('local')['primary'])->toBe('LOCAL');
});

it('shows the environment and the branch in other environments that do not hide it', function () {
    expect(indicator()->resolve('testing'))
        ->primary->toBe('TESTING')
        ->secondary->toBe('(feature/x)');
});

it('hides the branch by default in deployed environments', function (string $environment) {
    config(['app.version' => null]);

    expect(indicator()->resolve($environment)['secondary'])->toBeNull();
})->with(['production', 'staging', 'qa', 'development', 'dev', 'demo']);

it('shows the branch only in show_branch_in when it is an array', function () {
    config(['environment-indicator.show_branch_in' => ['demo']]);

    expect(indicator()->resolve('demo')['secondary'])->toBe('(feature/x)')
        ->and(indicator()->resolve('local')['primary'])->toBe('LOCAL');
});

it('returns null for hidden environments', function () {
    config(['environment-indicator.hidden_in' => ['demo']]);

    expect(indicator()->resolve('demo'))->toBeNull();
});

it('reads the branch from .git/HEAD', function () {
    $dir = sys_get_temp_dir() . '/ei-' . uniqid();
    mkdir($dir . '/.git', 0777, true);
    file_put_contents($dir . '/.git/HEAD', "ref: refs/heads/main\n");

    expect((new BranchResolver)->resolve($dir))->toBe('main')
        ->and((new BranchResolver)->resolve($dir . '/missing'))->toBeNull();
});

it('reads the version from the package config before app.version', function () {
    config(['app.version' => 'v0.0.1', 'environment-indicator.version' => '1.0.0']);

    expect(indicator()->resolve('production')['primary'])->toBe('1.0.0');
});

it('shows the prefixed short hash in dev and demo', function (string $environment, string $version, string $expected) {
    config(['app.version' => $version]);

    expect(indicator()->resolve($environment))
        ->color->toBe(Color::Green[700])
        ->primary->toBe($expected)
        ->secondary->toBeNull();
})->with([
    ['dev', 'dev-1a2b3c4', 'dev-1a2b3'],
    ['development', 'dev-1a2b3c4', 'dev-1a2b3'],
    ['demo', 'demo-9f8e7d6', 'demo-9f8e7'],
    ['demo', '9f8e7d6', 'demo-9f8e7'],
    ['dev', '1a2b3c4d5e6f7a8b9c0d1a2b3c4d5e6f7a8b9c0d', 'dev-1a2b3'],
]);

it('formats release tags in production, staging and qa', function (string $environment, string $version, string $expected) {
    config(['app.version' => $version]);

    expect(indicator()->resolve($environment))
        ->primary->toBe($expected)
        ->secondary->toBeNull();
})->with([
    ['staging', 'v1.35.5-rc.0', 'qa-1.35.5-rc.0'],
    ['qa', '1.35.5-rc.0', 'qa-1.35.5-rc.0'],
    ['production', 'v1.43.45', '1.43.45'],
]);

it('uses black text only on light colors', function () {
    expect(indicator()->resolve('staging')['textColor'])->toBe('#000')
        ->and(indicator()->resolve('production')['textColor'])->toBe('#fff');
});

it('uses the primary blue in production', function () {
    expect(indicator()->resolve('production'))
        ->color->toBe(Color::Blue[500])
        ->textColor->toBe('#fff');
});
