<?php

namespace App\Http\Controllers\Mechanic;

use App\Http\Controllers\Controller;
use App\Models\Job;
use Illuminate\Http\Request;

class JobController extends Controller
{
    /**
     * Mechanic sees only their assigned jobs.
     */
    public function index(Request $request)
    {
        $jobs = Job::with(['vehicle', 'report', 'maintenanceSchedule.maintenanceType'])
            ->where('mechanic_id', auth()->id())
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('mechanic.jobs.index', compact('jobs'));
    }

    public function show(Job $job)
    {
        if ($job->mechanic_id !== auth()->id()) abort(403);

        $job->load(['vehicle.driver', 'report.reporter', 'maintenanceSchedule.maintenanceType']);

        return view('mechanic.jobs.show', compact('job'));
    }

    /**
     * Mechanic updates status and optionally adds notes.
     * Status flow: pending → in_shop → in_progress → done
     */
    public function updateStatus(Request $request, Job $job)
    {
        if ($job->mechanic_id !== auth()->id()) abort(403);

        if ($job->isDone()) {
            return back()->with('error', 'This job is already completed.');
        }

        $transitions = [
            'pending'     => 'in_shop',
            'in_shop'     => 'in_progress',
            'in_progress' => 'done',
        ];

        $nextStatus = $transitions[$job->status] ?? null;

        if (! $nextStatus) {
            return back()->with('error', 'Invalid status transition.');
        }

        $request->validate([
            'mechanic_notes'      => 'nullable|string|max:2000',
            'completion_odometer' => 'nullable|integer|min:0',
        ]);

        if ($nextStatus === 'done') {
            $job->markDone(
                $request->mechanic_notes,
                $request->completion_odometer
            );

            $job->vehicle->update([
                'current_odometer_km' => $request->completion_odometer ?? $job->vehicle->current_odometer_km,
            ]);

            $hasOtherActiveJobs = Job::where('vehicle_id', $job->vehicle_id)
                ->where('id', '!=', $job->id)
                ->whereIn('status', ['pending', 'in_shop', 'in_progress'])
                ->exists();

            if (! $hasOtherActiveJobs) {
                $job->vehicle->update(['status' => 'active']);
            }
        } else {
            $job->update(['status' => $nextStatus]);
            if ($nextStatus === 'in_shop') {
                Job::where('vehicle_id', $job->vehicle_id)
                    ->where('id', '!=', $job->id)
                    ->where('status', 'pending')
                    ->update(['status' => 'in_shop']);
            }
        }

        return back()->with('success', "Job status updated to \"{$nextStatus}\".");
    }
}
