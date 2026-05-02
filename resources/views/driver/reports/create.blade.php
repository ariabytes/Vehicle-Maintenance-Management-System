<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Report an Issue</h2>
            <a href="{{ route('driver.reports.index') }}" class="text-sm text-gray-500 hover:underline">← Back to My Reports</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg border border-gray-200 p-6">

                @if($errors->any())
                    <div class="bg-red-50 border border-red-300 text-red-800 px-4 py-3 rounded mb-6">
                        <ul class="list-disc list-inside text-sm space-y-1">
                            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('driver.reports.store') }}" class="space-y-5">
                    @csrf

                    <div>
                        <x-input-label for="vehicle_display" value="Vehicle" />
                        <x-text-input id="vehicle_display" type="text" class="mt-1 block w-full"
                                      value="{{ $vehicles->first()->plate_number }} — {{ $vehicles->first()->model }}"
                                      disabled />
                        <input type="hidden" name="vehicle_id" value="{{ $vehicles->first()->id }}">
                    </div>

                    <div>
                        <x-input-label for="issue_type" value="Issue Type *" />
                        <select id="issue_type" name="issue_type" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="">Select issue type...</option>
                            @foreach(['engine','brakes','tires','electrical','cooling','others'] as $type)
                                <option value="{{ $type }}" {{ old('issue_type') === $type ? 'selected' : '' }}>
                                    {{ ucfirst($type) }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('issue_type')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="description" value="Description (optional)" />
                        <textarea id="description" name="description" rows="4"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Describe the issue in detail...">{{ old('description') }}</textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label value="Current Odometer" />
                        <x-text-input type="text" class="mt-1 block w-full"
                                      value="{{ number_format($vehicles->first()->current_odometer_km) }} km"
                                      disabled />
                    </div>

                    <div class="flex justify-end gap-3 pt-2 border-t border-gray-100">
                        <a href="{{ route('driver.reports.index') }}"
                           class="px-4 py-2 text-sm text-gray-600 border border-gray-300 rounded-md hover:bg-gray-50">
                            Cancel
                        </a>
                        <x-primary-button>Submit Report</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>