<?php

use App\DDD\Modules\Reservation\Models\Reservation;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\HotelController;
use App\Http\Controllers\Admin\ContractController;
use App\Http\Controllers\Admin\CreditController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\SupplierGroupController;
use App\Http\Controllers\Admin\FirmController;
use App\Http\Controllers\Admin\ApprovalController;
use App\Http\Controllers\Admin\ProfitController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\SupplierPaymentController;
use App\Http\Controllers\ApprovalController as PublicApprovalController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

Route::get('/approval/{token}/accept', [PublicApprovalController::class, 'accept']);
Route::get('/approval/{token}/reject', [PublicApprovalController::class, 'reject']);

// Public API endpoints (no middleware)
Route::get('/api/hotels/by-destination', [HotelController::class, 'getHotelsByDestination'])->name('api.hotels.by-destination')->withoutMiddleware(['auth', 'admin']);
Route::get('/api/contracts/by-hotel/{hotelId}', [ContractController::class, 'getContractsByHotel'])->name('api.contracts.by-hotel')->withoutMiddleware(['auth', 'admin']);
Route::get('/api/destinations/cities', [HotelController::class, 'getCities'])->name('api.destinations.cities')->withoutMiddleware(['auth', 'admin']);
Route::get('/api/destinations/sub-destinations', [HotelController::class, 'getSubDestinations'])->name('api.destinations.sub-destinations')->withoutMiddleware(['auth', 'admin']);

Route::get('/', function () {
    return redirect()->route('admin.dashboard');
})->name('home');

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Admin Only Routes (Sadece Admin Erişebilir)
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('dashboard');
    
    // Users - Admin only operations
    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::get('users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('users', [UserController::class, 'store'])->name('users.store');
    Route::get('users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::post('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::post('users/{user}/resend-verification', [UserController::class, 'resendVerification'])->name('users.resend-verification');
    
    // Hotels
    Route::resource('hotels', HotelController::class);
    Route::post('hotels/import', [HotelController::class, 'import'])->name('hotels.import');
    Route::get('hotels/export', [HotelController::class, 'export'])->name('hotels.export');
    Route::get('hotels/template/download', [HotelController::class, 'downloadTemplate'])->name('hotels.template.download');
    Route::get('hotels/by-destination', [HotelController::class, 'getHotelsByDestination'])->name('hotels.by-destination');
    
    // Contracts
    Route::resource('contracts', ContractController::class);
    Route::get('contracts/{contract}/rooms', [ContractController::class, 'rooms'])->name('contracts.rooms');
    Route::post('contracts/{contract}/rooms', [ContractController::class, 'addRoom'])->name('contracts.rooms.store');
    Route::delete('contracts/{contract}/rooms/{room}', [ContractController::class, 'destroyRoom'])->name('contracts.rooms.destroy');
    Route::get('contracts/by-hotel/{hotelId}', [ContractController::class, 'getContractsByHotel'])->name('contracts.by-hotel');
    
    // Credits
    Route::resource('credits', CreditController::class)->parameters(['credits' => 'creditAccount']);
    Route::post('credits/{creditAccount}/add-credit', [CreditController::class, 'addCredit'])->name('credits.add-credit');
    Route::post('credits/{creditAccount}/use-credit', [CreditController::class, 'useCredit'])->name('credits.use-credit');
    Route::get('credits/{creditAccount}/transactions', [CreditController::class, 'transactions'])->name('credits.transactions');
    
    // Supplier Payments
    Route::get('supplier-payments', [SupplierPaymentController::class, 'index'])->name('supplier-payments.index');
    Route::put('supplier-payments/{supplierPayment}/status', [SupplierPaymentController::class, 'updateStatus'])->name('supplier-payments.update-status');
    
    // Firms
    Route::resource('firms', FirmController::class);
    
    // Approvals
    // Not: 'requests' route'u resource'tan ÖNCE olmalı (çakışma olmasın diye)
    Route::get('approval-requests', [ApprovalController::class, 'requests'])->name('approval-requests.index');
    Route::post('approval-requests/{approvalRequest}/approve', [ApprovalController::class, 'approveRequest'])->name('approval-requests.approve');
    Route::post('approval-requests/{approvalRequest}/reject', [ApprovalController::class, 'rejectRequest'])->name('approval-requests.reject');
    
    Route::resource('approvals', ApprovalController::class)->parameters(['approvals' => 'scenario']);
    Route::get('approvals/{scenario}/rules', [ApprovalController::class, 'rules'])->name('approvals.rules');
    Route::post('approvals/{scenario}/rules', [ApprovalController::class, 'storeRule'])->name('approvals.rules.store');
    Route::delete('approvals/{scenario}/rules/{rule}', [ApprovalController::class, 'destroyRule'])->name('approvals.rules.destroy');
    
    // Tedarikçi Grupları - Sadece Admin
    Route::resource('supplier-groups', SupplierGroupController::class);
    Route::post('supplier-groups/{supplierGroup}/toggle-status', [SupplierGroupController::class, 'toggleStatus'])->name('supplier-groups.toggle-status');
    
    // Settings - Sadece Admin
    Route::get('settings', [SettingsController::class, 'index'])->name('settings');
    Route::put('settings/firm/{firm}', [SettingsController::class, 'updateFirmSettings'])->name('settings.firm.update');
    Route::put('settings/supplier/{supplier}', [SettingsController::class, 'updateSupplierSettings'])->name('settings.supplier.update');
    Route::put('settings/system', [SettingsController::class, 'updateSystemSettings'])->name('settings.system.update');
    
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

// Authenticated Routes (Tüm Giriş Yapmış Kullanıcılar Erişebilir)
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    // User Profile - Tüm kullanıcılar kendi profillerini düzenleyebilir
    Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');
    
    // Suppliers - Normal kullanıcılar sadece API olmayan tedarikçileri görebilir
    Route::get('suppliers', [SupplierController::class, 'index'])->name('suppliers.index');
    Route::get('suppliers/create', [SupplierController::class, 'create'])->name('suppliers.create');
    Route::post('suppliers', [SupplierController::class, 'store'])->name('suppliers.store');
    Route::get('suppliers/{supplier}', [SupplierController::class, 'show'])->name('suppliers.show');
    Route::get('suppliers/{supplier}/edit', [SupplierController::class, 'edit'])->name('suppliers.edit');
    Route::put('suppliers/{supplier}', [SupplierController::class, 'update'])->name('suppliers.update');
    Route::delete('suppliers/{supplier}', [SupplierController::class, 'destroy'])->name('suppliers.destroy');
    Route::post('suppliers/{supplier}/sync', [SupplierController::class, 'sync'])->name('suppliers.sync');
    Route::post('suppliers/{supplier}/test-connection', [SupplierController::class, 'testConnection'])->name('suppliers.test-connection');
    Route::post('suppliers/{supplier}/toggle-status', [SupplierController::class, 'toggleStatus'])->name('suppliers.toggle-status');
});

Route::get('/dummy', function () {
    return view('emails.approval', [
        'token' => '123-test-token',
        'reservation' => Reservation::latest()->first(),
    ]);
});

// Test route for multi-period pricing
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('test-pricing', function () {
        $selectedRoom = null;
        if (request('room_id')) {
            $selectedRoom = \App\DDD\Modules\Contract\Models\ContractRoom::with('periods', 'contract.hotel')->find(request('room_id'));
        }
        return view('admin.test-pricing', compact('selectedRoom'));
    })->name('test.pricing');
    
    Route::post('test-pricing/calculate', function (\Illuminate\Http\Request $request) {
        \Log::info('Test pricing request', $request->all());
        
        $room = \App\DDD\Modules\Contract\Models\ContractRoom::with('periods')->find($request->room_id);
        $user = auth()->user();
        
        if (!$room) {
            return back()->withErrors(['message' => 'Oda bulunamadı']);
        }
        
        $pricingService = new \App\DDD\Modules\Contract\Services\PricingService();
        
        try {
            \Log::info('Calculating price', [
                'room_id' => $room->id,
                'checkin' => $request->checkin_date,
                'checkout' => $request->checkout_date,
                'currency' => $request->currency,
            ]);
            
            $result = $pricingService->calculateMultiPeriodPrice(
                $room,
                $user,
                $request->checkin_date,
                $request->checkout_date,
                $request->currency ?? 'TRY',
                $request->guest_count ?? 1
            );
            
            \Log::info('Calculation result', ['nights' => $result['nights'], 'total' => $result['grand_total']]);
            
            return view('admin.test-pricing', compact('room', 'result'));
        } catch (\Exception $e) {
            \Log::error('Pricing calculation error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withErrors(['message' => $e->getMessage()])->withInput();
        }
    })->name('test.pricing.calculate');
});


