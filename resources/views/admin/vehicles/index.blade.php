<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Manage Vehicles</h2>
            <a href="{{ route('admin.vehicles.create') }}"
               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-md hover:bg-blue-700">
                + Add Vehicle
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            @if(session('success'))
                <div class="bg-green-50 border border-green-300 text-green-800 px-4 py-3 rounded">{{ session('success') }}</div>
            @endif

            {{-- Filters --}}
            <form method="GET" class="flex gap-3 flex-wrap">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search plate or model..."
                       class="border border-gray-300 rounded-md px-3 py-2 text-sm w-56 focus:outline-none focus:ring-1 focus:ring-blue-500">
                <select name="status" class="border border-gray-300 rounded-md pl-3 pr-8 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <option value="">All Status</option>
                    <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>Active</option>
                    <option value="in_shop"  {{ request('status') === 'in_shop'  ? 'selected' : '' }}>In Shop</option>
                    <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Archived</option>
                </select>
                <button type="submit" class="px-4 py-2 bg-gray-800 text-white text-sm rounded-md hover:bg-gray-700">Filter</button>
                <a href="{{ route('admin.vehicles.index') }}" class="px-4 py-2 text-sm text-gray-600 hover:underline self-center">Reset</a>
            </form>

            {{-- Table --}}
            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Plate No.</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Model</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Year</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Odometer (km)</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Assigned Driver</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($vehicles as $vehicle)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 font-medium text-gray-800">{{ $vehicle->plate_number }}</td>
                                <td class="px-4 py-3 text-gray-700">
                                    {{ $vehicle->model }}
                                    <div class="text-xs text-gray-400">{{ ucfirst($vehicle->vehicle_type) }}</div>
                                </td>
                                <td class="px-4 py-3 text-gray-600">{{ $vehicle->year }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ number_format($vehicle->current_odometer_km) }}</td>
                                <td class="px-4 py-3 text-gray-600">
                                    {{ $vehicle->driver ? $vehicle->driver->full_name : 'Unassigned' }}
                                </td>
                                <td class="px-4 py-3">
                                    @php
                                        $sc = ['active'=>'bg-green-100 text-green-700','in_shop'=>'bg-blue-100 text-blue-700','archived'=>'bg-gray-100 text-gray-500'][$vehicle->status] ?? 'bg-gray-100 text-gray-500';
                                        $sl = ['active'=>'Active','in_shop'=>'In Shop','archived'=>'Archived'][$vehicle->status] ?? $vehicle->status;
                                    @endphp
                                    <span class="px-2 py-0.5 rounded text-xs font-semibold {{ $sc }}">{{ $sl }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        @if($vehicle->status !== 'archived')
                                            <a href="{{ route('admin.vehicles.edit', $vehicle) }}"
                                               class="text-xs px-3 py-1 bg-gray-100 text-gray-700 rounded hover:bg-gray-200 font-medium">Edit</a>
                                            <form method="POST" action="{{ route('admin.vehicles.archive', $vehicle) }}"
                                                  onsubmit="return confirm('Archive this vehicle?')">
                                                @csrf @method('PATCH')
                                                <button type="submit"
                                                    class="text-xs px-3 py-1 bg-red-100 text-red-700 rounded hover:bg-red-200 font-medium">Archive</button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('admin.vehicles.restore', $vehicle) }}">
                                                @csrf @method('PATCH')
                                                <button type="submit"
                                                    class="text-xs px-3 py-1 bg-green-100 text-green-700 rounded hover:bg-green-200 font-medium">Restore</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="px-4 py-6 text-center text-gray-400">No vehicles found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="px-4 py-3 border-t border-gray-100 text-sm text-gray-500 flex items-center justify-between">
                    <span>Showing {{ $vehicles->firstItem() }}–{{ $vehicles->lastItem() }} of {{ $vehicles->total() }} records</span>
                    {{ $vehicles->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>