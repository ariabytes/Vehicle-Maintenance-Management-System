<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Job Details</h2>
            <a href="{{ route('admin.jobs.index') }}" class="text-sm text-gray-500 hover:underline">← Back to Jobs</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            @if(session('success'))
                <div class="bg-green-50 border border-green-300 text-green-800 px-4 py-3 rounded">{{ session('success') }}</div>
            @endif

            <div class="bg-white rounded-lg border border-gray-200 p-6 space-y-4">

                <div class="flex items-center justify-between">
                    <h3 class="font-semibold text-gray-800 text-lg">
                        {{ $job->vehicle->plate_number }} — {{ $job->vehicle->model }}
                    </h3>
                    @php $sc = ['pending'=>'bg-yellow-100 text-yellow-700','in_shop'=>'bg-blue-100 text-blue-700','in_progress'=>'bg-orange-100 text-orange-700','done'=>'bg-green-100 text-green-700'][$job->status] ?? ''; @endphp
                    <span class="px-3 py-1 rounded text-sm font-semibold {{ $sc }}">{{ ucwords(str_replace('_',' ',$job->status)) }}</span>
                </div>

                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide">Job Type</p>
                        <p class="font-medium text-gray-800 mt-0.5">{{ $job->job_type === 'report' ? 'Driver Report' : 'PMS Schedule' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide">Mechanic</p>
                        <p class="font-medium text-gray-800 mt-0.5">{{ $job->mechanic->full_name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide">Scheduled Date</p>
                        <p class="font-medium text-gray-800 mt-0.5">{{ $job->scheduled_at->format('M d, Y') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide">Priority</p>
                        <p class="font-medium text-gray-800 mt-0.5">{{ ucfirst($job->priority) }}</p>
                    </div>
                    @if($job->completed_at)
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide">Completed</p>
                            <p class="font-medium text-gray-800 mt-0.5">{{ $job->completed_at->format('M d, Y · g:i A') }}</p>
                        </div>
                    @endif
                    @if($job->report)
                        <div class="col-span-2">
                            <p class="text-xs text-gray-400 uppercase tracking-wide">From Report</p>
                            <p class="font-medium text-gray-800 mt-0.5">{{ ucfirst($job->report->issue_type) }} — reported by {{ $job->report->reporter->full_name }}</p>
                            @if($job->report->description)
                                <p class="text-xs text-gray-500 mt-0.5 italic">"{{ $job->report->description }}"</p>
                            @endif
                        </div>
                    @elseif($job->maintenanceSchedule)
                        <div class="col-span-2">
                            <p class="text-xs text-gray-400 uppercase tracking-wide">From PMS Schedule</p>
                            <p class="font-medium text-gray-800 mt-0.5">{{ $job->maintenanceSchedule->maintenanceType->name }}</p>
                        </div>
                    @endif
                    @if($job->mechanic_notes)
                        <div class="col-span-2 bg-gray-50 border border-gray-200 rounded p-3">
                            <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Mechanic Notes</p>
                            <p class="text-sm text-gray-700">{{ $job->mechanic_notes }}</p>
                        </div>
                    @endif
                </div>

                {{-- Admin can reassign mechanic or reschedule if not done --}}
                @if(!$job->isDone())
                    <form method="POST" action="{{ route('admin.jobs.update', $job) }}" class="border-t border-gray-100 pt-4 space-y-4">
                        @csrf @method('PUT')
                        <p class="text-sm font-semibold text-gray-700">Update Assignment</p>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <x-input-label for="mechanic_id" value="Mechanic" />
                                <select id="mechanic_id" name="mechanic_id"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                                @foreach($mechanics as $m)
                                    <option value="{{ $m->id }}" {{ $job->mechanic_id == $m->id ? 'selected' : '' }}>
                                        {{ $m->full_name }}
                                    </option>
                                @endforeach
                            </select>
                            </div>
                            <div>
                                <x-input-label for="priority" value="Priority" />
                                <select id="priority" name="priority"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                                    <option value="normal" {{ $job->priority === 'normal' ? 'selected' : '' }}>Normal</option>
                                    <option value="high"   {{ $job->priority === 'high'   ? 'selected' : '' }}>High</option>
                                    <option value="low"    {{ $job->priority === 'low'    ? 'selected' : '' }}>Low</option>
                                </select>
                            </div>
                            <div>
                                <x-input-label for="scheduled_at" value="Scheduled Date" />
                                <x-text-input id="scheduled_at" name="scheduled_at" type="date" class="mt-1 block w-full"
                                    :value="$job->scheduled_at->toDateString()" />
                            </div>
                        </div>
                        <x-primary-button>Update Job</x-primary-button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>