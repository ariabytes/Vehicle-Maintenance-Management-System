<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Assign Job to Mechanic</h2>
            <a href="{{ route('admin.jobs.index') }}" class="text-sm text-gray-500 hover:underline">← Back to Jobs</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            @if($errors->any())
                <div class="bg-red-50 border border-red-300 text-red-800 px-4 py-3 rounded">
                    <ul class="list-disc list-inside text-sm space-y-1">
                        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                    </ul>
                </div>
            @endif

            {{-- Source info card --}}
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-sm">
                @if($type === 'report')
                    <p class="font-semibold text-blue-800 mb-2">Driver Report</p>
                    <div class="grid grid-cols-2 gap-2 text-blue-700">
                        <div><span class="text-xs text-blue-400 uppercase">Vehicle</span><br>{{ $source->vehicle->plate_number }} — {{ $source->vehicle->model }}</div>
                        <div><span class="text-xs text-blue-400 uppercase">Driver</span><br>{{ $source->reporter->full_name }}</div>
                        <div><span class="text-xs text-blue-400 uppercase">Issue Type</span><br>{{ ucfirst($source->issue_type) }}</div>
                        <div><span class="text-xs text-blue-400 uppercase">Reported</span><br>{{ $source->created_at->format('M d, Y') }}</div>
                    </div>
                    @if($source->description)
                        <p class="mt-2 italic text-blue-700">"{{ $source->description }}"</p>
                    @endif
                @else
                    <p class="font-semibold text-blue-800 mb-2">PMS Schedule</p>
                    <div class="grid grid-cols-2 gap-2 text-blue-700">
                        <div><span class="text-xs text-blue-400 uppercase">Vehicle</span><br>{{ $source->vehicle->plate_number }} — {{ $source->vehicle->model }}</div>
                        <div><span class="text-xs text-blue-400 uppercase">Maintenance Type</span><br>{{ $source->maintenanceType->name }}</div>
                        <div><span class="text-xs text-blue-400 uppercase">Current Odometer</span><br>{{ number_format($source->vehicle->current_odometer_km) }} km</div>
                        <div><span class="text-xs text-blue-400 uppercase">Next Due</span><br>{{ number_format($source->next_due_odo) }} km</div>
                    </div>
                @endif
            </div>

            {{-- Job form --}}
            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <form method="POST" action="{{ route('admin.jobs.store') }}" class="space-y-5">
                    @csrf

                    {{-- Hidden fields carrying source data --}}
                    <input type="hidden" name="vehicle_id" value="{{ $source->vehicle->id }}">
                    <input type="hidden" name="job_type"   value="{{ $type }}">
                    @if($type === 'report')
                        <input type="hidden" name="report_id" value="{{ $source->id }}">
                    @else
                        <input type="hidden" name="maintenance_sched_id" value="{{ $source->id }}">
                    @endif

                    <div>
                        <x-input-label for="mechanic_id" value="Assign Mechanic *" />
                        <select id="mechanic_id" name="mechanic_id" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="">Select mechanic...</option>
                            @foreach($mechanics as $m)
                                <option value="{{ $m->id }}" {{ old('mechanic_id') == $m->id ? 'selected' : '' }}>{{ $m->full_name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('mechanic_id')" class="mt-1" />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="priority" value="Priority *" />
                            <select id="priority" name="priority" required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                                <option value="normal" {{ old('priority') === 'normal' ? 'selected' : '' }}>Normal</option>
                                <option value="high"   {{ old('priority') === 'high'   ? 'selected' : '' }}>High</option>
                                <option value="low"    {{ old('priority') === 'low'    ? 'selected' : '' }}>Low</option>
                            </select>
                        </div>
                        <div>
                            <x-input-label for="scheduled_at" value="Scheduled Date *" />
                            <x-text-input id="scheduled_at" name="scheduled_at" type="date" class="mt-1 block w-full"
                                :value="old('scheduled_at', now()->toDateString())" required />
                            <x-input-error :messages="$errors->get('scheduled_at')" class="mt-1" />
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2 border-t border-gray-100">
                        <a href="{{ route('admin.jobs.index') }}"
                            class="px-4 py-2 text-sm text-gray-600 border border-gray-300 rounded-md hover:bg-gray-50">Cancel</a>
                        <x-primary-button>Confirm Assignment</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>