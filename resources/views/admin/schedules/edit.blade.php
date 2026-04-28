<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit PMS Schedule</h2>
            <a href="{{ route('admin.schedules.index') }}" class="text-sm text-gray-500 hover:underline">← Back to Schedule List</a>
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

            {{-- Vehicle + Type header --}}
            <div class="bg-gray-50 rounded-md px-4 py-3 border border-gray-200">
                <p class="text-sm font-semibold text-gray-700">
                    {{ $schedule->vehicle->plate_number }} · {{ $schedule->vehicle->model }}
                </p>
                <p class="text-xs text-gray-500 mt-0.5">{{ $schedule->maintenanceType->name }}</p>
            </div>

            {{-- UPDATE FORM --}}
            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <form method="POST"
                      action="{{ route('admin.schedules.update', $schedule) }}"
                      class="space-y-5">
                    @csrf
                    @method('PUT')

                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Maintenance Details</p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="last_service_odo" value="Last Service ODO (km) *" />
                            <x-text-input id="last_service_odo" name="last_service_odo" type="number"
                                          class="mt-1 block w-full"
                                          :value="old('last_service_odo', $schedule->last_service_odo)"
                                          required min="0" />
                            <x-input-error :messages="$errors->get('last_service_odo')" class="mt-1" />
                        </div>
                        <div>
                            <x-input-label for="interval_km" value="Interval (km) *" />
                            <x-text-input id="interval_km" name="interval_km" type="number"
                                          class="mt-1 block w-full"
                                          :value="old('interval_km', $schedule->interval_km)"
                                          required min="1" />
                            <x-input-error :messages="$errors->get('interval_km')" class="mt-1" />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="last_service_date" value="Last Service Date" />
                        <x-text-input id="last_service_date" name="last_service_date" type="date"
                                      class="mt-1 block w-full"
                                      :value="old('last_service_date', $schedule->last_service_date?->format('Y-m-d'))" />
                    </div>

                    {{-- Computed preview --}}
                    <div class="bg-blue-50 border border-blue-100 rounded-md px-4 py-3 text-sm text-blue-800">
                        <p class="font-medium">Next Due ODO (computed)</p>
                        <p class="mt-0.5 text-blue-600 font-bold" id="next-due-preview">
                            {{ number_format($schedule->next_due_odo) }} km
                        </p>
                        <p class="text-xs text-blue-500 mt-1">Automatically recalculated from Last Service ODO + Interval.</p>
                    </div>

                    <div>
                        <x-input-label for="notes" value="Notes (optional)" />
                        <textarea id="notes" name="notes" rows="3"
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm"
                                  placeholder="Any additional notes...">{{ old('notes', $schedule->notes) }}</textarea>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2 border-t border-gray-100">
                        <a href="{{ route('admin.schedules.index') }}"
                           class="px-4 py-2 text-sm text-gray-600 border border-gray-300 rounded-md hover:bg-gray-50">
                            Cancel
                        </a>
                        <x-primary-button>Save Changes</x-primary-button>
                    </div>

                </form>
            </div>

            {{-- DELETE FORM — completely outside the update form --}}
            <form method="POST"
                  action="{{ route('admin.schedules.destroy', $schedule) }}"
                  onsubmit="return confirm('Delete this schedule? This cannot be undone.')">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="px-4 py-2 text-sm font-semibold bg-red-100 text-red-700 rounded-md hover:bg-red-200">
                    Delete Schedule
                </button>
            </form>

        </div>
    </div>

    @push('scripts')
    <script>
        const odoInput      = document.getElementById('last_service_odo');
        const intervalInput = document.getElementById('interval_km');
        const preview       = document.getElementById('next-due-preview');

        function updatePreview() {
            const odo      = parseInt(odoInput.value) || 0;
            const interval = parseInt(intervalInput.value) || 0;
            preview.textContent = (odo + interval).toLocaleString() + ' km';
        }

        odoInput.addEventListener('input', updatePreview);
        intervalInput.addEventListener('input', updatePreview);
    </script>
    @endpush
</x-app-layout>