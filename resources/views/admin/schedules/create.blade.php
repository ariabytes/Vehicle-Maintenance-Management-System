<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Add PMS Schedule</h2>
            <a href="{{ route('admin.schedules.index') }}" class="text-sm text-gray-500 hover:underline">← Back to Schedule List</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg border border-gray-200 p-6">

                @if($errors->any())
                    <div class="bg-red-50 border border-red-300 text-red-800 px-4 py-3 rounded mb-6">
                        <ul class="list-disc list-inside text-sm space-y-1">
                            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.schedules.store') }}" class="space-y-5">
                    @csrf

                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Vehicle Details</p>

                    {{-- Vehicle --}}
                    <div>
                        <x-input-label for="vehicle_id" value="Vehicle *" />
                        <select id="vehicle_id" name="vehicle_id" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="">Please select a vehicle...</option>
                            @foreach($vehicles as $vehicle)
                                <option value="{{ $vehicle->id }}"
                                        data-odo="{{ $vehicle->current_odometer_km }}"
                                        {{ old('vehicle_id', $selectedVehicle?->id) == $vehicle->id ? 'selected' : '' }}>
                                    {{ $vehicle->plate_number }} — {{ $vehicle->model }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('vehicle_id')" class="mt-1" />
                    </div>

                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider pt-4">Maintenance Details</p>

                    {{-- Maintenance Type --}}
                    <div>
                        <x-input-label for="maintenance_type_id" value="Type *" />
                        <select id="maintenance_type_id" name="maintenance_type_id" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="">Select maintenance type...</option>
                            @foreach($types as $type)
                                <option value="{{ $type->id }}"
                                        data-interval="{{ $type->default_interval_km }}"
                                        {{ old('maintenance_type_id') == $type->id ? 'selected' : '' }}>
                                    {{ $type->name }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('maintenance_type_id')" class="mt-1" />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="last_service_odo" value="Last Service ODO (km) *" />
                            <x-text-input id="last_service_odo" name="last_service_odo" type="number"
                                class="mt-1 block w-full" :value="old('last_service_odo')"
                                required min="0" placeholder="e.g. 41000" />
                            <x-input-error :messages="$errors->get('last_service_odo')" class="mt-1" />
                        </div>
                        <div>
                            <x-input-label for="interval_km" value="Interval (km) *" />
                            <x-text-input id="interval_km" name="interval_km" type="number"
                                class="mt-1 block w-full" :value="old('interval_km')"
                                required min="1" placeholder="Auto-filled from type" />
                            <x-input-error :messages="$errors->get('interval_km')" class="mt-1" />
                        </div>
                    </div>

                    {{-- Current ODO + Next Due ODO preview --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="current_odo_display" value="Current ODO (km)" />
                            <x-text-input id="current_odo_display" type="text"
                                class="mt-1 block w-full bg-gray-50 text-gray-500 cursor-not-allowed"
                                placeholder="Auto-filled from vehicle"
                                readonly />
                        </div>
                        <div>
                            <x-input-label for="next_due_preview" value="Next Due ODO (km)" />
                            <x-text-input id="next_due_preview" type="text"
                                class="mt-1 block w-full bg-gray-50 text-gray-500 cursor-not-allowed"
                                placeholder="Auto-calculated"
                                readonly />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="last_service_date" value="Last Service Date" />
                        <x-text-input id="last_service_date" name="last_service_date" type="date"
                            class="mt-1 block w-full"
                            :value="old('last_service_date')" />
                    </div>

                    <div>
                        <x-input-label for="notes" value="Notes (optional)" />
                        <textarea id="notes" name="notes" rows="3"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm"
                            placeholder="Any additional notes...">{{ old('notes') }}</textarea>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2 border-t border-gray-100">
                        <a href="{{ route('admin.schedules.index') }}"
                            class="px-4 py-2 text-sm text-gray-600 border border-gray-300 rounded-md hover:bg-gray-50">Cancel</a>
                        <x-primary-button>Add Schedule</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const vehicleSelect  = document.getElementById('vehicle_id');
        const typeSelect     = document.getElementById('maintenance_type_id');
        const lastOdoInput   = document.getElementById('last_service_odo');
        const intervalInput  = document.getElementById('interval_km');
        const currentOdoDisp = document.getElementById('current_odo_display');
        const nextDueDisp    = document.getElementById('next_due_preview');

        function fmt(n) {
            return Number.isFinite(n) && n > 0
                ? n.toLocaleString() + ' km'
                : '';
        }

        function recalc() {
            const last     = parseInt(lastOdoInput.value);
            const interval = parseInt(intervalInput.value);
            nextDueDisp.value = (!isNaN(last) && !isNaN(interval))
                ? fmt(last + interval)
                : '';
        }

        vehicleSelect.addEventListener('change', function () {
            const opt = this.options[this.selectedIndex];
            const odo = opt ? opt.getAttribute('data-odo') : null;
            currentOdoDisp.value = odo ? fmt(parseInt(odo)) : '';
            recalc();
        });

        typeSelect.addEventListener('change', function () {
            const opt      = this.options[this.selectedIndex];
            const interval = opt ? opt.getAttribute('data-interval') : null;
            if (interval) {
                intervalInput.value = interval;
            }
            recalc();
        });

        lastOdoInput.addEventListener('input', recalc);
        intervalInput.addEventListener('input', recalc);

        // Restore state on validation failure (old() values already in the selects)
        document.addEventListener('DOMContentLoaded', function () {
            if (vehicleSelect.value) {
                vehicleSelect.dispatchEvent(new Event('change'));
            }
            if (typeSelect.value) {
                typeSelect.dispatchEvent(new Event('change'));
            }
            recalc();
        });
    </script>
    @endpush
</x-app-layout>