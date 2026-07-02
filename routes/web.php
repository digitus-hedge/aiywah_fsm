<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ServiceRequestController;
use App\Http\Controllers\UserProvisioningController;

// auth routes

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])
        ->name('login');

    Route::post('/login', [LoginController::class, 'login'])
        ->name('login.attempt');
});

    Route::post('/logout', function (Illuminate\Http\Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/login');
})->name('logout')->middleware('auth');
//redirect to login
Route::get('/', function () {
    return redirect()->route('login');
});

//protected routes
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // User Provisioning
    Route::get('/user-provisioning', [UserProvisioningController::class, 'index'])
        ->name('user_provisioning');

    Route::post('/user-provisioning', [UserProvisioningController::class, 'store'])
    ->name('user_provisioning.store');
    Route::put('/user-provisioning/{user}', [UserProvisioningController::class, 'update'])
    ->name('user_provisioning.update');

    // Inquiry Approval
    Route::get('/inquiry-approval', [ServiceRequestController::class, 'approvalIndex'])
        ->name('inquiry-approval.index');

    Route::post('/service-requests/{serviceRequest}/approve', [ServiceRequestController::class, 'approve'])
        ->name('service-requests.approve');

    Route::post('/service-requests/{serviceRequest}/forward', [ServiceRequestController::class, 'forward'])
        ->name('service-requests.forward');

    Route::post('/service-requests/{serviceRequest}/reject', [ServiceRequestController::class, 'reject'])
        ->name('service-requests.reject');

    // Service Request
    Route::get('/sr-registration', [ServiceRequestController::class, 'create'])
        ->name('sr_registration');

    Route::get('/service-requests/lookup/{code}', [ServiceRequestController::class, 'lookup'])
        ->name('service-requests.lookup');

    Route::post('/service-requests', [ServiceRequestController::class, 'store'])
        ->name('service-requests.store');

    // Dispatch Engine
    Route::get('/dispatch-engine', function () {
        return view('dispatch_engine');
    })->name('dispatch_engine');

    // Ticket Summary
    Route::get('/ticket-summary', function () {
        return view('kanban_view');
    })->name('kanban_view');

    // Client
    Route::get('/clients/create', [ClientController::class, 'create'])
        ->name('clients.create');

    Route::post('/clients', [ClientController::class, 'store'])
        ->name('clients.store');

    Route::get('/clients/{client}/edit', [ClientController::class, 'edit'])
        ->name('clients.edit');

    Route::put('/clients/{client}', [ClientController::class, 'update'])
        ->name('clients.update');

    Route::get('/clients/lookup-by-name', [ClientController::class, 'lookupByName'])
        ->name('clients.lookupByName');
});