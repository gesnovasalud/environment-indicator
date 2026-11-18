<?php

use Filament\Facades\Filament;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;

beforeEach(function () {
    $panel = Filament::getPanel('admin');
    Filament::setCurrentPanel($panel);
    $panel->boot();
});

function renderBodyStart(): string
{
    return FilamentView::renderHook(PanelsRenderHook::BODY_START)->toHtml();
}

it('renders the bar and the version badge in production', function () {
    config(['app.env' => 'production']);

    expect(renderBodyStart())
        ->toContain('environment-indicator-bar')
        ->toContain('9.9.9');
});

it('renders nothing when the plugin is not visible', function () {
    config(['test.visible' => false]);

    expect(renderBodyStart())->not->toContain('environment-indicator-bar');
});

it('registers the plugin in the panel', function () {

    expect(Filament::getPanel('admin')->getPlugin('environment-indicator'))->not->toBeNull();
});
