<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit Maintenance Type
            </h2>

            <a href="{{ route('admin.maintenance-types.index') }}"
            class="text-sm text-gray-600 hover:underline">
                ← Back
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            {{-- Errors --}}
            @if ($errors->any())
                <div class="bg-red-50 border border-red-300 text-red-800 px-4 py-3 rounded">
                    <ul class="list-disc pl-5 text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Form --}}
            <div class="bg-white rounded-lg border border-gray-200 p-6">

                <form method="POST" action="{{ route('admin.maintenance-types.update', $maintenanceType) }}"
                    class="space-y-5">

                    @csrf
                    @method('PUT')

                    {{-- Name --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Name</label>
                        <input type="text" name="name" value="{{ old('name', $maintenanceType->name) }}"
                            class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500"
                            required>
                    </div>

                    {{-- Default Interval --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            Default Interval (KM)
                        </label>
                        <input type="number" name="default_interval_km" value="{{ old('default_interval_km', $maintenanceType->default_interval_km) }}"
                            class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500"
                            required>
                    </div>

                    {{-- Description --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea name="description" rows="4"
                            class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">{{ old('description', $maintenanceType->description) }}</textarea>
                    </div>

                    {{-- Buttons --}}
                    <div class="flex items-center gap-3 pt-2">
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-md hover:bg-blue-700">
                            Update
                        </button>

                        <a href="{{ route('admin.maintenance-types.index') }}" class="px-4 py-2 text-sm text-gray-600 hover:underline">
                            Cancel
                        </a>
                    </div>

                </form>

            </div>
        </div>
    </div>
</x-app-layout>