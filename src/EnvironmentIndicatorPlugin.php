<?php

namespace Gesnovasalud\EnvironmentIndicator;

use Closure;
use Filament\Contracts\Plugin;
use Filament\Facades\Filament;
use Filament\Panel;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Blade;

class EnvironmentIndicatorPlugin implements Plugin
{
    /** @var array<string, string> */
    protected array $colors = [];

    protected string | Closure | null $version = null;

    protected ?bool $shouldShowBranch = null;

    protected bool | Closure | null $visible = null;

    public function getId(): string
    {
        return 'environment-indicator';
    }

    /**
     * @param  array<string, string>  $colors  Filament color name per environment.
     */
    public function colors(array $colors): static
    {
        $this->colors = $colors;

        return $this;
    }

    public function version(string | Closure | null $version): static
    {
        $this->version = $version;

        return $this;
    }

    public function showBranch(bool $condition = true): static
    {
        $this->shouldShowBranch = $condition;

        return $this;
    }

    public function hideBranch(): static
    {
        return $this->showBranch(false);
    }

    public function visible(bool | Closure $condition = true): static
    {
        $this->visible = $condition;

        return $this;
    }

    public function register(Panel $panel): void
    {
        $panel->renderHook(
            PanelsRenderHook::BODY_START,
            fn (): string => Blade::render(
                '@include("environment-indicator::indicator", ["data" => $data])',
                ['data' => $this->resolveData()],
            ),
        );
    }

    public function boot(Panel $panel): void {}

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }

    /**
     * @return array{color: string, primary: string, secondary: ?string, title: string}|null
     */
    protected function resolveData(): ?array
    {
        $isVisible = $this->visible === null
            ? Filament::auth()->check()
            : (bool) value($this->visible);

        if (! $isVisible) {
            return null;
        }

        $environment = (string) config('app.env');
        $overrides = [];

        if ($this->colors !== []) {
            $overrides['colors'] = $this->colors;
        }

        if ($this->version !== null) {
            $overrides['version'] = value($this->version);
        }

        if ($this->shouldShowBranch !== null) {
            $overrides['show_branch_in'] = $this->shouldShowBranch ? [$environment] : [];
        }

        return app(EnvironmentIndicator::class)->resolve($environment, $overrides);
    }
}
