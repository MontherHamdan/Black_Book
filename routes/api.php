<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SvgController;
use App\Http\Controllers\Api\BookTypeController;
use App\Http\Controllers\Api\BookDesignController;
use App\Http\Controllers\Api\UniversityController;
use App\Http\Controllers\Api\PhoneNumbersConroller;
use App\Http\Controllers\Api\BookDesginCategoryController;
use App\Http\Controllers\Api\BookDesginSubCategoryController;
use App\Http\Controllers\Api\MajorController;

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
    Route::resource('/book_type', BookTypeController::class);
    Route::resource('/book_design', BookDesignController::class);
    Route::resource('/book_design_categories', BookDesginCategoryController::class);
    Route::resource('/book_design_subCategories', BookDesginSubCategoryController::class);
    Route::resource('/phone_numbers', PhoneNumbersConroller::class);
    Route::resource('/svgs', SvgController::class);

    Route::middleware('throttle:60,1')->group(function () {
        Route::resource('svgs', SvgController::class);
    });

    Route::resource('universities', UniversityController::class);
    Route::resource('/universities/{university_id}/majors', MajorController::class);
});
