<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-green-50 border border-green-300 text-green-800 px-4 py-3 rounded">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="bg-red-50 border border-red-300 text-red-800 px-4 py-3 rounded">{{ session('error') }}</div>
            @endif

            {{-- Stat Cards --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white rounded-2xl border border-gray-200 p-4 border-b-gray-500 border-b-4 text-center">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Assigned Jobs</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">{{ $activeJobs->count() }}</p>
                </div>
                <div class="bg-white rounded-2xl border border-gray-200 p-4 border-b-yellow-500 border-b-4 text-center">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Pending</p>
                    <p class="text-3xl font-bold text-yellow-500 mt-1">{{ $activeJobs->where('status', 'pending')->count() }}</p>
                    <p class="text-xs text-gray-400 mt-1">Awaiting arrival</p>
                </div>
                <div class="bg-white rounded-2xl border border-gray-200 p-4 border-b-blue-500 border-b-4 text-center">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Received</p>
                    <p class="text-3xl font-bold text-blue-500 mt-1">{{ $activeJobs->where('status', 'in_shop')->count() }}</p>
                    <p class="text-xs text-gray-400 mt-1">Cars in shop queue</p>
                </div>
                <div class="bg-white rounded-2xl border border-gray-200 p-4 border-b-orange-500 border-b-4 text-center">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">In Progress</p>
                    <p class="text-3xl font-bold text-orange-500 mt-1">{{ $activeJobs->where('status', 'in_progress')->count() }}</p>
                    <p class="text-xs text-gray-400 mt-1">Currently working</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                {{-- Driver Reported Issues --}}
                <div class="bg-white rounded-lg border border-gray-200">
                    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                        <h3 class="font-semibold text-gray-800">Driver Reported Issues</h3>
                        <a href="{{ route('mechanic.jobs.index') }}?status=" class="text-sm text-blue-600 hover:underline">View all →</a>
                    </div>
                    <ul class="divide-y divide-gray-100">
                        @forelse($activeJobs->where('job_type', 'report') as $job)
                            <li class="px-5 py-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2">
                                            <p class="text-sm font-semibold text-gray-800">
                                                {{ $job->vehicle->plate_number }} — {{ ucfirst($job->report->issue_type ?? 'Issue') }}
                                            </p>
                                            @php $pc = ['low'=>'text-gray-500','normal'=>'text-blue-600','high'=>'text-red-600'][$job->priority] ?? ''; @endphp
                                            <span class="text-xs font-semibold {{ $pc }}">{{ ucfirst($job->priority) }} Priority</span>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-0.5">
                                            {{ $job->vehicle->model }}
                                            · {{ $job->vehicle->driver?->full_name ?? 'Unassigned' }}
                                        </p>
                                        <p class="text-xs text-gray-400 mt-0.5">
                                            Scheduled: {{ $job->scheduled_at->format('M d') }}
                                        </p>
                                        @if($job->report?->description)
                                            <p class="text-xs text-gray-500 mt-1 italic">"{{ $job->report->description }}"</p>
                                        @endif
                                    </div>
                                    <div class="flex flex-col items-end gap-2 shrink-0">
                                        @php $sc = ['pending'=>'bg-yellow-100 text-yellow-700','in_shop'=>'bg-blue-100 text-blue-700','in_progress'=>'bg-orange-100 text-orange-700'][$job->status] ?? ''; @endphp
                                        <span class="text-xs px-2 py-1 rounded font-semibold {{ $sc }}">
                                            {{ ucwords(str_replace('_', ' ', $job->status)) }}
                                        </span>
                                        <a href="{{ route('mechanic.jobs.show', $job) }}"
                                           class="text-xs px-3 py-1 bg-gray-100 text-gray-700 rounded hover:bg-gray-200 font-medium">
                                            {{ $job->status === 'in_progress' ? 'Mark Done' : ($job->status === 'pending' ? 'Car Arrived' : 'Start Work') }} →
                                        </a>
                                    </div>
                                </div>
                            </li>
                        @empty
                            <li class="px-5 py-4 text-sm text-gray-400">No driver report jobs assigned.</li>
                        @endforelse
                    </ul>
                </div>

                {{-- PMS Scheduled Jobs --}}
                <div class="bg-white rounded-lg border border-gray-200">
                    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                        <h3 class="font-semibold text-gray-800">PMS Scheduled Jobs</h3>
                        <a href="{{ route('mechanic.jobs.index') }}" class="text-sm text-blue-600 hover:underline">View all →</a>
                    </div>
                    <ul class="divide-y divide-gray-100">
                        @forelse($activeJobs->where('job_type', 'maintenance_schedule') as $job)
                            <li class="px-5 py-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-semibold text-gray-800">
                                            {{ $job->vehicle->plate_number }} — {{ $job->maintenanceSchedule->maintenanceType->name ?? 'PMS' }}
                                        </p>
                                        <p class="text-xs text-gray-500 mt-0.5">
                                            {{ $job->vehicle->model }}
                                            · {{ $job->vehicle->driver?->full_name ?? 'Unassigned' }}
                                        </p>
                                        <p class="text-xs text-gray-400 mt-0.5">
                                            Scheduled: {{ $job->scheduled_at->format('M d') }}
                                            @if($job->maintenanceSchedule)
                                                · Current: {{ number_format($job->vehicle->current_odometer_km) }} km
                                            @endif
                                        </p>
                                    </div>
                                    <div class="flex flex-col items-end gap-2 shrink-0">
                                        @php $sc = ['pending'=>'bg-yellow-100 text-yellow-700','in_shop'=>'bg-blue-100 text-blue-700','in_progress'=>'bg-orange-100 text-orange-700'][$job->status] ?? ''; @endphp
                                        <span class="text-xs px-2 py-1 rounded font-semibold {{ $sc }}">
                                            {{ ucwords(str_replace('_', ' ', $job->status)) }}
                                        </span>
                                        <a href="{{ route('mechanic.jobs.show', $job) }}"
                                           class="text-xs px-3 py-1 bg-gray-100 text-gray-700 rounded hover:bg-gray-200 font-medium">
                                            View →
                                        </a>
                                    </div>
                                </div>
                            </li>
                        @empty
                            <li class="px-5 py-4 text-sm text-gray-400">No PMS jobs assigned.</li>
                        @endforelse
                    </ul>
                </div>

            </div>

            {{-- Done Today --}}
            @php
                $doneToday = auth()->user()->assignedJobs()
                    ->with(['vehicle', 'report', 'maintenanceSchedule.maintenanceType'])
                    ->where('status', 'done')
                    ->whereDate('completed_at', today())
                    ->get();
            @endphp
            @if($doneToday->isNotEmpty())
                <div class="bg-white rounded-lg border border-gray-200">
                    <div class="px-5 py-4 border-b border-gray-100">
                        <h3 class="font-semibold text-gray-800">Done Today</h3>
                    </div>
                    <ul class="divide-y divide-gray-100">
                        @foreach($doneToday as $job)
                            <li class="px-5 py-3 flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-800">
                                        {{ $job->vehicle->plate_number }}
                                        —
                                        @if($job->job_type === 'report')
                                            {{ ucfirst($job->report->issue_type ?? 'Issue') }}
                                        @else
                                            {{ $job->maintenanceSchedule->maintenanceType->name ?? 'PMS' }}
                                        @endif
                                    </p>
                                    <p class="text-xs text-gray-400 mt-0.5">
                                        {{ $job->vehicle->model }}
                                        · {{ $job->vehicle->driver?->full_name ?? '—' }}
                                    </p>
                                </div>
                                <span class="text-xs px-2 py-1 rounded font-semibold bg-green-100 text-green-700">Done</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>