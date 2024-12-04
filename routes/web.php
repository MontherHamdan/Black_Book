<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookTypeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BookDesignController;
use App\Http\Controllers\GovernorateController;
use App\Http\Controllers\DiscountCodeController;
use App\Http\Controllers\BookDecorationController;
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

    // book type sub media
    Route::post('book-type-sub-media', [BookTypeSubMediaController::class, 'store'])->name('book-type-sub-media.store');
    Route::delete('book-type-sub-media/{subMedia}', [BookTypeSubMediaController::class, 'destroy'])->name('book-type-sub-media.destroy');

    // Book Decorations
    Route::resource('book-decorations', BookDecorationController::class);

    // Governorates and Addresses
    Route::resource('governorates', GovernorateController::class);
    Route::post('governorates/{governorate}/add-address', [GovernorateController::class, 'addAddress'])->name('governorates.addAddress');
    Route::delete('addresses/{address}', [GovernorateController::class, 'deleteAddress'])->name('addresses.delete');
    Route::get('/governorates/{id}/addresses', [GovernorateController::class, 'getAddresses'])->name('governorates.addresses');

    // Discount Code 
    Route::resource('discount-codes', DiscountCodeController::class);
});
