<?php

return [
    'name' => 'Core',

    /*
    |--------------------------------------------------------------------------
    | Application Information
    |--------------------------------------------------------------------------
    */
    'info' => [
        'version'     => env('APP_VERSION', 'v0.3.x-alpha'),
        'series_code' => env('APP_SERIES_CODE', 'ARC01-USER'),
        'status'      => env('APP_STATUS', 'Active Development'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Author Information
    |--------------------------------------------------------------------------
    */
    'author' => [
        'name'   => env('APP_AUTHOR_NAME', 'Reas Vyn'),
        'github' => env('APP_AUTHOR_GITHUB', 'https://github.com/reasnov'),
        'email'  => env('APP_AUTHOR_EMAIL', 'reasnov.official@gmail.com'),
    ],
];