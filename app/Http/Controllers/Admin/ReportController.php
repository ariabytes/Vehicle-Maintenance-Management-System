<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $reports = Report::with(['vehicle', 'reporter', 'reviewer'])
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.reports.index', compact('reports'));
    }

    public function show(Report $report)
    {
        $report->load(['vehicle', 'reporter', 'reviewer', 'job.mechanic']);
        return view('admin.reports.show', compact('report'));
    }

    public function approve(Request $request, Report $report)
    {
        if (! $report->isPending()) {
            return back()->with('error', 'This report has already been reviewed.');
        }

        $request->validate([
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $report->update([
            'status'      => 'approved',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'admin_notes' => $request->admin_notes,
        ]);

        return redirect()->route('admin.reports.show', $report)
            ->with('success', 'Report approved. You can now assign a job to a mechanic.');
    }

    public function reject(Request $request, Report $report)
    {
        if (! $report->isPending()) {
            return back()->with('error', 'This report has already been reviewed.');
        }

        $request->validate([
            'admin_notes' => 'required|string|max:1000',
        ]);

        $report->update([
            'status'      => 'rejected',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'admin_notes' => $request->admin_notes,
        ]);

        return redirect()->route('admin.reports.index')
            ->with('success', 'Report rejected. Driver has been notified.');
    }
}
