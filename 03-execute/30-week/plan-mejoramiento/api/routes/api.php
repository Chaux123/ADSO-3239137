
<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PromoterController;
use App\Http\Controllers\ToyController;
use App\Http\Controllers\FamilyController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\ReportController;

// Health check
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
    ]);
});

// Authentication
Route::post('/auth/login', [
    AuthController::class,
    'login',
]);

// Public catalog
Route::get('/categories', [
    ToyController::class,
    'categories',
]);

Route::get('/toys', [
    ToyController::class,
    'index',
]);

Route::get('/toys/{id}', [
    ToyController::class,
    'show',
]);

Route::get('/toys/{id}/cover', [
    ToyController::class,
    'cover',
]);

// Protected routes
Route::middleware('jwt')->group(function () {

    // Promoters
    Route::post('/users', [
        PromoterController::class,
        'store',
    ]);

    // Toy management
    Route::post('/toys', [
        ToyController::class,
        'store',
    ]);

    Route::put('/toys/{id}', [
        ToyController::class,
        'update',
    ]);

    Route::delete('/toys/{id}', [
        ToyController::class,
        'destroy',
    ]);

    Route::put('/toys/{id}/cover', [
        ToyController::class,
        'putCover',
    ]);

    Route::delete('/toys/{id}/cover', [
        ToyController::class,
        'deleteCover',
    ]);

    // Families
    Route::get('/families', [
        FamilyController::class,
        'index',
    ]);

    Route::get('/families/{id}', [
        FamilyController::class,
        'show',
    ]);

    Route::post('/families', [
        FamilyController::class,
        'store',
    ]);

    // Loans
    Route::get('/loans', [
        LoanController::class,
        'index',
    ]);

    Route::get('/loans/{id}', [
        LoanController::class,
        'show',
    ]);

    Route::post('/loans', [
        LoanController::class,
        'store',
    ]);

    // Returns
    Route::post(
        '/loans/{loanId}/items/{itemId}/return',
        [
            LoanController::class,
            'returnItem',
        ]
    );

    // Reports
    Route::get('/reports/most-loaned', [
        ReportController::class,
        'mostLoaned',
    ]);
});
