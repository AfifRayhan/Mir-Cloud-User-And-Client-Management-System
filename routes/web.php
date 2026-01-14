<?php

use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\BillingTaskManagementController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\KamTaskManagementController;
use App\Http\Controllers\MailController;
use App\Http\Controllers\MyTaskController;
use App\Http\Controllers\PlatformManagementController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ResourceAllocationController;
use App\Http\Controllers\ServiceManagementController;
use App\Http\Controllers\TaskActionController;
use App\Http\Controllers\TaskManagementController;
use App\Http\Controllers\TechResourceAllocationController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    $incompleteTaskCount = \App\Models\Task::where('assigned_to', \Illuminate\Support\Facades\Auth::id())
        ->whereNull('completed_at')
        ->count();

    $unassignedTaskCount = \App\Models\Task::whereNull('assigned_to')->count();

    return view('dashboard', compact('incompleteTaskCount', 'unassignedTaskCount'));
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::put('/password', [PasswordController::class, 'update'])->name('profile.password');

    // Customer routes (Admin, Pro-Tech, KAM, Pro-KAM, Management)
    Route::middleware('role:admin,pro-tech,kam,pro-kam,management')->group(function () {
        Route::resource('customers', CustomerController::class);
    });

    // Resource allocation (Admin, Pro-Tech, KAM, Pro-KAM, Management) - Tech EXCLUDED
    Route::middleware('role:admin,pro-tech,kam,pro-kam,management')->group(function () {
        Route::get('resource-allocation', [ResourceAllocationController::class, 'index'])->name('resource-allocation.index');
        Route::post('resource-allocation', [ResourceAllocationController::class, 'process'])->name('resource-allocation.process');

        Route::get('resource-allocation/{customer}/allocate', [ResourceAllocationController::class, 'allocationForm'])->name('resource-allocation.allocate');
        Route::post('resource-allocation/{customer}/allocate', [ResourceAllocationController::class, 'storeAllocation'])->name('resource-allocation.store');
    });

    // Tech resource allocation (Tech only)
    Route::middleware('role:tech,admin')->group(function () {
        Route::get('tech-resource-allocation', [TechResourceAllocationController::class, 'index'])->name('tech-resource-allocation.index');
        Route::get('tech-resource-allocation/{customer}/allocate', [TechResourceAllocationController::class, 'allocationForm'])->name('tech-resource-allocation.allocate');
        Route::post('tech-resource-allocation/{customer}/allocate', [TechResourceAllocationController::class, 'storeAllocation'])->name('tech-resource-allocation.store');
    });

    // User management (Admin only)
    Route::middleware('role:admin')->group(function () {
        Route::resource('users', UserManagementController::class);
    });

    // Platform management (Admin, Pro-Tech, Tech, Management)
    Route::middleware('role:admin,pro-tech,tech,management')->group(function () {
        Route::get('platforms', [PlatformManagementController::class, 'index'])->name('platforms.index');
        Route::post('platforms', [PlatformManagementController::class, 'store'])->name('platforms.store');
        Route::delete('platforms/{platform}', [PlatformManagementController::class, 'destroy'])->name('platforms.destroy');
    });

    // Service management (Admin, Pro-Tech, Management)
    Route::middleware('role:admin,pro-tech,management')->group(function () {
        Route::get('services', [ServiceManagementController::class, 'index'])->name('services.index');
        Route::post('services', [ServiceManagementController::class, 'store'])->name('services.store');
        Route::put('services/{service}', [ServiceManagementController::class, 'update'])->name('services.update');
        // Removed duplicate route definition
        Route::delete('services/{service}', [ServiceManagementController::class, 'destroy'])->name('services.destroy');
    });

    // Mail routes (Admin, Pro-Tech, KAM, Pro-KAM, Management) - Assuming Techs don't need this
    Route::middleware('role:admin,pro-tech,kam,pro-kam,management')->group(function () {
        Route::get('mail/create', [MailController::class, 'create'])->name('mail.create');
        Route::post('mail', [MailController::class, 'store'])->name('mail.store');
    });

    // Task Management (Admin and ProTech and Management)
    Route::middleware('role:admin,pro-tech,management')->group(function () {
        Route::get('task-management', [TaskManagementController::class, 'index'])->name('task-management.index');
        Route::get('task-management/{task}/details', [TaskManagementController::class, 'getDetails'])->name('task-management.details');
        Route::post('task-management/{task}/assign', [TaskManagementController::class, 'assign'])->name('task-management.assign');
    });

    Route::middleware('role:admin,kam,pro-kam')->group(function () {
        Route::get('kam-task-management/export', [KamTaskManagementController::class, 'export'])->name('kam-task-management.export');
        Route::get('kam-task-management/export-customers', [KamTaskManagementController::class, 'exportCustomers'])->name('kam-task-management.export-customers');
        Route::get('kam-task-management', [KamTaskManagementController::class, 'index'])->name('kam-task-management.index');
        Route::get('kam-task-management/{task}/details', [KamTaskManagementController::class, 'getDetails'])->name('kam-task-management.details');
        Route::put('kam-task-management/{task}', [KamTaskManagementController::class, 'update'])->name('kam-task-management.update');
        Route::delete('kam-task-management/{task}', [KamTaskManagementController::class, 'destroy'])->name('kam-task-management.destroy');
    });

    // Billing Task Management (Admin, Bill)
    Route::middleware('role:admin,bill')->group(function () {
        Route::get('billing-task-management', [BillingTaskManagementController::class, 'index'])->name('billing-task-management.index');
        Route::get('billing-task-management/export', [BillingTaskManagementController::class, 'export'])->name('billing-task-management.export');
        Route::get('billing-task-management/export-customers', [BillingTaskManagementController::class, 'exportCustomers'])->name('billing-task-management.export-customers');
        Route::get('billing-task-management/{task}/details', [BillingTaskManagementController::class, 'getDetails'])->name('billing-task-management.details');
        Route::get('billing-task-management/{task}/bill', [BillingTaskManagementController::class, 'bill'])->name('billing-task-management.bill-get');
        Route::post('billing-task-management/{task}/bill', [BillingTaskManagementController::class, 'bill'])->name('billing-task-management.bill');
    });

    // My Tasks (All authenticated users)
    Route::get('my-tasks', [MyTaskController::class, 'index'])->name('my-tasks.index');
    Route::get('my-tasks/{task}/details', [MyTaskController::class, 'getDetails'])->name('my-tasks.details');
    Route::get('my-tasks/{task}/complete', function (App\Models\Task $task) {
        return redirect()->route('my-tasks.index', ['dtid' => $task->id, 'da' => 'complete']);
    });
    Route::post('my-tasks/{task}/complete', [MyTaskController::class, 'complete'])->name('my-tasks.complete');
    Route::post('my-tasks/{task}/platform', [MyTaskController::class, 'updatePlatform'])->name('my-tasks.update-platform');
    Route::get('my-tasks/customer/{customerId}/vdcs', [MyTaskController::class, 'getCustomerVdcs'])->name('my-tasks.customer-vdcs');

    // Task Actions (Email links)
    Route::get('tasks/{task}/approve', [TaskActionController::class, 'approve'])->name('tasks.approve');
    Route::get('tasks/{task}/undo', [TaskActionController::class, 'undo'])->name('tasks.undo');
});

require __DIR__.'/auth.php';
