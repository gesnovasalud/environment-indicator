<?php

return [

    /*
    | Filament color (name from Filament\Support\Colors\Color) per environment.
    */
    'colors' => [
        'production' => 'blue',
        'staging' => 'orange',
        'development' => 'yellow',
        'demo' => 'cyan',
        'local' => 'purple',
    ],

    'default_color' => 'purple',

    /*
    | Environments in which the version / the git branch are shown in the badge.
    | The branch is hidden by default outside "local" so it is never leaked.
    */
    'show_version_in' => ['production', 'staging'],

    'show_branch_in' => ['local'],

    /*
    | Application version. Reads APP_VERSION and falls back to config('app.version').
    */
    'version' => env('APP_VERSION'),

    /*
    | Environments in which the indicator is hidden entirely.
    */
    'hidden_in' => [],

];
