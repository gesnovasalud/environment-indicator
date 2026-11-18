<?php

namespace Gesnovasalud\EnvironmentIndicator;

use Filament\Support\Colors\Color;
use Gesnovasalud\EnvironmentIndicator\Support\BranchResolver;

class EnvironmentIndicator
{
    public function __construct(protected BranchResolver $branchResolver) {}

    /**
     * Builds the data used by the indicator view.
     *
     * @param  array<string, mixed>  $overrides  Plugin-level overrides (colors, version, showBranch).
     * @return array{color: string, primary: string, secondary: ?string, title: string}|null
     */
    public function resolve(string $environment, array $overrides = []): ?array
    {
        $config = array_replace(config('environment-indicator', []), $overrides);

        if (in_array($environment, $config['hidden_in'] ?? [], true)) {
            return null;
        }

        $colorName = $config['colors'][$environment] ?? $config['default_color'] ?? 'purple';
        $shades = Color::all()[$colorName] ?? Color::Purple;

        $version = filled($config['version'] ?? null) ? $config['version'] : config('app.version');
        $showVersion = filled($version) && in_array($environment, $config['show_version_in'] ?? [], true);
        $branch = in_array($environment, $config['show_branch_in'] ?? [], true)
            ? $this->branchResolver->resolve()
            : null;

        $label = strtoupper($environment);
        $primary = ($showVersion && $environment === 'production') ? $version : $label;
        $secondary = ($showVersion && $environment !== 'production') ? $version : (filled($branch) ? "({$branch})" : null);

        return [
            'color' => $shades[500],
            'primary' => $primary,
            'secondary' => $secondary,
            'title' => trim(__('environment-indicator::environment-indicator.environment', ['environment' => ucfirst($environment)]) . ' ' . ($secondary ?? '')),
        ];
    }
}
