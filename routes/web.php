<?php

use Illuminate\Support\Facades\Route;

// Auth controllers
use App\Http\Controllers\Auth\AuthenticatedSessionController;

// Admin controllers
use App\Http\Controllers\Admin\DashboardController        as AdminDashboard;
use App\Http\Controllers\Admin\UserController             as AdminUserController;
use App\Http\Controllers\Admin\VehicleController          as AdminVehicleController;
use App\Http\Controllers\Admin\ReportController           as AdminReportController;
use App\Http\Controllers\Admin\JobController              as AdminJobController;
use App\Http\Controllers\Admin\MaintenanceTypeController;
use App\Http\Controllers\Admin\MaintenanceScheduleController;
use App\Http\Controllers\Admin\GenerateReportsController;

// Driver controllers
use App\Http\Controllers\Driver\DashboardController       as DriverDashboard;
use App\Http\Controllers\Driver\ReportController          as DriverReportController;
use App\Http\Controllers\Driver\VehicleController         as DriverVehicleController;

// Mechanic controllers
use App\Http\Controllers\Mechanic\DashboardController     as MechanicDashboard;
use App\Http\Controllers\Mechanic\JobController           as MechanicJobController;

// Shared
use App\Http\Controllers\MaintenanceHistoryController;

// Public------------------------------------------------------------------------

Route::get('/', fn() => redirect()->route('login'));

//  Auth (Breeze)-----------------------------------------------------------------

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});

//  Admin-----------------------------------------------------------------------------

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Dashboard
        Route::get('dashboard', [AdminDashboard::class, 'index'])->name('dashboard');

        // User (account) management 
        Route::resource('users', AdminUserController::class)->except(['destroy']);
        Route::patch('users/{user}/toggle-status', [AdminUserController::class, 'toggleStatus'])
            ->name('users.toggle-status');

        // Vehicle management 
        Route::resource('vehicles', AdminVehicleController::class)->only(['index', 'create', 'store', 'edit', 'update']);
        Route::patch('vehicles/{vehicle}/archive', [AdminVehicleController::class, 'archive'])
            ->name('vehicles.archive');
        Route::patch('vehicles/{vehicle}/restore', [AdminVehicleController::class, 'restore'])
            ->name('vehicles.restore');

        // Driver report review
        Route::resource('reports', AdminReportController::class)->only(['index', 'show']);
        Route::patch('reports/{report}/approve', [AdminReportController::class, 'approve'])
            ->name('reports.approve');
        Route::patch('reports/{report}/reject', [AdminReportController::class, 'reject'])
            ->name('reports.reject');

        // Job management
        Route::resource('jobs', AdminJobController::class)->only(['index', 'show', 'update', 'store']);
        Route::get('jobs/from-report/{report}',   [AdminJobController::class, 'createFromReport'])
            ->name('jobs.from-report');
        Route::get('jobs/from-schedule/{schedule}', [AdminJobController::class, 'createFromSchedule'])
            ->name('jobs.from-schedule');

        // Maintenance schedules
        Route::resource('schedules', MaintenanceScheduleController::class)
            ->except(['show']);

        // Maintenance type
        Route::resource('maintenance-types', MaintenanceTypeController::class)->except(['show']);

        // Reports overview 
        Route::get('reports-overview', [GenerateReportsController::class, 'index'])
            ->name('reports-overview.index');

        // Maintenance history 
        Route::get('maintenance-history', [MaintenanceHistoryController::class, 'index'])
            ->name('maintenance-history');
    });

//  Driver --------------------------------------------------------------------------------------------------------------------------

Route::middleware(['auth', 'role:driver'])
    ->prefix('driver')
    ->name('driver.')
    ->group(function () {

        Route::get('dashboard', [DriverDashboard::class, 'index'])->name('dashboard');

        // Update odometer
        Route::patch('odometer', [DriverVehicleController::class, 'updateOdometer'])->name('odometer.update');

        // Issue reports
        Route::resource('reports', DriverReportController::class);

        // Maintenance history
        Route::get('maintenance-history', [MaintenanceHistoryController::class, 'index'])
            ->name('maintenance-history');
    });

//  Mechanic -------------------------------------------------------------------

Route::middleware(['auth', 'role:mechanic'])
    ->prefix('mechanic')
    ->name('mechanic.')
    ->group(function () {

        Route::get('dashboard', [MechanicDashboard::class, 'index'])->name('dashboard');

        // Assigned jobs 
        Route::resource('jobs', MechanicJobController::class)->only(['index', 'show']);

        // Update job status 
        Route::patch('jobs/{job}/update-status', [MechanicJobController::class, 'updateStatus'])
            ->name('jobs.update-status');

        // Maintenance history — scoped to mechanic's jobs 
        Route::get('maintenance-history', [MaintenanceHistoryController::class, 'index'])
            ->name('maintenance-history');
    });
