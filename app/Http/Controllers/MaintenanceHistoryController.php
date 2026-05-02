<?php

namespace App\Http\Controllers;

use App\Models\Job;
use Illuminate\Http\Request;

class MaintenanceHistoryController extends Controller
{
    /**
     * UC10 — Role-scoped maintenance history.
     *
     * Admin   → all vehicles
     * Driver  → only their assigned vehicle(s)
     * Mechanic→ only vehicles they've worked on
     */
    public function index(Request $request)
    {
        $user  = auth()->user();
        $query = Job::with(['vehicle', 'mechanic', 'report', 'maintenanceSchedule.maintenanceType'])
            ->where('status', 'done');

        if ($user->isDriver()) {
            $vehicleIds = $user->vehicles()->pluck('id');
            $query->whereIn('vehicle_id', $vehicleIds);
        } elseif ($user->isMechanic()) {
            $query->where('mechanic_id', $user->id);
        }

        $query
            ->when($request->vehicle_id, fn($q, $id) => $q->where('vehicle_id', $id))
            ->when($request->from, fn($q, $d) => $q->whereDate('completed_at', '>=', $d))
            ->when($request->to,   fn($q, $d) => $q->whereDate('completed_at', '<=', $d));

        $jobs = $query->latest('completed_at')->paginate(20)->withQueryString();

        return view('shared.maintenance-history', compact('jobs'));
    }
}
