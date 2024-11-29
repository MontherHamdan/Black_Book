<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookTypeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BookDesignController;
use App\Http\Controllers\BookTypeSubMediaController;
use App\Http\Controllers\BookDesignCategoryController;
use App\Http\Controllers\BookDesignSubCategoryController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


// Login routes
Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('auth.login');
Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('auth.store');

// Logout route
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('auth.logout');



// Admin routes with auth and admin middleware
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');

    // book design
    Route::resource('book-designs', BookDesignController::class);

    // Categories
    Route::resource('categories', BookDesignCategoryController::class);

    // Subcategories
    Route::resource('subcategories', BookDesignSubCategoryController::class);

    // book type
    Route::resource('book-types', BookTypeController::class);

    Route::post('book-types/{bookType}/submedia', [BookTypeController::class, 'storeSubMedia'])->name('book-types.submedia.store');
});
