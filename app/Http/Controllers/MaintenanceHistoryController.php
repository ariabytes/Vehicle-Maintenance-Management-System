<?php

namespace App\Http\Controllers;

use App\Models\Job;
use Illuminate\Http\Request;

class MaintenanceHistoryController extends Controller
{

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
            ->when(
                $request->plate_number,
                fn($q, $plate) =>
                $q->whereHas(
                    'vehicle',
                    fn($v) =>
                    $v->where('plate_number', 'like', "%{$plate}%")
                )
            )
            ->when($request->from, fn($q, $d) => $q->whereDate('completed_at', '>=', $d))
            ->when($request->to,   fn($q, $d) => $q->whereDate('completed_at', '<=', $d));

        $jobs = $query->latest('completed_at')->paginate(20)->withQueryString();

        return view('shared.maintenance-history', compact('jobs'));
    }
}
