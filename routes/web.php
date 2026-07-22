<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ServiceRequestController;
use App\Http\Controllers\MasterController;
use App\Http\Controllers\UserProvisioningController;
use App\Http\Controllers\Userdirectorycontroller;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\WorkerPipelineController;
use App\Http\Controllers\WorkerpunchController;
use App\Http\Controllers\InquiryController;
use App\Http\Controllers\NotificationController;
use App\Services\WhatsAppService;
use App\Http\Controllers\WhatsappLogController;
use App\Http\Controllers\CompletedService;
use App\Http\Controllers\AssignedServiceRequestController;

/*
|--------------------------------------------------------------------------
| SR Portal Routes
|--------------------------------------------------------------------------
*/
// usercontroller
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
// Route::get('/', function () {
//     return redirect()->route('dashboard');
// });

/*
|--------------------------------------------------------------------------
| Protected routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    /* ---- Dashboard ---- */
    // Route::get('/dashboard', function () {
    //     return view('dashboard');
    // })->name('dashboard');
     Route::get('/analytics', function () {
        return view('analytics_dashboard');
    })->name('analytics');


    /* ---- User Provisioning ---- */
    Route::get('/user-provisioning', [UserProvisioningController::class, 'index'])->name('user_provisioning');
    Route::post('/user-provisioning', [UserProvisioningController::class, 'store'])->name('user_provisioning.store');
    // Route::put('/user-provisioning/{user}', [UserProvisioningController::class, 'update'])->name('user_provisioning.update');
    Route::post('/user-provisioning/{user}', [UserProvisioningController::class, 'update'])
    ->name('user_provisioning.update');
    //user-directory
    Route::get('/user-directory', [Userdirectorycontroller::class, 'index'])->name('user_directory');
    Route::post('/user-directory/{user}/toggle-status', [Userdirectorycontroller::class, 'toggleStatus'])->name('user_directory.toggle');
    Route::post('/user-directory/{user}/reset-password', [Userdirectorycontroller::class, 'resetPassword'])->name('user_directory.reset');

    Route::get('/user-directory/{user}',        [UserdirectoryController::class, 'show'])->name('user_directory.show');

    /* ---- Inquiry Approval ---- */
    Route::get('/inquiry-approval', [ServiceRequestController::class, 'approvalIndex'])->name('inquiry-approval.index');
    Route::post('/service-requests/{serviceRequest}/approve', [ServiceRequestController::class, 'approve'])->name('service-requests.approve');
    Route::post('/service-requests/{serviceRequest}/forward', [ServiceRequestController::class, 'forward'])->name('service-requests.forward');
    Route::post('/service-requests/{serviceRequest}/reject', [ServiceRequestController::class, 'reject'])->name('service-requests.reject');


    /* ---- Service Request ---- */
    Route::get('/sr-registration', [ServiceRequestController::class, 'create'])->name('sr_registration');
    Route::get('/service-requests/lookup/{code}', [ServiceRequestController::class, 'lookup'])->name('service-requests.lookup');
    Route::post('/service-requests', [ServiceRequestController::class, 'store'])->name('service-requests.store');
    Route::get('/', [ServiceRequestController::class, 'sr_explorer'])->name('sr_explorer');
    Route::get('/ticket-summary', [ServiceRequestController::class, 'ticketSummary'])->name('kanban_view');


    /* ---- Dispatch Engine / Kanban ---- */
    Route::get('/dispatch-engine', [ServiceRequestController::class, 'dispatch_engine'])->name('dispatch_engine');
    Route::post('/service-requests/{serviceRequest}/dispatch', [ServiceRequestController::class, 'dispatch'])->name('service-requests.dispatch');


    Route::post('/service-requests/contacts', [ServiceRequestController::class, 'storeContact']);
    
    /* ---- Clients / Projects ---- */
    Route::get('/clients/create', [ClientController::class, 'create'])->name('clients.create');
    Route::post('/clients', [ClientController::class, 'store'])->name('clients.store');
    Route::get('/clients/{client}/edit', [ClientController::class, 'edit'])->name('clients.edit');
    Route::put('/clients/{client}', [ClientController::class, 'update'])->name('clients.update');
    Route::get('/clients/lookup-by-name', [ClientController::class, 'lookupByName'])->name('clients.lookupByName');
    Route::get('/clients/directory', [ClientController::class, 'directory'])->name('clients.directory');
    Route::get('/clients/{id}', [ClientController::class, 'show'])->name('clients.show');
    Route::post('/clients-directory/{client}/toggle-status', [ClientController::class, 'toggleStatus'])->name('clients.toggle');
   
    // Job Tracking
    Route::get('/job-tracking/{id}', [ClientController::class, 'job_tracking'])->name('clients.job_tracking');
    Route::get('/job-tracking/{id}/data', [ClientController::class, 'job_tracking_data'])->name('clients.job_tracking.data');


    Route::get('project_site_directory',   [ProjectController::class, 'index'])->name('project_site_directory');
    Route::post('projects',                [ProjectController::class, 'store'])->name('projects.store');
    Route::put('projects/{project}',       [ProjectController::class, 'update'])->name('projects.update');
    Route::delete('projects/{project}',    [ProjectController::class, 'destroy'])->name('projects.destroy');
    Route::get('projects/{project}',       [ProjectController::class, 'show'])->name('projects.show');
    Route::post('projects/{project}/inquiries', [InquiryController::class, 'store'])->name('inquiries.store');

    // Clients Feedback
    Route::get('/client_feedback/{id}', [ClientController::class, 'showFeedback'])->name('clients.feedback.show');
    Route::post('/client_feedback/{id}', [ClientController::class, 'storeFeedback'])->name('clients.feedback.store');

    // Whatapp notifcation

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::get('/notifications/all', [NotificationController::class, 'all'])->name('notifications.all'); // full page

    
    Route::controller(ServiceRequestController::class)->group(function () {
    Route::get('/qc-review', 'qcReview')->name('qc_review');
    Route::post('/qc-review/{serviceRequest}/pass', 'qcPass')->name('qc.pass');
    Route::post('/qc-review/{serviceRequest}/fail', 'qcFail')->name('qc.fail');

    Route::get('/quotation_desk', [ServiceRequestController::class, 'quotationDesk'])->name('quotation_desk');
    Route::post('/quotation_desk/{serviceRequest}/quote',   [ServiceRequestController::class, 'quoteSubmit'])->name('quote.submit');
    Route::post('/quotation_desk/{serviceRequest}/approve', [ServiceRequestController::class, 'quoteApprove'])->name('quote.approve');

    Route::get('/invoice_panel', [ServiceRequestController::class, 'invoicePanel'])->name('invoice_panel');
    Route::post('/invoice_panel/{serviceRequest}/submit', [ServiceRequestController::class, 'invoiceSubmit'])->name('invoice.submit');
    Route::post('/invoice_panel/{serviceRequest}/hop-approve', [ServiceRequestController::class, 'hopApprove'])->name('invoice.hop');

    Route::get('/expense_ledger', [ServiceRequestController::class, 'expenseLedger'])->name('expense_ledger');

    Route::patch('/service-requests/{serviceRequest}/status', [ServiceRequestController::class, 'updateStatus'])
    ->name('service-requests.updateStatus');

    //whatsapp notification log
    Route::get('/wa_notification_log', [WhatsappLogController::class, 'index'])->name('wa_notification_log');
    Route::get('/wa_notification_log/export', [WhatsappLogController::class, 'export'])->name('wa_notification_log.export');
    Route::post('/wa_notification_log/retry-all', [WhatsappLogController::class, 'retryAll'])->name('wa_notification_log.retryAll');
    Route::get('/wa_notification_log/{log}', [WhatsappLogController::class, 'show'])->name('wa_notification_log.show');
    Route::post('/wa_notification_log/{log}/retry', [WhatsappLogController::class, 'retry'])->name('wa_notification_log.retry');

    Route::get('/completed-sr',        [CompletedService::class, 'index'])->name('completed');
    Route::get('/completed-sr/{id}',   [CompletedService::class, 'show'])->name('completed.show');

    Route::get('/assigned-sr',      [AssignedServiceRequestController::class, 'index'])->name('assigned');
    Route::get('/assigned-sr/{id}', [AssignedServiceRequestController::class, 'show'])->name('assigned.show');
    
    });


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


         // Warranty Categories
        Route::post('/warranty-category/store', [MasterController::class, 'storeWarrantyCategory'])->name('warranty-category.store');
        Route::put('/warranty-category/update/{id}', [MasterController::class, 'updateWarrantyCategory'])->name('warranty-category.update');
        Route::delete('/warranty-category/delete/{id}', [MasterController::class, 'deleteWarrantyCategory'])->name('warranty-category.delete');
        Route::post('/warranty-category/status/{id}', [MasterController::class, 'changeWarrantyCategoryStatus'])->name('warranty-category.status');


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

 
Route::prefix('worker')->name('worker.')->group(function () {
    Route::get('/pipeline', [WorkerPipelineController::class, 'index'])->name('pipeline');

    Route::post('/job/accept',     [WorkerPipelineController::class, 'accept'])->name('job.accept');
    Route::post('/job/reschedule', [WorkerPipelineController::class, 'reschedule'])->name('job.reschedule');
    Route::post('/job/hold',       [WorkerPipelineController::class, 'hold'])->name('job.hold');
    Route::post('/punch/in',       [WorkerpunchController::class, 'punchIn'])->name('punch.in');
    Route::post('/punch/out',      [WorkerpunchController::class, 'punchOut'])->name('punch.out');
    Route::post('/punch/upload',   [WorkerpunchController::class, 'upload'])->name('punch.upload');
    Route::post('/punch/expense',  [WorkerpunchController::class, 'expense'])->name('punch.expense');
    Route::post('/punch/signature',[WorkerpunchController::class, 'signature'])->name('punch.signature');  // ← add this

    Route::get('/history', [WorkerPipelineController::class, 'history'])->name('history');
    Route::get('/profile', [WorkerPipelineController::class, 'profile'])->name('profile');

    Route::post('/job/resume', [WorkerPipelineController::class, 'resume'])->name('job.resume');
});
