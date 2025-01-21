<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SvgController;
use App\Http\Controllers\DiplomaController;
use App\Http\Controllers\BookTypeController;
use App\Http\Controllers\OrderWebController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BookDesignController;
use App\Http\Controllers\UniversityController;
use App\Http\Controllers\GovernorateController;
use App\Http\Controllers\PhoneNumberController;
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

    // Svg 
    Route::resource('svgs', SvgController::class);

    // univeristies majors  
    Route::resource('universities', UniversityController::class);
    Route::get('/universities/{university}/majors', [UniversityController::class, 'fetchMajors']);
    Route::post('/universities/{university}/add-major', [UniversityController::class, 'storeMajor']);
    Route::delete('/universities/{university}/delete-major/{major}', [UniversityController::class, 'deleteMajor'])->name('majors.delete');

    // phone numbers 
    Route::resource('phone-numbers', PhoneNumberController::class)->only(['index', 'destroy']);

    // diploma and majors 
    Route::resource('diplomas', DiplomaController::class);
    Route::post('diplomas/{diplomaId}/majors', [DiplomaController::class, 'storeMajor'])->name('diplomas.storeMajor');
    Route::delete('diplomas/{diplomaId}/majors/{majorId}', [DiplomaController::class, 'deleteMajor'])->name('diplomas.deleteMajor');
    Route::get('diplomas/{diplomaId}/majors', [DiplomaController::class, 'fetchMajors'])->name('diplomas.fetchMajors');

    // orders
    Route::get('/orders', [OrderWebController::class, 'index'])->name('orders.index');
    Route::get('/orders/fetch', [OrderWebController::class, 'fetchOrders'])->name('orders.fetch');
    Route::post('/orders/update-status', [OrderWebController::class, 'updateStatus'])->name('orders.updateStatus');
    Route::delete('/orders/{id}', [OrderWebController::class, 'destroy'])->name('orders.destroy');
    Route::post('orders/add-note', [OrderWebController::class, 'addNote'])->name('orders.addNote');
    Route::get('/orders/{order}/notes', [OrderWebController::class, 'getNotes'])->name('orders.getNotes');
    Route::get('/orders/{id}', [OrderWebController::class, 'show'])->name('orders.show');
    Route::get('/orders/{order}/back-images/download', [OrderWebController::class, 'downloadAllBackImages'])->name('orders.backImages.download');

});
