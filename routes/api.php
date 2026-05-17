<?php

use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\BookDecorationController;
use App\Http\Controllers\Api\BookDesginCategoryController;
use App\Http\Controllers\Api\BookDesginSubCategoryController;
use App\Http\Controllers\Api\BookDesignController;
use App\Http\Controllers\Api\BookTypeController;
use App\Http\Controllers\Api\CountryApiController;
use App\Http\Controllers\Api\DiplomaController;
use App\Http\Controllers\Api\DiplomaMajorController;
use App\Http\Controllers\Api\DiscountCodeController;
use App\Http\Controllers\Api\GovernorateController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\MajorController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PhoneNumbersConroller;
use App\Http\Controllers\Api\PlanController;
use App\Http\Controllers\Api\SpecializedDepartmentController;
use App\Http\Controllers\Api\SvgController;
use App\Http\Controllers\Api\UniversityController;
use App\Http\Controllers\Api\UserImageController;
use App\Http\Controllers\Api\VideoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/* |-------------------------------------------------------------------------- | API Routes |-------------------------------------------------------------------------- | | Here is where you can register API routes for your application. These | routes are loaded by the RouteServiceProvider and all of them will | be assigned to the "api" middleware group. Make something great! | */

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('v1')->name('api.')->group(function () {
    // *******************book type **********************************
    Route::resource('/book_type', BookTypeController::class);

    // *******************book_design **********************************
    Route::resource('/book_design', BookDesignController::class)->only(['index', 'store']);
    Route::get('/book_design/all', [BookDesignController::class, 'all'])->name('book_design.all');

    // *******************book_design_categoriese **********************************
    Route::resource('/book_design_categories', BookDesginCategoryController::class);

    // *******************book_design_subCategories **********************************
    Route::resource('/book_design_subCategories', BookDesginSubCategoryController::class);

    // *******************phone_numbers **********************************
    Route::resource('/phone_numbers', PhoneNumbersConroller::class);

    // *******************svgs ********************************************
    Route::resource('/svgs', SvgController::class);
    Route::get('svg-categories', [SvgController::class, 'getCategoriesWithSvgs']);
    // *******************Universities and majors **********************************
    Route::resource('universities', UniversityController::class);
    Route::resource('/universities/{university_id}/majors', MajorController::class);

    // *******************Book Decorations **********************************
    Route::resource('/book_decorations', BookDecorationController::class)->only(['index']);

    // *******************governorates and addresses **********************************
    Route::get('/governorates', [GovernorateController::class, 'index']);
    Route::get('/governorates/{id}/addresses', [AddressController::class, 'getAddressesByGovernorate']);

    // ******************* Discount Codes **********************************
    Route::get('/discount_codes/check', [DiscountCodeController::class, 'check']);

    // *******************User Upload Image ******************************************
    Route::post('/user_upload_image', [UserImageController::class, 'store'])->middleware('throttle:200,1');
    // Route::post('/user_upload_image', [UserImageController::class, 'store'])->middleware('throttle:20,1');

    // create orders — throttle raised temporarily for load testing, revert to throttle:10,1 before go-live
    Route::resource('orders', OrderController::class)->only(['store'])->middleware('throttle:200,1');
    // Route::resource('orders', OrderController::class)->only(['store'])->middleware('throttle:10,1');

    // Diplomas Routes
    Route::resource('diplomas', DiplomaController::class);

    // Diploma Majors Routes
    Route::get(
        'diplomas/{diploma_id}/majors',
        [DiplomaMajorController::class, 'index']
    );

    // Videos
    Route::apiResource('videos', VideoController::class)->only(['index', 'show']);

    // Specialized Departments
    Route::apiResource('specialized-departments', SpecializedDepartmentController::class)->only(['index', 'show']);
    // Plans
    Route::apiResource('plans', PlanController::class)->only(['index', 'show']);

    // Countries
    Route::get('countries', [CountryApiController::class, 'index']);

    Route::get('/locations/countries', [LocationController::class, 'getCountries']);
    Route::get('/locations/governorates', [LocationController::class, 'getGovernorates']);
    Route::get('/locations/cities/{governorate_id}', [LocationController::class, 'getCities']);
    Route::get('/locations/areas/{city_id}', [LocationController::class, 'getAreas']);
});
