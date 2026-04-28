<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VehicleController extends Controller
{
    public function index(Request $request)
    {
        $vehicles = Vehicle::with('driver')
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->when(
                $request->search,
                fn($q, $s) =>
                $q->where(
                    fn($q2) =>
                    $q2->where('plate_number', 'like', "%{$s}%")
                        ->orWhere('model', 'like', "%{$s}%")
                )
            )
            ->orderBy('plate_number')
            ->paginate(15)
            ->withQueryString();

        return view('admin.vehicles.index', compact('vehicles'));
    }

    public function create()
    {
        $drivers = User::where('role', 'driver')->where('status', 'active')->orderBy('last_name')->get();
        return view('admin.vehicles.create', compact('drivers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'driver_id'           => 'nullable|exists:users,id',
            'plate_number'        => 'required|string|max:20|unique:vehicles',
            'model'               => 'required|string|max:100',
            'vehicle_type'        => 'required|string|max:100',
            'year'                => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'color'               => 'nullable|string|max:50',
            'current_odometer_km' => 'required|integer|min:0',
            'photo'               => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('vehicles', 'public');
        }

        Vehicle::create($validated);

        return redirect()->route('admin.vehicles.index')
            ->with('success', 'Vehicle added successfully.');
    }

    public function show(Vehicle $vehicle)
    {
        $vehicle->load([
            'driver',
            'maintenanceSchedules.maintenanceType',
            'reports.reporter',
            'jobs.mechanic',
        ]);

        return view('admin.vehicles.show', compact('vehicle'));
    }

    public function edit(Vehicle $vehicle)
    {
        $drivers = User::where('role', 'driver')->where('status', 'active')->orderBy('last_name')->get();
        return view('admin.vehicles.edit', compact('vehicle', 'drivers'));
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        $validated = $request->validate([
            'driver_id'           => 'nullable|exists:users,id',
            'plate_number'        => "required|string|max:20|unique:vehicles,plate_number,{$vehicle->id}",
            'model'               => 'required|string|max:100',
            'vehicle_type'        => 'required|string|max:100',
            'year'                => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'color'               => 'nullable|string|max:50',
            'current_odometer_km' => 'required|integer|min:0',
            'photo'               => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            if ($vehicle->photo) {
                Storage::disk('public')->delete($vehicle->photo);
            }
            $validated['photo'] = $request->file('photo')->store('vehicles', 'public');
        }

        $vehicle->update($validated);

        return redirect()->route('admin.vehicles.index')
            ->with('success', 'Vehicle updated successfully.');
    }

    /**
     * Archive instead of delete to preserve history.
     */
    public function archive(Vehicle $vehicle)
    {
        $vehicle->update(['status' => 'archived']);

        return back()->with('success', 'Vehicle archived.');
    }

    public function restore(Vehicle $vehicle)
    {
        $vehicle->update(['status' => 'active']);

        return back()->with('success', 'Vehicle restored to active.');
    }
}
