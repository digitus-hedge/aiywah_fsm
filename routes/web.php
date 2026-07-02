<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ServiceRequestController;
use App\Http\Controllers\MasterController;
use App\Http\Controllers\UserProvisioningController;
use App\Http\Controllers\UserDirectoryController;
/*
|--------------------------------------------------------------------------
| SR Portal Routes
|--------------------------------------------------------------------------
*/

/* ---- Auth ---- */
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.attempt');
});

Route::post('/logout', function (Illuminate\Http\Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/login');
})->name('logout')->middleware('auth');

/* ---- Root: redirect to dashboard (or login) ---- */
Route::get('/', function () {
    return redirect()->route('dashboard');
});

/*
|--------------------------------------------------------------------------
| Protected routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    /* ---- Dashboard ---- */
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    /* ---- User Provisioning ---- */
    Route::get('/user-provisioning', [UserProvisioningController::class, 'index'])->name('user_provisioning');
    Route::post('/user-provisioning', [UserProvisioningController::class, 'store'])->name('user_provisioning.store');
    Route::put('/user-provisioning/{user}', [UserProvisioningController::class, 'update'])->name('user_provisioning.update');

    //user-directory
    Route::get('/user-directory', [UserDirectoryController::class, 'index'])
    ->name('user_directory');

    Route::post('/user-directory/{user}/toggle-status', [UserDirectoryController::class, 'toggleStatus'])
        ->name('user_directory.toggle');

    Route::post('/user-directory/{user}/reset-password', [UserDirectoryController::class, 'resetPassword'])
        ->name('user_directory.reset');
 
    /* ---- Inquiry Approval ---- */
    Route::get('/inquiry-approval', [ServiceRequestController::class, 'approvalIndex'])->name('inquiry-approval.index');
    Route::post('/service-requests/{serviceRequest}/approve', [ServiceRequestController::class, 'approve'])->name('service-requests.approve');
    Route::post('/service-requests/{serviceRequest}/forward', [ServiceRequestController::class, 'forward'])->name('service-requests.forward');
    Route::post('/service-requests/{serviceRequest}/reject', [ServiceRequestController::class, 'reject'])->name('service-requests.reject');

    /* ---- Service Request ---- */
    Route::get('/sr-registration', [ServiceRequestController::class, 'create'])->name('sr_registration');
    Route::get('/service-requests/lookup/{code}', [ServiceRequestController::class, 'lookup'])->name('service-requests.lookup');
    Route::post('/service-requests', [ServiceRequestController::class, 'store'])->name('service-requests.store');

    /* ---- Dispatch Engine / Kanban ---- */
    Route::get('/dispatch-engine', function () {
        return view('dispatch_engine');
    })->name('dispatch_engine');

    Route::get('/ticket-summary', function () {
        return view('kanban_view');
    })->name('kanban_view');

    /* ---- Clients / Projects ---- */
    Route::get('/clients/create', [ClientController::class, 'create'])->name('clients.create');
    Route::post('/clients', [ClientController::class, 'store'])->name('clients.store');
    Route::get('/clients/{client}/edit', [ClientController::class, 'edit'])->name('clients.edit');
    Route::put('/clients/{client}', [ClientController::class, 'update'])->name('clients.update');
    Route::get('/clients/lookup-by-name', [ClientController::class, 'lookupByName'])->name('clients.lookupByName');
    
    Route::get('/clients/directory', [ClientController::class, 'directory'])
    ->name('clients.directory');
    /*
    |--------------------------------------------------------------------------
    | Master Data Management
    |--------------------------------------------------------------------------
    */
    Route::prefix('masters')->name('masters.')->group(function () {

        Route::get('/', [MasterController::class, 'index'])->name('index');

        // Service Categories
        Route::post('/service-category/store', [MasterController::class, 'storeServiceCategory'])->name('service-category.store');
        Route::put('/service-category/update/{id}', [MasterController::class, 'updateServiceCategory'])->name('service-category.update');
        Route::delete('/service-category/delete/{id}', [MasterController::class, 'deleteServiceCategory'])->name('service-category.delete');
        Route::post('/service-category/status/{id}', [MasterController::class, 'changeServiceCategoryStatus'])->name('service-category.status');

        // Service Domains
        Route::get('/service-domains/{category}', [MasterController::class, 'getServiceDomains'])->name('service-domains');
        Route::post('/service-domain/store', [MasterController::class, 'storeServiceDomain'])->name('service-domain.store');
        Route::put('/service-domain/update/{id}', [MasterController::class, 'updateServiceDomain'])->name('service-domain.update');
        Route::delete('/service-domain/delete/{id}', [MasterController::class, 'destroyServiceDomain'])->name('service-domain.delete');
        Route::post('/service-domain/status/{id}', [MasterController::class, 'changeServiceDomainStatus'])->name('service-domain.status');

        // Expense Categories
        Route::post('/expense-category/store', [MasterController::class, 'storeExpenseCategory'])->name('expense-category.store');
        Route::put('/expense-category/update/{id}', [MasterController::class, 'updateExpenseCategory'])->name('expense-category.update');
        Route::delete('/expense-category/delete/{id}', [MasterController::class, 'deleteExpenseCategory'])->name('expense-category.delete');
        Route::post('/expense-category/status/{id}', [MasterController::class, 'changeExpenseCategoryStatus'])->name('expense-category.status');

        // Priorities
        Route::post('/priority/store', [MasterController::class, 'storePriority'])->name('priority.store');
        Route::put('/priority/update/{id}', [MasterController::class, 'updatePriority'])->name('priority.update');
        Route::delete('/priority/delete/{id}', [MasterController::class, 'deletePriority'])->name('priority.delete');
        Route::post('/priority/status/{id}', [MasterController::class, 'changePriorityStatus'])->name('priority.status');

        // SLA Matrix
        Route::post('/sla-matrix/store', [MasterController::class, 'storeSlaMatrix'])->name('sla-matrix.store');
        Route::put('/sla-matrix/update/{id}', [MasterController::class, 'updateSlaMatrix'])->name('sla-matrix.update');
        Route::delete('/sla-matrix/delete/{id}', [MasterController::class, 'deleteSlaMatrix'])->name('sla-matrix.delete');
        Route::post('/sla-matrix/save-all', [MasterController::class, 'saveAllSla'])->name('sla-matrix.save-all');

        // WhatsApp Templates
        Route::post('/whatsapp-template/store', [MasterController::class, 'storeWhatsappTemplate'])->name('whatsapp-template.store');
        Route::put('/whatsapp-template/update/{id}', [MasterController::class, 'updateWhatsappTemplate'])->name('whatsapp-template.update');
        Route::delete('/whatsapp-template/delete/{id}', [MasterController::class, 'deleteWhatsappTemplate'])->name('whatsapp-template.delete');
        Route::post('/whatsapp-template/status/{id}', [MasterController::class, 'changeWhatsappTemplateStatus'])->name('whatsapp-template.status');

        // Activity Logs
        Route::get('/activity-logs', [MasterController::class, 'activityLogs'])->name('activity-logs');
        Route::get('/activity-log/{id}', [MasterController::class, 'viewActivityLog'])->name('activity-log.view');

        // AJAX read APIs
        Route::get('/ajax/categories', [MasterController::class, 'ajaxCategories'])->name('ajax.categories');
        Route::get('/ajax/domains/{category}', [MasterController::class, 'ajaxDomains'])->name('ajax.domains');
        Route::get('/ajax/expenses', [MasterController::class, 'ajaxExpenses'])->name('ajax.expenses');
        Route::get('/ajax/priorities', [MasterController::class, 'ajaxPriorities'])->name('ajax.priorities');
        Route::get('/ajax/sla', [MasterController::class, 'ajaxSla'])->name('ajax.sla');
        Route::get('/ajax/templates', [MasterController::class, 'ajaxTemplates'])->name('ajax.templates');
    });
});