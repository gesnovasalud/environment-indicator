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
    expect(indicator()->resolve('staging')['color'])->toBe(Color::Orange[500]);
});

it('falls back to the default color for unknown environments', function () {
    expect(indicator()->resolve('qa')['color'])->toBe(Color::Purple[500]);
});

it('shows the version as the main text in production', function () {
    config(['app.version' => 'v1.2.3']);

    expect(indicator()->resolve('production'))
        ->primary->toBe('v1.2.3')
        ->secondary->toBeNull();
});

it('shows the branch only in environments where it is enabled', function () {
    expect(indicator()->resolve('local')['secondary'])->toBe('(feature/x)')
        ->and(indicator()->resolve('production')['secondary'])->toBeNull();
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
