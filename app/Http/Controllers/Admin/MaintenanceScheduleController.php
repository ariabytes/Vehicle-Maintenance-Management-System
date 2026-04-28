<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceSchedule;
use App\Models\MaintenanceType;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class MaintenanceScheduleController extends Controller
{
    public function index(Request $request)
    {
        $query = MaintenanceSchedule::with([
            'vehicle',
            'maintenanceType',
            'jobs' => fn($q) => $q->whereIn('status', ['pending', 'in_shop', 'in_progress']),
        ])
            ->when($request->vehicle_id, fn($q, $id) => $q->where('vehicle_id', $id));

        $schedules = $query->get()
            ->when($request->status, fn($col, $s) => $col->filter(fn($sch) => $sch->status === $s))
            ->when($request->needs_attention, fn($col) => $col->filter(fn($sch) => in_array($sch->status, ['overdue', 'due_soon'])));

        // Manual pagination
        $page     = $request->get('page', 1);
        $perPage  = 10;
        $paginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $schedules->forPage($page, $perPage),
            $schedules->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $vehicles = Vehicle::where('status', '!=', 'archived')->orderBy('plate_number')->get();

        return view('admin.schedules.index', ['schedules' => $paginated, 'vehicles' => $vehicles]);
    }

    public function create(Request $request)
    {
        $vehicles = Vehicle::where('status', '!=', 'archived')->orderBy('plate_number')->get();
        $types    = MaintenanceType::orderBy('name')->get();

        $selectedVehicle = $request->vehicle_id
            ? Vehicle::find($request->vehicle_id)
            : null;

        return view('admin.schedules.create', compact('vehicles', 'types', 'selectedVehicle'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id'          => 'required|exists:vehicles,id',
            'maintenance_type_id' => [
                'required',
                'exists:maintenance_types,id',
                \Illuminate\Validation\Rule::unique('maintenance_schedules')
                    ->where('vehicle_id', $request->vehicle_id),
            ],
            'last_service_odo'    => 'required|integer|min:0',
            'last_service_date'   => 'nullable|date',
            'interval_km'         => 'required|integer|min:1',
            'notes'               => 'nullable|string|max:1000',
        ]);

        $validated['next_due_odo'] = $validated['last_service_odo'] + $validated['interval_km'];

        MaintenanceSchedule::create($validated);

        return redirect()->route('admin.schedules.index')
            ->with('success', 'Maintenance schedule created.');
    }

    public function edit(MaintenanceSchedule $schedule)
    {
        $schedule->load(['vehicle', 'maintenanceType']);
        return view('admin.schedules.edit', compact('schedule'));
    }

    public function update(Request $request, MaintenanceSchedule $schedule)
    {
        $validated = $request->validate([
            'last_service_odo'  => 'required|integer|min:0',
            'last_service_date' => 'nullable|date',
            'interval_km'       => 'required|integer|min:1',
            'notes'             => 'nullable|string|max:1000',
        ]);

        $validated['next_due_odo'] = $validated['last_service_odo'] + $validated['interval_km'];

        $schedule->update($validated);

        return redirect()->route('admin.schedules.index')
            ->with('success', 'Schedule updated.');
    }

    public function destroy(MaintenanceSchedule $schedule)
    {
        $schedule->delete();
        return back()->with('success', 'Schedule removed.');
    }
}
