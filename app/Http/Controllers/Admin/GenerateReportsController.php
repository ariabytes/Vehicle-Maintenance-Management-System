<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\Report;
use Illuminate\Http\Request;

class GenerateReportsController extends Controller
{
    /**
     * UC9 — Admin views a unified list of all jobs
     * (both driver-reported and maintenance-schedule-based).
     */
    public function index(Request $request)
    {
        $jobs = Job::with([
            'vehicle',
            'mechanic',
            'report.reporter',
            'maintenanceSchedule.maintenanceType',
        ])
            ->when($request->job_type,   fn($q, $t) => $q->where('job_type', $t))
            ->when($request->status,     fn($q, $s) => $q->where('status', $s))
            ->when($request->priority,   fn($q, $p) => $q->where('priority', $p))
            ->when($request->mechanic_id, fn($q, $id) => $q->where('mechanic_id', $id))
            ->when($request->vehicle_id, fn($q, $id) => $q->where('vehicle_id', $id))
            ->when($request->from, fn($q, $d) => $q->whereDate('scheduled_at', '>=', $d))
            ->when($request->to,   fn($q, $d) => $q->whereDate('scheduled_at', '<=', $d))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.reports-overview.index', compact('jobs'));
    }
}
