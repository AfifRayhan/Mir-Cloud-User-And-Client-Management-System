<?php

use App\Http\Controllers\PlatformManagementController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ResourceAllocationController;
use App\Http\Controllers\ServiceManagementController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::put('/password', [\App\Http\Controllers\Auth\PasswordController::class, 'update'])->name('profile.password');
    
    // Customer routes
    Route::resource('customers', \App\Http\Controllers\CustomerController::class);
    
    // Cloud details routes
    Route::get('customers/{customerId}/cloud-details/create', [\App\Http\Controllers\CloudDetailController::class, 'create'])->name('cloud-details.create');
    Route::post('customers/{customerId}/cloud-details', [\App\Http\Controllers\CloudDetailController::class, 'store'])->name('cloud-details.store');

    // Resource allocation (combined upgrade/downgrade)
    Route::get('resource-allocation', [ResourceAllocationController::class, 'index'])->name('resource-allocation.index');
    Route::post('resource-allocation', [ResourceAllocationController::class, 'process'])->name('resource-allocation.process');
    Route::get('resource-allocation/customer/{customer}/form', [ResourceAllocationController::class, 'cloudDetailForm'])->name('resource-allocation.cloud-form');
    Route::get('resource-allocation/{customer}/allocate', [ResourceAllocationController::class, 'allocationForm'])->name('resource-allocation.allocate');
    Route::post('resource-allocation/{customer}/allocate', [ResourceAllocationController::class, 'storeAllocation'])->name('resource-allocation.store');

    // User management (admin only enforced in controller)
    Route::resource('users', UserManagementController::class)->except(['show']);

    // Platform management
    Route::get('platforms', [PlatformManagementController::class, 'index'])->name('platforms.index');
    Route::post('platforms', [PlatformManagementController::class, 'store'])->name('platforms.store');
    Route::delete('platforms/{platform}', [PlatformManagementController::class, 'destroy'])->name('platforms.destroy');

    // Service management
    Route::get('services', [ServiceManagementController::class, 'index'])->name('services.index');
    Route::post('services', [ServiceManagementController::class, 'store'])->name('services.store');
    Route::put('services/{service}', [ServiceManagementController::class, 'update'])->name('services.update');
    Route::delete('services/{service}', [ServiceManagementController::class, 'destroy'])->name('services.destroy');
});

require __DIR__.'/auth.php';
