<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Add PMS Schedule</h2>
            <a href="{{ route('admin.schedules.index') }}" class="text-sm text-gray-500 hover:underline">← Back to Schedule List</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg border border-gray-200 p-6">

                @if($errors->any())
                    <div class="bg-red-50 border border-red-300 text-red-800 px-4 py-3 rounded mb-6">
                        <ul class="list-disc list-inside text-sm space-y-1">
                            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.schedules.store') }}" class="space-y-6">
                    @csrf

                    {{-- Vehicle --}}
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Vehicle</p>
                        <select id="vehicle_id" name="vehicle_id" required
                                class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="">Please select a vehicle...</option>
                            @foreach($vehicles as $vehicle)
                                <option value="{{ $vehicle->id }}"
                                        data-odo="{{ $vehicle->current_odometer_km }}"
                                        {{ old('vehicle_id', $selectedVehicle?->id) == $vehicle->id ? 'selected' : '' }}>
                                    {{ $vehicle->plate_number }} — {{ $vehicle->model }}
                                    ({{ number_format($vehicle->current_odometer_km) }} km)
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('vehicle_id')" class="mt-1" />

                        {{-- Current ODO display --}}
                        <p id="current-odo-display" class="text-xs text-gray-400 mt-1 hidden">
                            Current odometer: <span id="current-odo-value" class="font-semibold text-gray-600"></span>
                        </p>
                    </div>

                    {{-- Maintenance Type Rows --}}
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Maintenance Types</p>

                        <div id="schedule-rows" class="space-y-4">
                            {{-- First row (always shown) --}}
                            <div class="schedule-row border border-gray-200 rounded-lg p-4 space-y-4 relative">
                                <button type="button" onclick="removeRow(this)"
                                        class="remove-btn absolute top-3 right-3 text-gray-300 hover:text-red-500 text-lg font-bold leading-none hidden">
                                    ×
                                </button>

                                <div>
                                    <x-input-label value="Maintenance Type *" />
                                    <select name="schedules[0][maintenance_type_id]" required
                                            class="type-select mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                                        <option value="">Select maintenance type...</option>
                                        @foreach($types as $type)
                                            <option value="{{ $type->id }}" data-interval="{{ $type->default_interval_km }}">
                                                {{ $type->name }} (default: {{ number_format($type->default_interval_km) }} km)
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                    <div>
                                        <x-input-label value="Last Service ODO (km) *" />
                                        <input type="number" name="schedules[0][last_service_odo]"
                                               class="last-odo mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm"
                                               min="0" placeholder="e.g. 41000" required />
                                    </div>
                                    <div>
                                        <x-input-label value="Interval (km) *" />
                                        <input type="number" name="schedules[0][interval_km]"
                                               class="interval-km mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm"
                                               min="1" placeholder="Auto-filled" required />
                                    </div>
                                    <div>
                                        <x-input-label value="Next Due ODO" />
                                        <input type="text" readonly
                                               class="next-due mt-1 block w-full border-gray-200 rounded-md bg-gray-50 text-gray-500 text-sm cursor-not-allowed"
                                               placeholder="Auto-calculated" />
                                    </div>
                                </div>

                                <div>
                                    <x-input-label value="Last Service Date" />
                                    <input type="date" name="schedules[0][last_service_date]"
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm" />
                                </div>
                            </div>
                        </div>

                        {{-- Add row button --}}
                        <button type="button" onclick="addRow()"
                                class="mt-4 w-full py-2.5 border-2 border-dashed border-gray-300 rounded-lg text-sm text-gray-500 hover:border-blue-400 hover:text-blue-500 transition font-medium">
                            + Add Another Maintenance Type
                        </button>
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
        let rowCount = 1;

        // ── Vehicle selector ──────────────────────────────────────────────
        document.getElementById('vehicle_id').addEventListener('change', function () {
            const opt = this.options[this.selectedIndex];
            const odo = opt?.getAttribute('data-odo');
            const display = document.getElementById('current-odo-display');
            const value   = document.getElementById('current-odo-value');
            if (odo) {
                value.textContent = Number(odo).toLocaleString() + ' km';
                display.classList.remove('hidden');
            } else {
                display.classList.add('hidden');
            }
        });

        // ── Recalculate next due for a row ────────────────────────────────
        function recalcRow(row) {
            const lastOdo  = parseInt(row.querySelector('.last-odo').value);
            const interval = parseInt(row.querySelector('.interval-km').value);
            const nextDue  = row.querySelector('.next-due');
            if (!isNaN(lastOdo) && !isNaN(interval) && lastOdo >= 0 && interval > 0) {
                nextDue.value = (lastOdo + interval).toLocaleString() + ' km';
            } else {
                nextDue.value = '';
            }
        }

        // ── Attach events to a row ────────────────────────────────────────
        function attachRowEvents(row) {
            row.querySelector('.type-select').addEventListener('change', function () {
                const interval = this.options[this.selectedIndex]?.getAttribute('data-interval');
                if (interval) {
                    row.querySelector('.interval-km').value = interval;
                }
                recalcRow(row);
            });
            row.querySelector('.last-odo').addEventListener('input', () => recalcRow(row));
            row.querySelector('.interval-km').addEventListener('input', () => recalcRow(row));
        }

        // Attach to first row
        attachRowEvents(document.querySelector('.schedule-row'));

        // ── Add row ───────────────────────────────────────────────────────
        function addRow() {
            const index = rowCount++;
            const container = document.getElementById('schedule-rows');

            const div = document.createElement('div');
            div.className = 'schedule-row border border-gray-200 rounded-lg p-4 space-y-4 relative';
            div.innerHTML = `
                <button type="button" onclick="removeRow(this)"
                        class="remove-btn absolute top-3 right-3 text-gray-300 hover:text-red-500 text-lg font-bold leading-none">×</button>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Maintenance Type *</label>
                    <select name="schedules[${index}][maintenance_type_id]" required
                            class="type-select mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                        <option value="">Select maintenance type...</option>
                        @foreach($types as $type)
                        <option value="{{ $type->id }}" data-interval="{{ $type->default_interval_km }}">
                            {{ $type->name }} (default: {{ number_format($type->default_interval_km) }} km)
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Last Service ODO (km) *</label>
                        <input type="number" name="schedules[${index}][last_service_odo]"
                               class="last-odo mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm"
                               min="0" placeholder="e.g. 41000" required />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Interval (km) *</label>
                        <input type="number" name="schedules[${index}][interval_km]"
                               class="interval-km mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm"
                               min="1" placeholder="Auto-filled" required />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Next Due ODO</label>
                        <input type="text" readonly
                               class="next-due mt-1 block w-full border-gray-200 rounded-md bg-gray-50 text-gray-500 text-sm cursor-not-allowed"
                               placeholder="Auto-calculated" />
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Last Service Date</label>
                    <input type="date" name="schedules[${index}][last_service_date]"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm" />
                </div>
            `;

            container.appendChild(div);
            attachRowEvents(div);
            updateRemoveButtons();
        }

        // ── Remove row ────────────────────────────────────────────────────
        function removeRow(btn) {
            const row = btn.closest('.schedule-row');
            row.remove();
            updateRemoveButtons();
        }

        // Hide remove button if only one row left
        function updateRemoveButtons() {
            const rows = document.querySelectorAll('.schedule-row');
            rows.forEach(row => {
                const btn = row.querySelector('.remove-btn');
                if (btn) btn.classList.toggle('hidden', rows.length === 1);
            });
        }

        // Restore on validation failure
        document.addEventListener('DOMContentLoaded', function () {
            const vehicleSel = document.getElementById('vehicle_id');
            if (vehicleSel.value) vehicleSel.dispatchEvent(new Event('change'));
            document.querySelectorAll('.schedule-row').forEach(row => {
                const typeSel = row.querySelector('.type-select');
                if (typeSel?.value) typeSel.dispatchEvent(new Event('change'));
                recalcRow(row);
            });
        });
    </script>
    @endpush
</x-app-layout>