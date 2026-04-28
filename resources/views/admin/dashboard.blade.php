<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-green-50 border border-green-300 text-green-800 px-4 py-3 rounded">{{ session('success') }}</div>
            @endif

            {{-- Stat Cards --}}
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                <div class="bg-white rounded-xl text-center shadow-sm border border-gray-200 border-b-4 border-b-blue-500 p-6">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Total Vehicles</p>
                    <p class="text-3xl font-bold text-green-800 mt-1">{{ $totalVehicles }}</p>
                </div>
                <div class="bg-white rounded-xl text-center shadow-sm border border-gray-200 border-b-4 border-b-blue-500 p-6">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">In Shop</p>
                    <p class="text-3xl font-bold text-blue-600 mt-1">{{ $vehiclesInShop }}</p>
                </div>
                <div class="bg-white rounded-xl text-center shadow-sm border border-gray-200 border-b-4 border-b-blue-500 p-6">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Open Reports</p>
                    <p class="text-3xl font-bold text-yellow-500 mt-1">{{ $pendingReports }}</p>
                </div>
                <div class="bg-white rounded-xl text-center shadow-sm border border-gray-200 border-b-4 border-b-blue-500 p-6">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Pending Jobs</p>
                    <p class="text-3xl font-bold text-orange-500 mt-1">{{ $pendingJobs }}</p>
                </div>
                <div class="bg-white rounded-xl text-center shadow-sm border border-gray-200 border-b-4 border-b-gray-500 p-6">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Drivers</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">{{ $activeDrivers }}</p>
                </div>
                <div class="bg-white rounded-xl text-center shadow-sm border border-gray-200 border-b-4 border-b-gray-500 p-6">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Mechanics</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">{{ $activeMechanics }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                {{-- Recent Pending Reports --}}
                <div class="bg-white rounded-lg border border-gray-200">
                    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                        <h3 class="font-semibold text-gray-800">Recent Driver Reports</h3>
                        <a href="{{ route('admin.reports.index') }}" class="text-sm text-blue-600 hover:underline">View all →</a>
                    </div>
                    <ul class="divide-y divide-gray-100">
                        @forelse($latestReports as $report)
                            <li class="px-5 py-3 flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-sm font-medium text-gray-800">
                                        {{ $report->vehicle->plate_number }} — {{ ucfirst($report->issue_type) }}
                                    </p>
                                    <p class="text-xs text-gray-500 mt-0.5">
                                        By {{ $report->reporter->first_name }} {{ $report->reporter->last_name }}
                                        · {{ $report->created_at->diffForHumans() }}
                                    </p>
                                </div>
                                <div class="flex flex-col items-end gap-1">
                                    @if($report->status === 'pending')
                                        <a href="{{ route('admin.reports.show', $report) }}"
                                        class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded font-medium whitespace-nowrap">
                                            Pending — Review
                                        </a>
                                    @elseif($report->status === 'approved' && !$report->job)
                                        <a href="{{ route('admin.jobs.from-report', $report) }}"
                                        class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded font-medium whitespace-nowrap">
                                            Approved — Assign Job
                                        </a>
                                    @elseif($report->status === 'approved' && $report->job)
                                        <span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded font-medium whitespace-nowrap">
                                            Approved — Assigned
                                        </span>
                                    @elseif($report->status === 'rejected')
                                        <span class="text-xs bg-red-100 text-red-700 px-2 py-1 rounded font-medium whitespace-nowrap">
                                            Rejected
                                        </span>
                                    @endif
                                </div>
                            </li>
                        @empty
                            <li class="px-5 py-4 text-sm text-gray-400">No pending reports.</li>
                        @endforelse
                    </ul>
                </div>

                {{-- Overdue / Due Soon --}}
                <div class="bg-white rounded-lg border border-gray-200">
                    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                        <h3 class="font-semibold text-gray-800">Maintenance Due / Overdue</h3>
                        <a href="{{ route('admin.schedules.index') }}" class="text-sm text-blue-600 hover:underline">View all →</a>
                    </div>
                    <ul class="divide-y divide-gray-100">
                        @forelse($dueSchedules->take(5) as $sched)
                            <li class="px-5 py-3 flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-sm font-medium text-gray-800">
                                        {{ $sched->vehicle->plate_number }} — {{ $sched->maintenanceType->name }}
                                    </p>
                                    <p class="text-xs text-gray-500 mt-0.5">
                                        Current: {{ number_format($sched->vehicle->current_odometer_km) }} km
                                        · Due: {{ number_format($sched->next_due_odo) }} km
                                    </p>
                                </div>
                                @php
                                    $status = $sched->status;

                                    $badge = match ($status) {
                                        'overdue' => 'bg-red-100 text-red-700',
                                        'due_soon' => 'bg-orange-100 text-orange-700',
                                        default => 'bg-green-100 text-green-700',
                                    };

                                    $label = match ($status) {
                                        'overdue' => 'Overdue',
                                        'due_soon' => 'Due Soon',
                                        default => 'OK',
                                    };
                                @endphp

                                <div class="flex flex-col items-end gap-1">
                                    <span class="text-xs px-2 py-1 rounded font-medium {{ $badge }}">{{ $label }}</span>
                                    @if($sched->jobs->isNotEmpty())
                                        <span class="text-xs bg-gray-100 text-gray-500 px-2 py-1 rounded font-medium whitespace-nowrap">
                                            Assigned
                                        </span>
                                    @else
                                        <a href="{{ route('admin.jobs.from-schedule', $sched) }}"
                                        class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded font-medium whitespace-nowrap">
                                            Assign Job
                                        </a>
                                    @endif
                                </div>
                            </li>
                        @empty
                            <li class="px-5 py-4 text-sm text-gray-400">No maintenance schedules due.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>