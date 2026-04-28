<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Maintenance Types
            </h2>
            <a href="{{ route('admin.maintenance-types.create') }}"
               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-md hover:bg-blue-700">
                + Add Type
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            {{-- Alerts --}}
            @if(session('success'))
                <div class="bg-green-50 border border-green-300 text-green-800 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-50 border border-red-300 text-red-800 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Search --}}
            <form method="GET" class="flex gap-3 flex-wrap">
                <input type="text"
                name="search" value="{{ request('search') }}" placeholder="Search maintenance type..."
                class="border border-gray-300 rounded-md px-3 py-2 text-sm w-56 focus:outline-none focus:ring-1 focus:ring-blue-500">

                <button type="submit"
                        class="px-4 py-2 bg-gray-800 text-white text-sm rounded-md hover:bg-gray-700">
                    Search
                </button>

                <a href="{{ route('admin.maintenance-types.index') }}"
                class="px-4 py-2 text-sm text-gray-600 hover:underline self-center">
                    Reset
                </a>
            </form>

            {{-- Table --}}
            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">

                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Name
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Default Interval
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Description
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Used In
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100">
                            @forelse($types as $type)
                                <tr class="hover:bg-gray-50">

                                    <td class="px-4 py-3 font-medium text-gray-800">
                                        {{ $type->name }}
                                    </td>

                                    <td class="px-4 py-3 text-gray-600">
                                        {{ number_format($type->default_interval_km) }} km
                                    </td>

                                    <td class="px-4 py-3 text-gray-500">
                                        {{ $type->description ?? '—' }}
                                    </td>

                                    <td class="px-4 py-3 text-gray-600">
                                        {{ $type->maintenance_schedules_count }}
                                        <span class="text-gray-400">
                                            schedule{{ $type->maintenance_schedules_count !== 1 ? 's' : '' }}
                                        </span>
                                    </td>

                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-2">

                                            <a href="{{ route('admin.maintenance-types.edit', $type) }}"
                                            class="text-xs px-3 py-1 bg-gray-100 text-gray-700 rounded hover:bg-gray-200 font-medium">
                                                Edit
                                            </a>

                                            @if($type->maintenance_schedules_count === 0)
                                                <form method="POST"
                                                action="{{ route('admin.maintenance-types.destroy', $type) }}"
                                                onsubmit="return confirm('Delete this maintenance type?')">
                                                    @csrf
                                                    @method('DELETE')

                                                    <button type="submit"
                                                            class="text-xs px-3 py-1 bg-red-100 text-red-700 rounded hover:bg-red-200 font-medium">
                                                        Delete
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-xs px-3 py-1 bg-gray-50 text-gray-300 rounded cursor-not-allowed"
                                                title="Cannot delete — in use">
                                                    Delete
                                                </span>
                                            @endif

                                        </div>
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-6 text-center text-gray-400">
                                        No maintenance types yet. Add one to get started.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>

                    </table>
                </div>

                {{-- Footer --}}
                <div class="px-4 py-3 border-t border-gray-100 text-sm text-gray-500 flex items-center justify-between">
                    <span>
                        Showing {{ $types->firstItem() }}–{{ $types->lastItem() }} of {{ $types->total() }} records
                    </span>

                    {{ $types->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>