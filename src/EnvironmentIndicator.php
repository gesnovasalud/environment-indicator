<?php

namespace Gesnovasalud\EnvironmentIndicator;

use Filament\Support\Colors\Color;
use Gesnovasalud\EnvironmentIndicator\Support\BranchResolver;
use Illuminate\Support\Str;

class EnvironmentIndicator
{
    public function __construct(protected BranchResolver $branchResolver) {}

    /**
     * Builds the data used by the indicator view.
     *
     * @param  array<string, mixed>  $overrides  Plugin-level overrides (colors, version, showBranch).
     * @return array{color: string, textColor: string, primary: string, secondary: ?string, title: string}|null
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
        $prefix = $config['version_prefixes'][$environment] ?? null;
        $versionLabel = null;
        $hashLength = $config['hash_length'] ?? 5;

        if (filled($version) && $prefix !== null) {
            $versionLabel = "{$prefix}-" . $this->formatVersion(Str::chopStart((string) $version, "{$prefix}-"), $hashLength);
        } elseif (filled($version) && in_array($environment, $config['show_version_in'] ?? [], true)) {
            $versionLabel = $this->formatVersion((string) $version, $hashLength);
        }

        $showBranch = is_array($config['show_branch_in'] ?? null)
            ? in_array($environment, $config['show_branch_in'], true)
            : ! in_array($environment, $config['hide_branch_in'] ?? [], true);
        $branch = $showBranch ? $this->branchResolver->resolve() : null;

        $showOnlyBranch = filled($branch) && in_array($environment, $config['show_only_branch_in'] ?? [], true);
        $primary = $versionLabel ?? ($showOnlyBranch ? $branch : strtoupper($environment));
        $secondary = (filled($branch) && ! $showOnlyBranch) ? "({$branch})" : null;
        $detail = trim(($versionLabel ?? ($showOnlyBranch ? $branch : '')) . " {$secondary}");

        return [
            'color' => $shades[$config['shades'][$colorName] ?? 500],
            'textColor' => in_array($colorName, $config['dark_text_on'] ?? [], true) ? '#000' : '#fff',
            'primary' => $primary,
            'secondary' => $secondary,
            'title' => trim(__('environment-indicator::environment-indicator.environment', ['environment' => ucfirst($environment)]) . ' ' . $detail),
        ];
    }

    /**
     * Removes the "v" of a tag and shortens a git hash.
     */
    protected function formatVersion(string $version, int $hashLength): string
    {
        if (preg_match('/^[0-9a-f]{7,40}$/', $version)) {
            return substr($version, 0, $hashLength);
        }

        return preg_replace('/^v(?=\d)/', '', $version);
    }
}
