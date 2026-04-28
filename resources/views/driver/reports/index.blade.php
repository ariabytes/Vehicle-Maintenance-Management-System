<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">My Reports</h2>
            <a href="{{ route('driver.reports.create') }}"
               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-md hover:bg-blue-700">
                + Report Issue
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            @if(session('success'))
                <div class="bg-green-50 border border-green-300 text-green-800 px-4 py-3 rounded">{{ session('success') }}</div>
            @endif

            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Vehicle</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Issue Type</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Description</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($reports as $report)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 font-medium text-gray-800">
                                    {{ $report->vehicle->plate_number }}
                                    <div class="text-xs text-gray-400">{{ $report->vehicle->model }}</div>
                                </td>
                                <td class="px-4 py-3 text-gray-700">{{ ucfirst($report->issue_type) }}</td>
                                <td class="px-4 py-3 text-gray-500 max-w-xs truncate italic">
                                    {{ $report->description ? '"' . $report->description . '"' : '—' }}
                                </td>
                                <td class="px-4 py-3 text-gray-500 whitespace-nowrap">
                                    {{ $report->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-4 py-3">
                                    @php $rc = ['pending'=>'bg-yellow-100 text-yellow-700','approved'=>'bg-green-100 text-green-700','rejected'=>'bg-red-100 text-red-700'][$report->status] ?? ''; @endphp
                                    <span class="px-2 py-0.5 rounded text-xs font-semibold {{ $rc }}">
                                        {{ ucfirst($report->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('driver.reports.show', $report) }}"
                                           class="text-xs px-3 py-1 bg-gray-100 text-gray-700 rounded hover:bg-gray-200 font-medium">
                                            View
                                        </a>
                                        @if($report->isPending())
                                            <a href="{{ route('driver.reports.edit', $report) }}"
                                               class="text-xs px-3 py-1 bg-blue-100 text-blue-700 rounded hover:bg-blue-200 font-medium">
                                                Edit
                                            </a>
                                            <form method="POST" action="{{ route('driver.reports.destroy', $report) }}"
                                                  onsubmit="return confirm('Cancel this report?')">
                                                @csrf @method('DELETE')
                                                <button type="submit"
                                                    class="text-xs px-3 py-1 bg-red-100 text-red-700 rounded hover:bg-red-200 font-medium">
                                                    Cancel
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-6 text-center text-gray-400">
                                    No reports submitted yet.
                                    <a href="{{ route('driver.reports.create') }}" class="text-blue-600 hover:underline ml-1">Report an issue →</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                @if($reports->hasPages())
                    <div class="px-4 py-3 border-t border-gray-100 text-sm text-gray-500 flex items-center justify-between">
                        <span>Showing {{ $reports->firstItem() }}–{{ $reports->lastItem() }} of {{ $reports->total() }} records</span>
                        {{ $reports->withQueryString()->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>