
<?php

return [
    'jwt' => [
        'secret' => env('JWT_SECRET'),
    ],

    'initial_admin' => [
        'username' => env('INITIAL_ADMIN_USERNAME'),
        'password' => env('INITIAL_ADMIN_PASSWORD'),
    ],
];
