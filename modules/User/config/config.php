<?php

return [
    'name' => 'User',

    /*
    |--------------------------------------------------------------------------
    | User ID Type
    |--------------------------------------------------------------------------
    |
    | This option defines the type of the primary key for the User model.
    | Supported: "uuid", "id"
    | Default: "uuid"
    |
    */
    'type_id' => env('USER_TYPE_ID', 'uuid'),
];
