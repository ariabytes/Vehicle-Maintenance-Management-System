<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\MaintenanceSchedule;
use App\Models\Report;
use App\Models\User;
use App\Models\Vehicle;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', [
            // Counts for summary cards
            'totalVehicles'     => Vehicle::where('status', '!=', 'archived')->count(),
            'vehiclesInShop'    => Vehicle::where('status', 'in_shop')->count(),
            'pendingReports'    => Report::where('status', 'pending')->count(),
            'pendingJobs'       => Job::where('status', 'pending')->count(),
            'activeDrivers'     => User::where('role', 'driver')->where('status', 'active')->count(),
            'activeMechanics'   => User::where('role', 'mechanic')->where('status', 'active')->count(),

            // Overdue / due-soon schedules (computed attribute, so load and filter in PHP)
            'dueSchedules'  => MaintenanceSchedule::with([  // rename from overdueSchedules to dueSchedules
                'vehicle',
                'maintenanceType',
                'jobs' => fn($q) => $q->whereIn('status', ['pending', 'in_shop', 'in_progress']),
            ])
                ->get()
                ->filter(fn($s) => in_array($s->status, ['overdue', 'due_soon'])),

            'latestReports' => Report::with(['vehicle', 'reporter', 'job'])
                ->where('status', 'pending')
                ->latest()
                ->take(10)
                ->get(),
        ]);
    }
}
