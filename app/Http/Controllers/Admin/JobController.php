<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\Report;
use App\Models\MaintenanceSchedule;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class JobController extends Controller
{
    public function index(Request $request)
    {
        $jobs = Job::with(['vehicle', 'mechanic', 'report', 'maintenanceSchedule.maintenanceType'])
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->when($request->mechanic_id, fn($q, $id) => $q->where('mechanic_id', $id))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $mechanics = User::where('role', 'mechanic')->where('status', 'active')->orderBy('last_name')->get();

        return view('admin.jobs.index', compact('jobs', 'mechanics'));
    }

    public function show(Job $job)
    {
        $job->load(['vehicle.driver', 'report.reporter', 'maintenanceSchedule.maintenanceType']);

        $mechanics = \App\Models\User::where('role', 'mechanic')
            ->where('status', 'active')
            ->orderBy('last_name')
            ->get();

        return view('admin.jobs.show', compact('job', 'mechanics'));
    }

    /**
     * Create a job from an approved report.
     */
    public function createFromReport(Report $report)
    {
        if (! $report->isApproved()) {
            return back()->with('error', 'Report must be approved before assigning a job.');
        }

        if ($report->job) {
            return back()->with('error', 'A job has already been created for this report.');
        }

        $mechanics = User::where('role', 'mechanic')->where('status', 'active')->orderBy('last_name')->get();

        return view('admin.jobs.create', [
            'source'   => $report,
            'type'     => 'report',
            'mechanics' => $mechanics,
        ]);
    }

    /**
     * Create a job from a due maintenance schedule.
     */
    public function createFromSchedule(MaintenanceSchedule $schedule)
    {
        $mechanics = User::where('role', 'mechanic')->where('status', 'active')->orderBy('last_name')->get();

        return view('admin.jobs.create', [
            'source'    => $schedule,
            'type'      => 'maintenance_schedule',
            'mechanics' => $mechanics,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id'           => 'required|exists:vehicles,id',
            'mechanic_id'          => 'required|exists:users,id',
            'report_id'            => 'nullable|exists:reports,id',
            'maintenance_sched_id' => 'nullable|exists:maintenance_schedules,id',
            'job_type'             => 'required|in:report,maintenance_schedule',
            'priority'             => 'required|in:low,normal,high',
            'scheduled_at'         => 'required|date',
        ]);

        Job::create($validated);

        // If from report, update vehicle status to in_shop
        if ($validated['report_id'] ?? null) {
            Vehicle::find($validated['vehicle_id'])->update(['status' => 'in_shop']);
        }

        return redirect()->route('admin.jobs.index')
            ->with('success', 'Job assigned to mechanic successfully.');
    }

    /**
     * Admin can update priority or reschedule, but NOT the status
     * (that's the mechanic's job).
     */
    public function update(Request $request, Job $job)
    {
        $validated = $request->validate([
            'mechanic_id'  => 'required|exists:users,id',
            'priority'     => 'required|in:low,normal,high',
            'scheduled_at' => 'required|date',
        ]);

        $job->update($validated);

        return back()->with('success', 'Job updated.');
    }
}
