<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        $user    = auth()->user();
        $vehicle = $user->vehicles()
            ->with([
                'maintenanceSchedules.maintenanceType',
                'maintenanceSchedules.jobs' => fn($q) => $q->whereIn('status', ['pending', 'in_shop', 'in_progress']),
                'maintenanceSchedules.jobs.mechanic',
                'jobs' => fn($q) => $q->whereIn('status', ['pending', 'in_shop', 'in_progress'])  // add this
                    ->where('job_type', 'report'),
                'jobs.mechanic',  // add this
                'jobs.report',    // add this
            ])
            ->first();

        $recentReports = $user->submittedReports()
            ->with(['vehicle', 'job.mechanic'])
            ->latest()
            ->take(5)
            ->get();

        return view('driver.dashboard', compact('vehicle', 'recentReports'));
    }
}
