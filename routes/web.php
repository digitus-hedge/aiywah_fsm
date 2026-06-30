<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ServiceRequestController;
/*
|--------------------------------------------------------------------------
| SR Portal Routes
|--------------------------------------------------------------------------
*/
// login 
Route::middleware('guest')->group(function () {
    Route::get('/login',  [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.attempt');
});
// Dashboard
Route::get('/', function () {
    return view('dashboard');
})->name('dashboard');

// inquiry approval
Route::get('/inquiry-approval', [ServiceRequestController::class, 'approvalIndex'])->name('inquiry-approval.index');
Route::post('/service-requests/{serviceRequest}/approve', [ServiceRequestController::class, 'approve'])->name('service-requests.approve');
Route::post('/service-requests/{serviceRequest}/forward', [ServiceRequestController::class, 'forward'])->name('service-requests.forward');
Route::post('/service-requests/{serviceRequest}/reject',  [ServiceRequestController::class, 'reject'])->name('service-requests.reject');

// service request
Route::get('/sr-registration', [ServiceRequestController::class, 'create'])->name('sr_registration');
Route::get('/service-requests/lookup/{code}', [ServiceRequestController::class, 'lookup'])->name('service-requests.lookup');
Route::post('/service-requests', [ServiceRequestController::class, 'store'])->name('service-requests.store');

Route::get('/dispatch-engine', function () {
    return view('dispatch_engine');
})->name('dispatch_engine');

Route::get('/ticket-summary', function () {
    return view('kanban_view');
})->name('kanban_view');

//client project

Route::get('/clients/create', [ClientController::class, 'create'])->name('clients.create');
Route::post('/clients', [ClientController::class, 'store'])->name('clients.store');
Route::get('/clients/{client}/edit', [ClientController::class, 'edit'])->name('clients.edit');
Route::put('/clients/{client}', [ClientController::class, 'update'])->name('clients.update');
Route::get('clients/lookup-by-name', [ClientController::class, 'lookupByName'])->name('clients.lookupByName');