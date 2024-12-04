<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SvgController;
use App\Http\Controllers\Api\MajorController;
use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\BookTypeController;
use App\Http\Controllers\Api\BookDesignController;
use App\Http\Controllers\Api\UniversityController;
use App\Http\Controllers\Api\GovernorateController;
use App\Http\Controllers\Api\PhoneNumbersConroller;
use App\Http\Controllers\Api\DiscountCodeController;
use App\Http\Controllers\Api\BookDecorationController;
use App\Http\Controllers\Api\BookDesginCategoryController;
use App\Http\Controllers\Api\BookDesginSubCategoryController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('v1')->group(function () {
    // *******************book type **********************************
    Route::resource('/book_type', BookTypeController::class);

    // *******************book_design **********************************
    Route::resource('/book_design', BookDesignController::class)->only(['index']);
    Route::get('/book_design/all', [BookDesignController::class, 'all'])->name('book_design.all');

    // *******************book_design_categoriese **********************************
    Route::resource('/book_design_categories', BookDesginCategoryController::class);

    // *******************book_design_subCategories **********************************
    Route::resource('/book_design_subCategories', BookDesginSubCategoryController::class);

    // *******************phone_numbers **********************************
    Route::resource('/phone_numbers', PhoneNumbersConroller::class);

    // *******************book type ********************************************
    Route::resource('/svgs', SvgController::class);

    Route::middleware('throttle:60,1')->group(function () {
        Route::resource('svgs', SvgController::class);
    });

    // *******************Universities and majors **********************************
    Route::resource('universities', UniversityController::class);
    Route::resource('/universities/{university_id}/majors', MajorController::class);

    // *******************Book Decorations **********************************
    Route::resource('/book_decorations', BookDecorationController::class)->only(['index']);

    // *******************governorates and addresses **********************************
    Route::get('/governorates', [GovernorateController::class, 'index']);
    Route::get('/governorates/{id}/addresses', [AddressController::class, 'getAddressesByGovernorate']);

    Route::resource('/discount_codes', DiscountCodeController::class)->only(['index']);
});
