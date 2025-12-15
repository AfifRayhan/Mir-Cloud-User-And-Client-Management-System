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
    $incompleteTaskCount = \App\Models\Task::where('assigned_to', \Illuminate\Support\Facades\Auth::id())
        ->whereNull('completed_at')
        ->count();
    
    return view('dashboard', compact('incompleteTaskCount'));
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::put('/password', [\App\Http\Controllers\Auth\PasswordController::class, 'update'])->name('profile.password');
    
    // Customer routes (Admin, Pro-Tech, KAM, Pro-KAM, Management)
    Route::middleware('role:admin,pro-tech,kam,pro-kam,management')->group(function () {
        Route::resource('customers', \App\Http\Controllers\CustomerController::class);
    });

    // Resource allocation (Admin, Pro-Tech, KAM, Pro-KAM, Management) - Tech EXCLUDED
    Route::middleware('role:admin,pro-tech,kam,pro-kam,management')->group(function () {
        Route::get('resource-allocation', [ResourceAllocationController::class, 'index'])->name('resource-allocation.index');
        Route::post('resource-allocation', [ResourceAllocationController::class, 'process'])->name('resource-allocation.process');
    
        Route::get('resource-allocation/{customer}/allocate', [ResourceAllocationController::class, 'allocationForm'])->name('resource-allocation.allocate');
        Route::post('resource-allocation/{customer}/allocate', [ResourceAllocationController::class, 'storeAllocation'])->name('resource-allocation.store');
    });

    // User management (Admin only)
    Route::middleware('role:admin')->group(function () {
        Route::resource('users', \App\Http\Controllers\UserManagementController::class);
    });

    // Platform management (Admin, Pro-Tech, Pro-KAM, Management)
    Route::middleware('role:admin,pro-tech,pro-kam,management')->group(function () {
        Route::get('platforms', [PlatformManagementController::class, 'index'])->name('platforms.index');
        Route::post('platforms', [PlatformManagementController::class, 'store'])->name('platforms.store');
        Route::delete('platforms/{platform}', [PlatformManagementController::class, 'destroy'])->name('platforms.destroy');
    });

    // Service management (Admin, Pro-Tech, Pro-KAM, Management)
    Route::middleware('role:admin,pro-tech,pro-kam,management')->group(function () {
        Route::get('services', [ServiceManagementController::class, 'index'])->name('services.index');
        Route::post('services', [ServiceManagementController::class, 'store'])->name('services.store');
        Route::put('services/{service}', [ServiceManagementController::class, 'update'])->name('services.update');
        // Removed duplicate route definition
        Route::delete('services/{service}', [ServiceManagementController::class, 'destroy'])->name('services.destroy');
    });

    // Mail routes (Admin, Pro-Tech, KAM, Pro-KAM, Management) - Assuming Techs don't need this
    Route::middleware('role:admin,pro-tech,kam,pro-kam,management')->group(function () {
         Route::get('mail/create', [\App\Http\Controllers\MailController::class, 'create'])->name('mail.create');
         Route::post('mail', [\App\Http\Controllers\MailController::class, 'store'])->name('mail.store');
    });

    // Task Management (Admin and ProTech and Management)
    Route::middleware('role:admin,pro-tech,management')->group(function () {
        Route::get('task-management', [\App\Http\Controllers\TaskManagementController::class, 'index'])->name('task-management.index');
        Route::get('task-management/{task}/details', [\App\Http\Controllers\TaskManagementController::class, 'getDetails'])->name('task-management.details');
        Route::post('task-management/{task}/assign', [\App\Http\Controllers\TaskManagementController::class, 'assign'])->name('task-management.assign');
    });

    // My Tasks (All authenticated users)
    Route::get('my-tasks', [\App\Http\Controllers\MyTaskController::class, 'index'])->name('my-tasks.index');
    Route::get('my-tasks/{task}/details', [\App\Http\Controllers\MyTaskController::class, 'getDetails'])->name('my-tasks.details');
    Route::get('my-tasks/{task}/complete', [\App\Http\Controllers\MyTaskController::class, 'complete'])->name('my-tasks.complete')->middleware('signed');
});

require __DIR__.'/auth.php';
