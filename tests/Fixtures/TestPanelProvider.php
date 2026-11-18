<?php

namespace Gesnovasalud\EnvironmentIndicator\Tests\Fixtures;

use Filament\Panel;
use Filament\PanelProvider;
use Gesnovasalud\EnvironmentIndicator\EnvironmentIndicatorPlugin;

class TestPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->plugin(EnvironmentIndicatorPlugin::make()->version('9.9.9')->visible(fn (): bool => (bool) config('test.visible', true)));
    }
}
