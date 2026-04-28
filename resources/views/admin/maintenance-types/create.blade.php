<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Add Maintenance Type
            </h2>

            <a href="{{ route('admin.maintenance-types.index') }}"
            class="text-sm text-gray-500 hover:text-gray-700 hover:underline transition">
                ← Back
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="bg-white shadow-sm border border-gray-200 rounded-xl overflow-hidden">

                <div class="p-6 space-y-6">

                    @if($errors->any())
                        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                            <ul class="list-disc list-inside space-y-1">
                                @foreach($errors->all() as $e)
                                    <li>{{ $e }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST"
                        action="{{ route('admin.maintenance-types.store') }}" class="space-y-6">
                        @csrf

                        <!-- Name -->
                        <div>
                            <x-input-label for="name" value="Name *" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                                    :value="old('name')" required placeholder="e.g. Engine Oil Change" />
                            <x-input-error :messages="$errors->get('name')" class="mt-1" />
                        </div>

                        <!-- Interval -->
                        <div>
                            <x-input-label for="default_interval_km" value="Default Interval (km) *" />
                            <x-text-input id="default_interval_km" name="default_interval_km" type="number" class="mt-1 block w-full"
                                :value="old('default_interval_km')" required placeholder="e.g. 8000" />
                            <x-input-error :messages="$errors->get('default_interval_km')" class="mt-1" />
                        </div>

                        <!-- Description -->
                        <div>
                            <x-input-label for="description" value="Description" />

                            <textarea id="description" name="description" rows="4" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Optional maintenance description...">{{ old('description') }}</textarea>

                            <x-input-error :messages="$errors->get('description')" class="mt-1" />
                        </div>

                        <!-- Buttons -->
                        <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                            <a href="{{ route('admin.maintenance-types.index') }}"
                            class="px-4 py-2 text-sm rounded-md border border-gray-300 text-gray-600 hover:bg-gray-50 transition">
                                Cancel
                            </a>

                            <x-primary-button class="px-5 py-2">
                                Save Maintenance Type
                            </x-primary-button>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>