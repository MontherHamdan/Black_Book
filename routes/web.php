<?php

use App\Http\Controllers\AllOrdersController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\BookDecorationController;
use App\Http\Controllers\BookDesignCategoryController;
use App\Http\Controllers\BookDesignController;
use App\Http\Controllers\BookDesignSubCategoryController;
use App\Http\Controllers\BookTypeController;
use App\Http\Controllers\BookTypeSubMediaController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeliveryDispatchController;
use App\Http\Controllers\DesignerAccountingController;
use App\Http\Controllers\DiplomaController;
use App\Http\Controllers\DiscountCodeController;
use App\Http\Controllers\GovernorateController;
use App\Http\Controllers\NotebookBindingController;
use App\Http\Controllers\OrderWebController;
use App\Http\Controllers\PhoneNumberController;
use App\Http\Controllers\PlanWebController;
use App\Http\Controllers\PrintQueueController;
use App\Http\Controllers\SpecializedDepartmentWebController;
use App\Http\Controllers\SvgController;
use App\Http\Controllers\SvgNameController;
use App\Http\Controllers\UniversityController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VideoWebController;
use Illuminate\Support\Facades\Route;

/* |-------------------------------------------------------------------------- | Web Routes |-------------------------------------------------------------------------- */

// Login routes
Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('auth.login');
Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('auth.store');
// Logout route
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('auth.logout');

// Routes متاحة لأي يوزر مسجّل (Admin أو Designer)
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::put('/dashboard/orders/{id}/dismiss-notes', [DashboardController::class, 'dismissNotes'])->name('dashboard.dismissNotes');

    // Orders (Admin + Designers)
    Route::get('/orders', [OrderWebController::class, 'index'])->name('orders.index');
    Route::get('/orders/fetch', [OrderWebController::class, 'fetchOrders'])->name('orders.fetch');
    Route::post('/orders/update-status', [OrderWebController::class, 'updateStatus'])->name('orders.updateStatus');
    Route::post('/orders/bulk-update-status', [OrderWebController::class, 'bulkUpdateStatus'])->name('orders.bulkUpdateStatus');
    Route::post('orders/add-note', [OrderWebController::class, 'addNote'])->name('orders.addNote');
    Route::get('/orders/{order}/notes', [OrderWebController::class, 'getNotes'])->name('orders.getNotes');
    Route::put('/orders/{order}/update-notebook-followup', [OrderWebController::class, 'updateNotebookFollowup'])->name('orders.updateNotebookFollowup');
    Route::get('/orders/{id}', [OrderWebController::class, 'show'])->name('orders.show');
    Route::delete('/orders/{order}/delete-image', [OrderWebController::class, 'deleteImage'])->name('orders.deleteImage');
    Route::get('/orders/{order}/back-images/download', [OrderWebController::class, 'downloadAllBackImages'])->name('orders.backImages.download');
    Route::get('/orders-export/excel', [OrderWebController::class, 'exportExcel'])->name('orders.exportExcel');
    Route::get('/admin/orders/{order}/additional-images/download', [OrderWebController::class, 'downloadAllAdditionalImages'])
        ->name('orders.additionalImages.download');
    Route::post('/orders/update-designer', [OrderWebController::class, 'updateDesigner'])
        ->name('orders.updateDesigner');
    Route::put('/admin/orders/{order}/binding', [OrderWebController::class, 'updateBinding'])
        ->name('orders.updateBinding');
    Route::put('/admin/orders/{order}/delivery-followup', [
        OrderWebController::class,
        'updateDeliveryFollowup',
    ])->name('orders.updateDeliveryFollowup');

    Route::put('/orders/{order}/design-followup', [OrderWebController::class, 'updateDesignFollowup'])
        ->name('orders.updateDesignFollowup');

    // New editable order routes
    Route::put('/orders/{order}/core-data', [OrderWebController::class, 'updateOrderCoreData'])->name('orders.updateCoreData');
    Route::put('/orders/{order}/graduate-info', [OrderWebController::class, 'updateGraduateInfo'])->name('orders.updateGraduateInfo');
    Route::put('/orders/{order}/internal-book', [OrderWebController::class, 'updateInternalBook'])->name('orders.updateInternalBook');
    Route::put('/orders/{order}/binding-tab', [OrderWebController::class, 'updateBindingTab'])->name('orders.updateBindingTab');
    Route::put('/orders/{order}/design-image', [OrderWebController::class, 'updateDesignImage'])->name('orders.updateDesignImage');
    Route::put('/orders/{order}/delivery-info', [OrderWebController::class, 'updateDeliveryInfo'])->name('orders.updateDeliveryInfo');

    Route::put('/orders/{order}/dismiss-notes', [OrderWebController::class, 'dismissNotes'])->name('orders.dismissNotes');

    // Print Queues, All Orders, Delivery Dispatch
    Route::get('/print-queues', [PrintQueueController::class, 'index'])->name('print-queues.index');
    Route::get('/all-orders', [AllOrdersController::class, 'index'])->name('all-orders.index');
    Route::get('/delivery-dispatch', [DeliveryDispatchController::class, 'index'])->name('delivery-dispatch.index');

    // Notebook Binding (Printer workspace)
    Route::get('/notebook-binding', [NotebookBindingController::class, 'index'])
        ->name('notebook-binding.index');
    Route::post('/notebook-binding/mark-downloaded', [NotebookBindingController::class, 'markFileDownloaded'])
        ->name('notebook-binding.mark-downloaded');
    Route::post('/notebook-binding/bulk-mark-downloaded', [NotebookBindingController::class, 'bulkMarkDownloaded'])
        ->name('notebook-binding.bulk-mark-downloaded');

});

// Admin-only routes (إعدادات النظام)
Route::middleware(['auth', 'admin'])->group(function () {

    Route::resource('users', UserController::class);

    // Order deletion (Admin strictly)
    Route::delete('/orders/{id}', [OrderWebController::class, 'destroy'])->name('orders.destroy');
    Route::post('/orders/bulk-delete', [OrderWebController::class, 'bulkDelete'])->name('orders.bulkDelete');
    Route::post('/orders/print-awbs', [OrderWebController::class, 'printAWBs'])->name('orders.printAWBs');
    // Penalty System (Admin / Supervisor strictly via Controller gates, placed in admin for extra measure or explicitly handled)
    Route::post('/admin/settings/update-penalty-threshold', [DashboardController::class, 'updatePenaltyThreshold'])->name('admin.settings.update-penalty-threshold');
    Route::post('/admin/designers/{user}/penalty', [DashboardController::class, 'applyDesignerPenalty'])->name('admin.designers.penalty');

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
    Route::post('/governorates/{governorate}/toggle-active', [GovernorateController::class, 'toggleActive'])->name('governorates.toggleActive');
    // Discount Code
    Route::resource('discount-codes', DiscountCodeController::class);

    // Svg
    Route::resource('svgs', SvgController::class);

    // Svg Names
    Route::resource('svg-names', SvgNameController::class)->only([
        'index',
        'create',
        'store',
        'edit',
        'update',
        'destroy',
    ]);

    // Svg Categories
    Route::get('/svg-categories', [SvgController::class, 'categoryIndex'])->name('svg-categories.index');
    Route::get('/svg-categories/create', [SvgController::class, 'createCategory'])->name('svg-categories.create');
    Route::post('/svg-categories', [SvgController::class, 'storeCategory'])->name('svg-categories.store');

    Route::get('/svg-categories/{svgCategory}/edit', [SvgController::class, 'editCategory'])->name('svg-categories.edit');
    Route::put('/svg-categories/{svgCategory}', [SvgController::class, 'updateCategory'])->name('svg-categories.update');

    Route::delete('/svg-categories/{svgCategory}', [SvgController::class, 'destroyCategory'])->name('svg-categories.destroy');

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

    // Plans
    Route::resource('plans', PlanWebController::class);

    // Specialized Departments
    Route::resource('specialized-departments', SpecializedDepartmentWebController::class);

    // Videos
    Route::resource('videos', VideoWebController::class);

    // Designer Accounting
    Route::get('/designer-accounting', [DesignerAccountingController::class, 'index'])->name('designer-accounting.index');
    Route::get('/designer-accounting/{user}', [DesignerAccountingController::class, 'show'])->name('designer-accounting.show');
    Route::post('/designer-accounting/{user}/settle', [DesignerAccountingController::class, 'settle'])
        ->name('designer-accounting.settle');

    Route::post('/designer-accounting/{user}/custom-settle', [DesignerAccountingController::class, 'customSettle'])
        ->name('designer-accounting.customSettle');

    // Countries
    Route::resource('countries', CountryController::class);
    Route::post('/locations/sync', function () {
        try {
            \Illuminate\Support\Facades\Artisan::call('logestechs:sync-api');

            return back()->with('success', 'تم مزامنة المناطق مع شركة التوصيل بنجاح!');
        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ أثناء المزامنة: '.$e->getMessage());
        }
    })->name('locations.sync');
});
