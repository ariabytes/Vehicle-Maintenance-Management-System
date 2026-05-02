<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Report</h2>
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

                <form method="POST" action="{{ route('driver.reports.update', $report) }}" class="space-y-5">
                    @csrf @method('PUT')

                    <div>
                        <x-input-label value="Vehicle" />
                        <x-text-input type="text" class="mt-1 block w-full"
                                      value="{{ $report->vehicle->plate_number }} — {{ $report->vehicle->model }}"
                                      disabled />
                    </div>

                    <div>
                        <x-input-label for="issue_type" value="Issue Type *" />
                        <select id="issue_type" name="issue_type" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                            @foreach(['engine','brakes','tires','electrical','cooling','others'] as $type)
                                <option value="{{ $type }}" {{ old('issue_type', $report->issue_type) === $type ? 'selected' : '' }}>
                                    {{ ucfirst($type) }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('issue_type')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="description" value="Description (optional)" />
                        <textarea id="description" name="description" rows="4"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500">{{ old('description', $report->description) }}</textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-1" />
                    </div>

                    <div class="flex items-center justify-between pt-2 border-t border-gray-100">
                        <button type="submit" form="cancel-form"
                                onclick="return confirm('Cancel this report?')"
                                class="px-4 py-2 text-sm font-semibold bg-red-100 text-red-700 rounded-md hover:bg-red-200">
                            Cancel Report
                        </button>

                        <div class="flex gap-3">
                            <a href="{{ route('driver.reports.index') }}"
                               class="px-4 py-2 text-sm text-gray-600 border border-gray-300 rounded-md hover:bg-gray-50">
                                Back
                            </a>
                            <x-primary-button>Save Changes</x-primary-button>
                        </div>
                    </div>
                </form>
                <form id="cancel-form" method="POST" action="{{ route('driver.reports.destroy', $report) }}">
                    @csrf @method('DELETE')
                </form>
            </div>
        </div>
    </div>
</x-app-layout>