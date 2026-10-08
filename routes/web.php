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
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\Auth\WorkerLoginController;
use App\Http\Controllers\Auth\WorkerPasswordController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MlDashboardController;
use App\Http\Controllers\FrontDashboardController;
use App\Http\Controllers\SEDashboardController;
use App\Http\Controllers\ReworkServiceRequestController;
use App\Http\Controllers\AccountantDashboardController;
use App\Http\Controllers\SrTrackingController;
use App\Http\Controllers\SummaryViewController;
use App\Http\Controllers\EmailLogController;


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


/* ---- Public customer links (WhatsApp) ---- */


    Route::get('/client_feedback/{id}', [ClientController::class, 'showFeedback'])
        ->name('clients.feedback.show');

    // Clients Feedback
    Route::get('/feedback/{id}/preview', [ClientController::class, 'preview'])->name('feedback.preview');
    
    Route::get('/sr/{serviceRequest}/photos', [ServiceRequestController::class, 'publicPhotos'])
        ->name('sr.photos');


// ── customer portal - public ──
Route::get('/portal/client/{code}',        [ClientController::class, 'portalClient'])->name('portal.client');
Route::get('/portal/project/{code}',       [ClientController::class, 'portal'])->name('portal.project');

// POST stays unsigned so the form can submit normally
Route::post('/client_feedback/{id}', [ClientController::class, 'storeFeedback'])
    ->name('clients.feedback.store');
Route::get('/portal/doc/{sr}/{type}', [ClientController::class, 'portalDoc'])
    ->whereIn('type', ['quote', 'invoice'])->name('portal.doc');

/* ---- Root: redirect to dashboard (or login) ---- */
// Route::get('/', function () {
//     return redirect()->route('dashboard');
// });

//internal summary page links
Route::get('/summary/admin/{user}', [SummaryViewController::class, 'admin'])
    ->name('summary.admin.show')
    ->middleware('signed');

Route::get('/summary/hop/{user}', [SummaryViewController::class, 'hop'])
    ->name('summary.hop.show')
    ->middleware('signed');

Route::get('/summary/se/{user}', [SummaryViewController::class, 'se'])
    ->name('summary.se.show')
    ->middleware('signed');

Route::get('/summary/ml/{user}', [SummaryViewController::class, 'ml'])
    ->name('summary.ml.show')
    ->middleware('signed');

Route::get('/summary/ac/{user}', [SummaryViewController::class, 'ac'])
    ->name('summary.ac.show')
    ->middleware('signed');
/*
|--------------------------------------------------------------------------
| Protected routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

   /* ---- Dashboard ---- */
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/panel', [DashboardController::class, 'panel'])->name('dashboard.panel');
    
      /* ---- Front Desk Executive Dashboard ---- */
    Route::get('/front-desk/dashboard', [FrontDashboardController::class, 'index'])
        ->middleware('role:FD')
        ->name('frontdashboard');
    Route::get('/front-dashboard/panel', [FrontDashboardController::class, 'panel'])
        ->name('front_dashboard.panel');

    Route::get('/service-engineer/dashboard', [SEDashboardController::class, 'index'])
        ->middleware('se_or_hop_se')
        ->name('sedashboard');


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
    Route::delete('/user-directory/{user}', [Userdirectorycontroller::class, 'destroy'])
    ->name('user_directory.destroy');
    Route::post('/user-directory/{user}/reset-password', [Userdirectorycontroller::class, 'resetPassword'])->name('user_directory.reset');

    Route::get('/user-directory/{user}',[UserdirectoryController::class, 'show'])->name('user_directory.show');

    Route::get('/accountant/dashboard', [AccountantDashboardController::class, 'index'])
        ->name('accountant.dashboard');
        
    /* ---- Inquiry Approval ---- */
    Route::get('/inquiry-approval', [ServiceRequestController::class, 'approvalIndex'])->name('inquiry-approval.index');
    Route::post('/service-requests/{serviceRequest}/approve', [ServiceRequestController::class, 'approve'])->name('service-requests.approve');
    Route::post('/service-requests/{serviceRequest}/forward', [ServiceRequestController::class, 'forward'])->name('service-requests.forward');
    Route::post('/service-requests/{serviceRequest}/reject', [ServiceRequestController::class, 'reject'])->name('service-requests.reject');

    Route::post('/service-requests/{serviceRequest}/additional', [ServiceRequestController::class, 'additionalWork'])->name('service-requests.additional');

    Route::post('/inquiry-approval/{serviceRequest}/approve-oow', [ServiceRequestController::class, 'approveOow'])->name('inquiry-approval.approve-oow');
    /* ---- Service Request ---- */
    Route::get('/sr-registration', [ServiceRequestController::class, 'create'])->name('sr_registration');
    Route::get('/service-requests/lookup/{code}', [ServiceRequestController::class, 'lookup'])->name('service-requests.lookup');
    Route::post('/service-requests', [ServiceRequestController::class, 'store'])->name('service-requests.store');
    Route::get('/sr_explorer', [ServiceRequestController::class, 'sr_explorer'])->name('sr_explorer');
    Route::get('/ticket-summary', [ServiceRequestController::class, 'ticketSummary'])->name('kanban_view');

    Route::post('service-requests/{serviceRequest}/approve-oow', [ServiceRequestController::class, 'approveOow']);

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
    Route::delete('/clients/{client}', [ClientController::class, 'destroy'])
    ->name('clients.destroy');
    // Job Tracking
    Route::get('/job-tracking/{id}', [ClientController::class, 'job_tracking'])->name('clients.job_tracking');
    Route::get('/job-tracking/{id}/data', [ClientController::class, 'job_tracking_data'])->name('clients.job_tracking.data');
    //sr-tracking
    Route::get('/sr-tracking/{id}/popup', [SrTrackingController::class, 'popup'])->name('sr.tracking.popup');

    Route::get('project_site_directory',   [ProjectController::class, 'index'])->name('project_site_directory');
    Route::post('projects',                [ProjectController::class, 'store'])->name('projects.store');
    Route::put('projects/{project}',       [ProjectController::class, 'update'])->name('projects.update');
    Route::delete('projects/{project}',    [ProjectController::class, 'destroy'])->name('projects.destroy');
    Route::get('projects/{project}',       [ProjectController::class, 'show'])->name('projects.show');
    Route::post('projects/{project}/inquiries', [InquiryController::class, 'store'])->name('inquiries.store');




    Route::get('/mls-by-category/{category}', [ServiceRequestController::class, 'mlsByCategory'])->name('mls.by_category');
    Route::post('/qc/reallocate', [ServiceRequestController::class, 'reallocate'])->name('qc.reallocate');

    

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
        Route::post('/quotation_desk/{serviceRequest}/quote/preview', [ServiceRequestController::class, 'quotePreview'])->name('quote.preview');

        Route::post('/quotation_desk/{serviceRequest}/reject',
        [ServiceRequestController::class, 'quoteReject'])->name('quotation.reject');

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

        //email notification log 
        Route::get('/email_notification_log', [EmailLogController::class, 'index'])->name('email_notification_log');
        Route::get('/email_notification_log/export', [EmailLogController::class, 'export'])->name('email_notification_log.export');
        Route::post('/email_notification_log/retry-all', [EmailLogController::class, 'retryAll'])->name('email_notification_log.retryAll');
        Route::get('/email_notification_log/{log}', [EmailLogController::class, 'show'])->name('email_notification_log.show');
        Route::post('/email_notification_log/{log}/retry', [EmailLogController::class, 'retry'])->name('email_notification_log.retry');

        Route::get('/completed-sr',        [CompletedService::class, 'index'])->name('completed');
        Route::get('/completed-sr/{id}',   [CompletedService::class, 'show'])->name('completed.show');

        Route::get('/assigned-sr',      [AssignedServiceRequestController::class, 'index'])->name('assigned');
        Route::get('/assigned-sr/{id}', [AssignedServiceRequestController::class, 'show'])->name('assigned.show');

    
        Route::get('/rework-sr',      [ReworkServiceRequestController::class, 'index'])->name('rework_sr');
        Route::get('/rework-sr/{id}', [ReworkServiceRequestController::class, 'show'])->name('rework_sr.show');

        // Activity Logs
        Route::get('/activity-log', [ActivityLogController::class, 'index'])->name('activity-log');
        Route::get('/activity-log/{activityLog}', [ActivityLogController::class, 'show'])->name('activity-log.view');
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

        // AJAX read APIs
        Route::get('/ajax/categories', [MasterController::class, 'ajaxCategories'])->name('ajax.categories');
        Route::get('/ajax/domains/{category}', [MasterController::class, 'ajaxDomains'])->name('ajax.domains');
        Route::get('/ajax/expenses', [MasterController::class, 'ajaxExpenses'])->name('ajax.expenses');
        Route::get('/ajax/priorities', [MasterController::class, 'ajaxPriorities'])->name('ajax.priorities');
        Route::get('/ajax/sla', [MasterController::class, 'ajaxSla'])->name('ajax.sla');

        // Summary Alert
        Route::get('/summary-alert/users/{roleSlug}', [MasterController::class, 'summaryAlertUsers'])
            ->name('summary-alert.users');
        Route::get('/summary-alert/permissions/{user}', [MasterController::class, 'summaryAlertPermissions'])
            ->name('summary-alert.permissions');
        Route::post('/summary-alert/save', [MasterController::class, 'summaryAlertSave'])
            ->name('summary-alert.save');
            });

        // Summary Alert
        Route::get('/summary-alert/users/{roleSlug}', [MasterController::class, 'summaryAlertUsers'])
            ->name('summary-alert.users');
        Route::get('/summary-alert/permissions/{user}', [MasterController::class, 'summaryAlertPermissions'])
            ->name('summary-alert.permissions');
        Route::post('/summary-alert/save', [MasterController::class, 'summaryAlertSave'])
            ->name('summary-alert.save');
});


Route::prefix('worker')->name('worker.')->group(function () {

    // Guest - login + OTP reset flow
    Route::get('/login',  [WorkerLoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [WorkerLoginController::class, 'login'])->name('login.attempt');

    // WhatsApp "Open My Jobs" button lands here - routes to the pipeline if
    // already logged in, otherwise to login with a redirect back to it.
    Route::get('/go', function () {
        if (Auth::guard('worker')->check()) {
            return redirect()->route('worker.pipeline', ['filter' => 'Pending']);
        }

        return redirect()->route('worker.login', [
            'redirect' => route('worker.pipeline', ['filter' => 'Pending']),
        ]);
    })->name('go');

    Route::get('/forgot-password',  [WorkerPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [WorkerPasswordController::class, 'sendOtp'])
        ->middleware('throttle:5,10')->name('password.email');

    Route::get('/verify-otp',  [WorkerPasswordController::class, 'showOtpForm'])->name('otp.form');
    Route::post('/verify-otp', [WorkerPasswordController::class, 'verifyOtp'])
        ->middleware('throttle:10,10')->name('otp.verify');
    Route::post('/resend-otp', [WorkerPasswordController::class, 'resendOtp'])
        ->middleware('throttle:3,10')->name('otp.resend');

    // Authenticated worker only
    Route::middleware(['worker', 'worker.active'])->group(function () {
        Route::post('/logout', [WorkerLoginController::class, 'logout'])->name('logout');

        Route::post('/password', [WorkerLoginController::class, 'changePassword'])->name('password.change');

        Route::get('/pipeline', [WorkerPipelineController::class, 'index'])->name('pipeline');
        Route::get('/sr-tracking-mobile/{id}/popup', [SrTrackingController::class, 'popupForWorker'])
        ->name('worker.sr.tracking.popup');

        // Forced reset - outside the gate, or you get a redirect loop
        Route::get('/set-password',  [WorkerPasswordController::class, 'showForcedResetForm'])->name('password.forced');
        Route::post('/set-password', [WorkerPasswordController::class, 'forcedReset'])->name('password.forced.update');
        // worker dashboard
        Route::get('/dashboard', [MlDashboardController::class, 'index'])->name('dashboard');
        // Everything else sits behind the reset gate
        Route::middleware('worker.reset')->group(function () {
            Route::get('/pipeline', [WorkerPipelineController::class, 'index'])->name('pipeline');
            Route::get('/pipeline/refresh', [WorkerPipelineController::class, 'refresh'])->name('pipeline.refresh');

            Route::post('/job/accept',     [WorkerPipelineController::class, 'accept'])->name('job.accept');
            Route::post('/job/reschedule', [WorkerPipelineController::class, 'reschedule'])->name('job.reschedule');
            Route::post('/job/hold',       [WorkerPipelineController::class, 'hold'])->name('job.hold');
            Route::post('/job/resume',     [WorkerPipelineController::class, 'resume'])->name('job.resume');

            Route::post('/punch/in',        [WorkerpunchController::class, 'punchIn'])->name('punch.in');
            Route::post('/punch/hold',      [WorkerpunchController::class, 'hold'])->name('punch.hold');
            Route::post('/punch/out',       [WorkerpunchController::class, 'punchOut'])->name('punch.out');
            Route::post('/punch/upload',    [WorkerpunchController::class, 'upload'])->name('punch.upload');
            Route::post('/punch/expense',   [WorkerpunchController::class, 'expense'])->name('punch.expense');
            Route::post('/punch/expense/delete', [WorkerPipelineController::class, 'expenseDelete'])->name('punch.expense.delete');            Route::post('/punch/signature', [WorkerpunchController::class, 'signature'])->name('punch.signature');

            Route::get('/history', [WorkerPipelineController::class, 'history'])->name('history');
            Route::get('/profile', [WorkerPipelineController::class, 'profile'])->name('profile');


            Route::post('/punch/photo/delete', [WorkerpunchController::class, 'deletePhoto'])->name('punch.photo.delete');
        });
    });
});
