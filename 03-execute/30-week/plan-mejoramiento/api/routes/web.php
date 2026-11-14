
<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'message' => 'Bienvenido a Toy Library',
        'api' => '/api/v1',
    ]);
});
