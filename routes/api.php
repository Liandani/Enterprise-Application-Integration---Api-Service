<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LoanController;

// =====================
// TEST ROUTE
// =====================
Route::get('/ping', function () {
    return 'OK';
});

// =====================
// USERS
// =====================
Route::get('/users', [UserController::class, 'index']);
Route::get('/users/{id}', [UserController::class, 'show']);
Route::get('/users/{id}/loans', [UserController::class, 'getUserWithLoans']);

// =====================
// BOOKS (DEBUG / CHECK DATA)
// =====================
Route::get('/books', function () {
    return \App\Models\Book::all();
});

// =====================
// LOANS
// =====================
Route::get('/loans', [LoanController::class, 'index']);
Route::post('/loans', [LoanController::class, 'store']);
