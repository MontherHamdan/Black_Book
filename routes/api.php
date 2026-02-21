<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SvgController;
use App\Http\Controllers\Api\MajorController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\DiplomaController;
use App\Http\Controllers\Api\BookTypeController;
use App\Http\Controllers\Api\UserImageController;
use App\Http\Controllers\Api\BookDesignController;
use App\Http\Controllers\Api\UniversityController;
use App\Http\Controllers\Api\GovernorateController;
use App\Http\Controllers\Api\PhoneNumbersConroller;
use App\Http\Controllers\Api\DiplomaMajorController;
use App\Http\Controllers\Api\DiscountCodeController;
use App\Http\Controllers\Api\BookDecorationController;
use App\Http\Controllers\Api\BookDesginCategoryController;
use App\Http\Controllers\Api\BookDesginSubCategoryController;
use App\Http\Controllers\Api\VideoController;
use App\Http\Controllers\Api\SpecializedDepartmentController;
use App\Http\Controllers\Api\PlanController;
/* |-------------------------------------------------------------------------- | API Routes |-------------------------------------------------------------------------- | | Here is where you can register API routes for your application. These | routes are loaded by the RouteServiceProvider and all of them will | be assigned to the "api" middleware group. Make something great! | */

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('v1')->group(function () {
    // *******************book type **********************************
    Route::resource('/book_type', BookTypeController::class);

    // *******************book_design **********************************
    Route::resource('/book_design', BookDesignController::class)->only(['index', 'store']);
    Route::get('/book_design/all', [BookDesignController::class , 'all'])->name('book_design.all');

    // *******************book_design_categoriese **********************************
    Route::resource('/book_design_categories', BookDesginCategoryController::class);

    // *******************book_design_subCategories **********************************
    Route::resource('/book_design_subCategories', BookDesginSubCategoryController::class);

    // *******************phone_numbers **********************************
    Route::resource('/phone_numbers', PhoneNumbersConroller::class);

    // *******************svgs ********************************************
    Route::resource('/svgs', SvgController::class);

    // *******************Universities and majors **********************************
    Route::resource('universities', UniversityController::class);
    Route::resource('/universities/{university_id}/majors', MajorController::class);

    // *******************Book Decorations **********************************
    Route::resource('/book_decorations', BookDecorationController::class)->only(['index']);

    // *******************governorates and addresses **********************************
    Route::get('/governorates', [GovernorateController::class , 'index']);
    Route::get('/governorates/{id}/addresses', [AddressController::class , 'getAddressesByGovernorate']);

    // ******************* Discount Codes **********************************
    Route::get('/discount_codes/check', [DiscountCodeController::class, 'check']);

    // *******************User Upload Image ******************************************
    Route::post('/user_upload_image', [UserImageController::class , 'store']);

    // create orders
    Route::resource('orders', OrderController::class)->only(['store']);

    // Diplomas Routes
    Route::resource('diplomas', DiplomaController::class);

    // Diploma Majors Routes
    Route::get('diplomas/{diploma_id}/majors',
    [DiplomaMajorController::class , 'index']
    );

    Route::resource('orders', OrderController::class)->only(['store']);

    // Videos 
    Route::apiResource('videos', VideoController::class)->only(['index', 'show']);

    // Specialized Departments
    Route::apiResource('specialized-departments', SpecializedDepartmentController::class)->only(['index', 'show']);
    // Plans
    Route::apiResource('plans', PlanController::class)->only(['index', 'show']);
});
