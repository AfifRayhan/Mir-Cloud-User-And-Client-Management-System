<?php

use App\Http\Controllers\ProfileController;
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
    Route::get('resource-allocation', [\App\Http\Controllers\ResourceAllocationController::class, 'index'])->name('resource-allocation.index');
    Route::post('resource-allocation', [\App\Http\Controllers\ResourceAllocationController::class, 'process'])->name('resource-allocation.process');

    // Tasks (admin, pro-tech, and tech)
    Route::resource('tasks', \App\Http\Controllers\TaskController::class)->only(['index', 'create', 'store', 'update', 'destroy']);
});

require __DIR__.'/auth.php';
