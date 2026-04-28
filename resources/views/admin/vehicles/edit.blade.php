<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit Vehicle — {{ $vehicle->plate_number }}
            </h2>
            <a href="{{ route('admin.vehicles.index') }}" class="text-sm text-gray-500 hover:underline">← Back to Vehicles</a>
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

                <form method="POST" action="{{ route('admin.vehicles.update', $vehicle) }}"
                      enctype="multipart/form-data" class="space-y-5">
                    @csrf @method('PUT')

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="plate_number" value="Plate Number" />
                            <x-text-input id="plate_number" name="plate_number" type="text" class="mt-1 block w-full"
                                          :value="$vehicle->plate_number" disabled />
                            {{-- Hidden field so the value still passes validation --}}
                            <input type="hidden" name="plate_number" value="{{ $vehicle->plate_number }}">
                            <p class="mt-1 text-xs text-gray-400">Plate number cannot be changed after creation.</p>
                        </div>
                        <div>
                            <x-input-label for="model" value="Vehicle Name / Model *" />
                            <x-text-input id="model" name="model" type="text" class="mt-1 block w-full"
                                          :value="old('model', $vehicle->model)" required />
                            <x-input-error :messages="$errors->get('model')" class="mt-1" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <x-input-label for="vehicle_type" value="Vehicle Type" />
                            <select id="vehicle_type" name="vehicle_type"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                                @foreach(['van','truck','car','suv','motorcycle','others'] as $type)
                                    <option value="{{ $type }}" {{ old('vehicle_type', $vehicle->vehicle_type) === $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <x-input-label for="year" value="Year *" />
                            <x-text-input id="year" name="year" type="number" class="mt-1 block w-full"
                                          :value="old('year', $vehicle->year)" required />
                        </div>
                        <div>
                            <x-input-label for="color" value="Color" />
                            <x-text-input id="color" name="color" type="text" class="mt-1 block w-full"
                                          :value="old('color', $vehicle->color)" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="current_odometer_km" value="Current Odometer (km) *" />
                            <x-text-input id="current_odometer_km" name="current_odometer_km" type="number"
                                          class="mt-1 block w-full"
                                          :value="old('current_odometer_km', $vehicle->current_odometer_km)" required min="0" />
                            <x-input-error :messages="$errors->get('current_odometer_km')" class="mt-1" />
                        </div>
                        <div>
                            <x-input-label for="driver_id" value="Assigned Driver" />
                            <select id="driver_id" name="driver_id"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                                <option value="">Unassigned</option>
                                @foreach($drivers as $driver)
                                    <option value="{{ $driver->id }}" {{ old('driver_id', $vehicle->driver_id) == $driver->id ? 'selected' : '' }}>
                                        {{ $driver->full_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <x-input-label for="status" value="Status *" />
                        <select id="status" name="status"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="active"  {{ old('status', $vehicle->status) === 'active'  ? 'selected' : '' }}>Active</option>
                            <option value="in_shop" {{ old('status', $vehicle->status) === 'in_shop' ? 'selected' : '' }}>In Shop</option>
                        </select>
                    </div>

                    <div>
                        <x-input-label for="photo" value="Replace Photo (optional)" />
                        @if($vehicle->photo)
                            <img src="{{ asset('storage/' . $vehicle->photo) }}" alt="Vehicle photo"
                                 class="mt-1 h-24 rounded border border-gray-200 object-cover">
                        @endif
                        <input id="photo" name="photo" type="file" accept="image/*"
                               class="mt-2 block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    </div>

                    <div class="flex items-center justify-between pt-2 border-t border-gray-100">
                        <form method="POST" action="{{ route('admin.vehicles.archive', $vehicle) }}"
                              onsubmit="return confirm('Archive this vehicle?')">
                            @csrf @method('PATCH')
                            <button type="submit"
                                class="px-4 py-2 text-sm font-semibold bg-red-100 text-red-700 rounded-md hover:bg-red-200">
                                Archive Vehicle
                            </button>
                        </form>

                        <div class="flex gap-3">
                            <a href="{{ route('admin.vehicles.index') }}"
                               class="px-4 py-2 text-sm text-gray-600 border border-gray-300 rounded-md hover:bg-gray-50">Cancel</a>
                            <x-primary-button>Save Changes</x-primary-button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>