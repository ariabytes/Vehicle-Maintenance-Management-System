<?php

namespace App\Http\Controllers\Mechanic;

use App\Http\Controllers\Controller;
use App\Models\Job;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $activeJobs = $user->assignedJobs()
            ->with(['vehicle', 'report', 'maintenanceSchedule.maintenanceType'])
            ->whereIn('status', ['pending', 'in_shop', 'in_progress'])
            ->latest()
            ->get();

        $completedCount = $user->assignedJobs()->where('status', 'done')->count();

        return view('mechanic.dashboard', compact('activeJobs', 'completedCount'));
    }
}
