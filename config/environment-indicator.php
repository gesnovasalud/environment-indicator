<?php

return [

    /*
    | Filament color (name from Filament\Support\Colors\Color) per environment.
    */
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

    /*
    | Shade of each color (50 to 950). Colors not listed use 500.
    */
    'shades' => [
        'green' => 700,
    ],

    /*
    | Light colors on which the badge uses black text instead of white.
    */
    'dark_text_on' => ['amber', 'yellow', 'lime'],

    /*
    | Environments in which the badge shows only the version, without the
    | environment name. A leading "v" is removed ("v1.2.3" is shown as "1.2.3").
    */
    'show_version_in' => ['production'],

    /*
    | Environments in which the badge shows "prefix-version", for example
    | "dev-1a2b3c4" or "qa-1.2.3". The version may already include the prefix,
    | a leading "v" is removed and git hashes are shortened to "hash_length"
    | characters.
    */
    'version_prefixes' => [
        'development' => 'dev',
        'dev' => 'dev',
        'demo' => 'demo',
        'staging' => 'qa',
        'qa' => 'qa',
    ],

    'hash_length' => 5,

    /*
    | Environments in which the badge shows only the git branch. The
    | environment name is used when the branch cannot be read.
    */
    'show_only_branch_in' => ['local'],

    /*
    | The git branch is shown in every environment except the ones listed in
    | "hide_branch_in". Set "show_branch_in" to an array to show it only in
    | those environments instead ("hide_branch_in" is then ignored).
    */
    'show_branch_in' => null,

    'hide_branch_in' => ['production', 'staging', 'qa', 'development', 'dev', 'demo'],

    /*
    | Application version. Reads APP_VERSION and falls back to config('app.version').
    */
    'version' => env('APP_VERSION'),

    /*
    | Environments in which the indicator is hidden entirely.
    */
    'hidden_in' => [],

];
