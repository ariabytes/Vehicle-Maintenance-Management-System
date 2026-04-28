<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    /**
     * Driver views their assigned vehicle(s) with status and PMS schedules.
     */
    public function index()
    {
        $vehicles = auth()->user()
            ->vehicles()
            ->with(['maintenanceSchedules.maintenanceType'])
            ->get();

        return view('driver.vehicles.index', compact('vehicles'));
    }

    public function show($id)
    {
        // Scope to only vehicles assigned to this driver
        $vehicle = auth()->user()
            ->vehicles()
            ->with(['maintenanceSchedules.maintenanceType'])
            ->findOrFail($id);

        return view('driver.vehicles.show', compact('vehicle'));
    }

    public function updateOdometer(Request $request)
    {
        $request->validate(['odometer' => 'required|integer|min:0']);

        $vehicle = auth()->user()->vehicles()->firstOrFail();

        if ($request->odometer < $vehicle->current_odometer_km) {
            return back()->with('error', 'Odometer cannot go backwards.');
        }

        $vehicle->update(['current_odometer_km' => $request->odometer]);

        return back()->with('success', 'Odometer updated.');
    }
}
