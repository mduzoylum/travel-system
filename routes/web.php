<?php

use App\DDD\Modules\Reservation\Models\Reservation;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\HotelController;
use App\Http\Controllers\Admin\ContractController;
use App\Http\Controllers\Admin\CreditController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\FirmController;
use App\Http\Controllers\Admin\ApprovalController;
use App\Http\Controllers\Admin\ProfitController;
use App\Http\Controllers\ApprovalController as PublicApprovalController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

Route::get('/approval/{token}/accept', [PublicApprovalController::class, 'accept']);
Route::get('/approval/{token}/reject', [PublicApprovalController::class, 'reject']);

Route::get('/', function () {
    return redirect()->route('admin.dashboard');
})->name('home');

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('dashboard');
    
    // Users
    Route::resource('users', UserController::class);
    Route::post('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::post('users/{user}/resend-verification', [UserController::class, 'resendVerification'])->name('users.resend-verification');
    
    // Hotels
    Route::resource('hotels', HotelController::class);
    Route::post('hotels/import', [HotelController::class, 'import'])->name('hotels.import');
    Route::get('hotels/export', [HotelController::class, 'export'])->name('hotels.export');
    Route::get('hotels/template/download', [HotelController::class, 'downloadTemplate'])->name('hotels.template.download');
    
    // Contracts
    Route::resource('contracts', ContractController::class);
    Route::get('contracts/{contract}/rooms', [ContractController::class, 'rooms'])->name('contracts.rooms');
    Route::post('contracts/{contract}/rooms', [ContractController::class, 'addRoom'])->name('contracts.rooms.store');
    Route::delete('contracts/{contract}/rooms/{room}', [ContractController::class, 'destroyRoom'])->name('contracts.rooms.destroy');
    
    // Credits
    Route::resource('credits', CreditController::class)->parameters(['credits' => 'creditAccount']);
    Route::post('credits/{creditAccount}/add-credit', [CreditController::class, 'addCredit'])->name('credits.add-credit');
    Route::post('credits/{creditAccount}/use-credit', [CreditController::class, 'useCredit'])->name('credits.use-credit');
    Route::get('credits/{creditAccount}/transactions', [CreditController::class, 'transactions'])->name('credits.transactions');
    
    // Firms
    Route::resource('firms', FirmController::class);
    
    // Suppliers
    Route::resource('suppliers', SupplierController::class);
    Route::post('suppliers/{supplier}/sync', [SupplierController::class, 'sync'])->name('suppliers.sync');
    Route::post('suppliers/{supplier}/test-connection', [SupplierController::class, 'testConnection'])->name('suppliers.test-connection');
    
    // Approvals
    // Not: 'requests' route'u resource'tan ÖNCE olmalı (çakışma olmasın diye)
    Route::get('approval-requests', [ApprovalController::class, 'requests'])->name('approval-requests.index');
    Route::post('approval-requests/{approvalRequest}/approve', [ApprovalController::class, 'approveRequest'])->name('approval-requests.approve');
    Route::post('approval-requests/{approvalRequest}/reject', [ApprovalController::class, 'rejectRequest'])->name('approval-requests.reject');
    
    Route::resource('approvals', ApprovalController::class)->parameters(['approvals' => 'scenario']);
    Route::get('approvals/{scenario}/rules', [ApprovalController::class, 'rules'])->name('approvals.rules');
    Route::post('approvals/{scenario}/rules', [ApprovalController::class, 'storeRule'])->name('approvals.rules.store');
    Route::delete('approvals/{scenario}/rules/{rule}', [ApprovalController::class, 'destroyRule'])->name('approvals.rules.destroy');
    
    // Profits
    Route::prefix('profits')->name('profits.')->group(function () {
        Route::get('service-fees', [ProfitController::class, 'serviceFees'])->name('service-fees');
        Route::get('service-fees/create', [ProfitController::class, 'createServiceFee'])->name('service-fees.create');
        Route::post('service-fees', [ProfitController::class, 'storeServiceFee'])->name('service-fees.store');
        Route::get('service-fees/{serviceFee}', [ProfitController::class, 'showServiceFee'])->name('service-fees.show');
        Route::get('service-fees/{serviceFee}/edit', [ProfitController::class, 'editServiceFee'])->name('service-fees.edit');
        Route::put('service-fees/{serviceFee}', [ProfitController::class, 'updateServiceFee'])->name('service-fees.update');
        Route::delete('service-fees/{serviceFee}', [ProfitController::class, 'destroyServiceFee'])->name('service-fees.destroy');
        Route::get('calculations', [ProfitController::class, 'calculations'])->name('calculations');
        Route::get('calculations/{calculation}', [ProfitController::class, 'showCalculation'])->name('calculations.show');
        Route::post('calculate', [ProfitController::class, 'calculate'])->name('calculate');
        Route::get('reports', [ProfitController::class, 'reports'])->name('reports');
        Route::post('reports/generate', [ProfitController::class, 'generateReport'])->name('reports.generate');
    });
    Route::resource('profits', ProfitController::class)->parameters(['profits' => 'profitRule']);
});

Route::get('/dummy', function () {
    return view('emails.approval', [
        'token' => '123-test-token',
        'reservation' => Reservation::latest()->first(),
    ]);
});


