<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceType;
use Illuminate\Http\Request;

class MaintenanceTypeController extends Controller
{
    public function index(Request $request)
    {
        $query = MaintenanceType::withCount('maintenanceSchedules')
            ->orderBy('name');

        // search filter
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $types = $query->paginate(15);

        return view('admin.maintenance-types.index', compact('types'));
    }

    public function create()
    {
        return view('admin.maintenance-types.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                => 'required|string|max:100|unique:maintenance_types,name',
            'default_interval_km' => 'required|integer|min:1',
            'description'         => 'nullable|string|max:500',
        ]);

        MaintenanceType::create($validated);

        return redirect()->route('admin.maintenance-types.index')
            ->with('success', 'Maintenance type created.');
    }

    public function edit(MaintenanceType $maintenanceType)
    {
        return view('admin.maintenance-types.edit', compact('maintenanceType'));
    }

    public function update(Request $request, MaintenanceType $maintenanceType)
    {
        $validated = $request->validate([
            'name'                => "required|string|max:100|unique:maintenance_types,name,{$maintenanceType->id}",
            'default_interval_km' => 'required|integer|min:1',
            'description'         => 'nullable|string|max:500',
        ]);

        $maintenanceType->update($validated);

        return redirect()->route('admin.maintenance-types.index')
            ->with('success', 'Maintenance type updated.');
    }

    public function destroy(MaintenanceType $maintenanceType)
    {
        if ($maintenanceType->maintenanceSchedules()->exists()) {
            return back()->with('error', 'Cannot delete — this type is used in existing schedules.');
        }

        $maintenanceType->delete();

        return back()->with('success', 'Maintenance type deleted.');
    }
}
