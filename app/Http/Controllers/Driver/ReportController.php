<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Driver sees only their own submitted reports.
     */
    public function index()
    {
        $reports = Report::with('vehicle')
            ->where('reported_by', auth()->id())
            ->latest()
            ->paginate(10);

        return view('driver.reports.index', compact('reports'));
    }

    public function create()
    {
        // Driver can only report their assigned vehicles
        $vehicles = auth()->user()->vehicles()->where('status', 'active')->get();

        if ($vehicles->isEmpty()) {
            return back()->with('error', 'You have no active assigned vehicles to report.');
        }

        return view('driver.reports.create', compact('vehicles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id'  => [
                'required',
                'exists:vehicles,id',
                // Ensure the driver can only report their own vehicle
                function ($attribute, $value, $fail) {
                    $vehicle = Vehicle::find($value);
                    if (! $vehicle || $vehicle->driver_id !== auth()->id()) {
                        $fail('You can only report your assigned vehicle.');
                    }
                },
            ],
            'issue_type'  => 'required|in:engine,brakes,tires,electrical,cooling,others',
            'description' => 'nullable|string|max:2000',
        ]);

        $validated['reported_by'] = auth()->id();
        $validated['status']      = 'pending';

        Report::create($validated);

        return redirect()->route('driver.reports.index')
            ->with('success', 'Issue reported successfully. Admin will review it shortly.');
    }

    public function show(Report $report)
    {
        // Drivers can only view their own reports
        if ($report->reported_by !== auth()->id()) {
            abort(403);
        }

        $report->load(['vehicle', 'job.mechanic']);
        return view('driver.reports.show', compact('report'));
    }

    /**
     * Drivers can edit a report only while it's still pending.
     */
    public function edit(Report $report)
    {
        if ($report->reported_by !== auth()->id()) abort(403);
        if (! $report->isPending()) {
            return back()->with('error', 'You can only edit pending reports.');
        }

        $vehicles = auth()->user()->vehicles()->where('status', 'active')->get();
        return view('driver.reports.edit', compact('report', 'vehicles'));
    }

    public function update(Request $request, Report $report)
    {
        if ($report->reported_by !== auth()->id()) abort(403);
        if (! $report->isPending()) {
            return back()->with('error', 'You can only edit pending reports.');
        }

        $validated = $request->validate([
            'issue_type'  => 'required|in:engine,brakes,tires,electrical,cooling,others',
            'description' => 'nullable|string|max:2000',
        ]);

        $report->update($validated);

        return redirect()->route('driver.reports.index')
            ->with('success', 'Report updated.');
    }

    /**
     * Drivers can cancel a pending report.
     */
    public function destroy(Report $report)
    {
        if ($report->reported_by !== auth()->id()) abort(403);
        if (! $report->isPending()) {
            return back()->with('error', 'Only pending reports can be cancelled.');
        }

        $report->delete();

        return redirect()->route('driver.reports.index')
            ->with('success', 'Report cancelled.');
    }
}
