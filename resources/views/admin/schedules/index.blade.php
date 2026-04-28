<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">PMS Schedules</h2>
            <a href="{{ route('admin.schedules.create') }}"
               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-md hover:bg-blue-700">
                + Add Schedule
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
                <select name="vehicle_id" class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <option value="">All Vehicles</option>
                    @foreach($vehicles as $v)
                        <option value="{{ $v->id }}" {{ request('vehicle_id') == $v->id ? 'selected' : '' }}>{{ $v->plate_number }} — {{ $v->model }}</option>
                    @endforeach
                </select>
                <select name="status" class="border border-gray-300 pl-3 pr-8 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <option value="">All Status</option>
                    <option value="overdue"  {{ request('status') === 'overdue'  ? 'selected' : '' }}>Overdue</option>
                    <option value="due_soon" {{ request('status') === 'due_soon' ? 'selected' : '' }}>Due Soon</option>
                    <option value="ok"       {{ request('status') === 'ok'       ? 'selected' : '' }}>OK</option>
                </select>
                <button type="submit" class="px-4 py-2 bg-gray-800 text-white text-sm rounded-md hover:bg-gray-700">Filter</button>
                <a href="{{ route('admin.schedules.index') }}" class="px-4 py-2 text-sm text-gray-600 hover:underline self-center">Reset</a>
            </form>

            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Vehicle</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Last Service ODO</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Interval</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Next Due ODO</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Remaining</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($schedules as $sched)
                            @php
                                $status = $sched->status;
                                $sc = ['overdue'=>'bg-red-100 text-red-700','due_soon'=>'bg-yellow-100 text-yellow-700','ok'=>'bg-green-100 text-green-700'][$status] ?? '';
                                $sl = ['overdue'=>'Overdue','due_soon'=>'Due Soon','ok'=>'OK'][$status] ?? $status;
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 font-medium text-gray-800">
                                    {{ $sched->vehicle->plate_number }}
                                    <div class="text-xs text-gray-400">{{ $sched->vehicle->model }}</div>
                                </td>
                                <td class="px-4 py-3 text-gray-700">{{ $sched->maintenanceType->name }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ number_format($sched->last_service_odo) }} km</td>
                                <td class="px-4 py-3 text-gray-600">{{ number_format($sched->interval_km) }} km</td>
                                <td class="px-4 py-3 text-gray-600">{{ number_format($sched->next_due_odo) }} km</td>
                                <td class="px-4 py-3 text-gray-600">
                                    @if($status === 'overdue')
                                        <span class="text-red-600 font-semibold">{{ number_format(abs($sched->remaining_km)) }} km overdue</span>
                                    @else
                                        {{ number_format($sched->remaining_km) }} km left
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-0.5 rounded text-xs font-semibold {{ $sc }}">{{ $sl }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('admin.schedules.edit', $sched) }}"
                                           class="text-xs px-3 py-1 bg-gray-100 text-gray-700 rounded hover:bg-gray-200 font-medium">Edit</a>
                                        @if(in_array($status, ['overdue','due_soon']))
                                            @if($sched->jobs->isNotEmpty())
                                                <span class="text-xs px-3 py-1 bg-gray-100 text-gray-500 rounded font-medium whitespace-nowrap cursor-default">
                                                    Assigned
                                                </span>
                                            @else
                                                <a href="{{ route('admin.jobs.from-schedule', $sched) }}"
                                                class="text-xs px-3 py-1 bg-blue-100 text-blue-700 rounded hover:bg-blue-200 font-medium whitespace-nowrap">
                                                    Assign Job
                                                </a>
                                            @endif
                                        @endif
                                        <form method="POST" action="{{ route('admin.schedules.destroy', $sched) }}"
                                              onsubmit="return confirm('Delete this schedule?')">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                class="text-xs px-3 py-1 bg-red-100 text-red-700 rounded hover:bg-red-200 font-medium">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="px-4 py-6 text-center text-gray-400">No schedules found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="px-4 py-3 border-t border-gray-100 text-sm text-gray-500 flex items-center justify-between">
                    <span>Showing {{ $schedules->firstItem() }}–{{ $schedules->lastItem() }} of {{ $schedules->total() }} records</span>
                    {{ $schedules->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>