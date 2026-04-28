<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Job Details</h2>
            <a href="{{ route('mechanic.jobs.index') }}" class="text-sm text-gray-500 hover:underline">← Back to My Jobs</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            @if(session('success'))
                <div class="bg-green-50 border border-green-300 text-green-800 px-4 py-3 rounded">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="bg-red-50 border border-red-300 text-red-800 px-4 py-3 rounded">{{ session('error') }}</div>
            @endif

            {{-- Job Detail Card --}}
            <div class="bg-white rounded-lg border border-gray-200 p-6 space-y-4">

                <div class="flex items-center justify-between">
                    <h3 class="font-semibold text-gray-800 text-lg">
                        {{ $job->vehicle->plate_number }} — {{ $job->vehicle->model }}
                    </h3>
                    @php $sc = ['pending'=>'bg-yellow-100 text-yellow-700','in_shop'=>'bg-blue-100 text-blue-700','in_progress'=>'bg-orange-100 text-orange-700','done'=>'bg-green-100 text-green-700'][$job->status] ?? ''; @endphp
                    <span class="px-3 py-1 rounded text-sm font-semibold {{ $sc }}">
                        {{ ucwords(str_replace('_', ' ', $job->status)) }}
                    </span>
                </div>

                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide">Job Type</p>
                        <p class="font-medium text-gray-800 mt-0.5">
                            {{ $job->job_type === 'report' ? 'Driver Report' : 'PMS Schedule' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide">Priority</p>
                        @php $pc = ['low'=>'text-gray-600','normal'=>'text-blue-600','high'=>'text-red-600'][$job->priority] ?? ''; @endphp
                        <p class="font-medium mt-0.5 {{ $pc }}">{{ ucfirst($job->priority) }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide">Scheduled Date</p>
                        <p class="font-medium text-gray-800 mt-0.5">{{ $job->scheduled_at->format('M d, Y') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide">Driver</p>
                        <p class="font-medium text-gray-800 mt-0.5">{{ $job->vehicle->driver?->full_name ?? 'Unassigned' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide">Current Odometer</p>
                        <p class="font-medium text-gray-800 mt-0.5">{{ number_format($job->vehicle->current_odometer_km) }} km</p>
                    </div>
                    @if($job->completed_at)
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide">Completed</p>
                            <p class="font-medium text-gray-800 mt-0.5">{{ $job->completed_at->format('M d, Y · g:i A') }}</p>
                        </div>
                    @endif
                </div>

                {{-- Source details --}}
                @if($job->job_type === 'report' && $job->report)
                    <div class="bg-orange-50 border border-orange-200 rounded p-3 space-y-1">
                        <p class="text-xs text-orange-500 uppercase tracking-wide font-semibold">Driver Report</p>
                        <p class="text-sm font-medium text-gray-800">{{ ucfirst($job->report->issue_type) }}</p>
                        @if($job->report->description)
                            <p class="text-sm text-gray-600 italic">"{{ $job->report->description }}"</p>
                        @endif
                        <p class="text-xs text-gray-400">
                            Reported by {{ $job->report->reporter->full_name }}
                            · {{ $job->report->created_at->format('M d, Y') }}
                        </p>
                    </div>
                @elseif($job->job_type === 'maintenance_schedule' && $job->maintenanceSchedule)
                    <div class="bg-blue-50 border border-blue-200 rounded p-3 space-y-1">
                        <p class="text-xs text-blue-500 uppercase tracking-wide font-semibold">PMS Schedule</p>
                        <p class="text-sm font-medium text-gray-800">{{ $job->maintenanceSchedule->maintenanceType->name }}</p>
                        <p class="text-xs text-gray-500">
                            Last service: {{ number_format($job->maintenanceSchedule->last_service_odo) }} km
                            · Next due: {{ number_format($job->maintenanceSchedule->next_due_odo) }} km
                            · Interval: every {{ number_format($job->maintenanceSchedule->interval_km) }} km
                        </p>
                    </div>
                @endif

                {{-- Existing mechanic notes if done --}}
                @if($job->mechanic_notes)
                    <div class="bg-gray-50 border border-gray-200 rounded p-3">
                        <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Your Notes</p>
                        <p class="text-sm text-gray-700">{{ $job->mechanic_notes }}</p>
                    </div>
                @endif

            </div>

            {{-- Status Update Form — only if not done --}}
            @if(!$job->isDone())
                @php
                    $nextLabel = [
                        'pending'     => 'Car Arrived → Mark as In Shop',
                        'in_shop'     => 'Start Work → Mark as In Progress',
                        'in_progress' => 'Finish Job → Mark as Done',
                    ][$job->status] ?? '';

                    $btnColor = [
                        'pending'     => 'bg-blue-600 hover:bg-blue-700',
                        'in_shop'     => 'bg-orange-500 hover:bg-orange-600',
                        'in_progress' => 'bg-green-600 hover:bg-green-700',
                    ][$job->status] ?? 'bg-gray-600';
                @endphp

                <div class="bg-white rounded-lg border border-gray-200 p-6 space-y-4">
                    <h3 class="font-semibold text-gray-800">Update Job Status</h3>

                    <form method="POST" action="{{ route('mechanic.jobs.update-status', $job) }}"
                          onsubmit="return confirm('Update job status?')">
                        @csrf @method('PATCH')

                        <div class="space-y-4">
                            <div>
                                <x-input-label for="mechanic_notes" value="Notes / Findings (optional)" />
                                <textarea id="mechanic_notes" name="mechanic_notes" rows="3"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Describe what was found or done...">{{ old('mechanic_notes', $job->mechanic_notes) }}</textarea>
                            </div>

                            {{-- Only ask for odometer when marking done --}}
                            @if($job->status === 'in_progress')
                                <div>
                                    <x-input-label for="completion_odometer" value="Completion Odometer (km) — updates vehicle record" />
                                    <x-text-input id="completion_odometer" name="completion_odometer" type="number"
                                                  class="mt-1 block w-full"
                                                  :value="old('completion_odometer', $job->vehicle->current_odometer_km)"
                                                  min="{{ $job->vehicle->current_odometer_km }}" />
                                    <p class="text-xs text-gray-400 mt-1">
                                        Current: {{ number_format($job->vehicle->current_odometer_km) }} km.
                                        Enter the odometer at time of completion to update the vehicle and reset the PMS schedule.
                                    </p>
                                    <x-input-error :messages="$errors->get('completion_odometer')" class="mt-1" />
                                </div>
                            @endif

                            <button type="submit"
                                    class="w-full py-2.5 text-white text-sm font-semibold rounded-md {{ $btnColor }}">
                                {{ $nextLabel }}
                            </button>
                        </div>
                    </form>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>