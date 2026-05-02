<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Review Driver Reports</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            @if(session('success'))
                <div class="bg-green-50 border border-green-300 text-green-800 px-4 py-3 rounded">{{ session('success') }}</div>
            @endif

            {{-- Filters --}}
            <form method="GET" class="flex gap-3 flex-wrap">
                <select name="status" class="border border-gray-300 pl-3 pr-8 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <option value="">All Status</option>
                    <option value="pending"  {{ request('status') === 'pending'  ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
                <button type="submit" class="px-4 py-2 bg-gray-800 text-white text-sm rounded-md hover:bg-gray-700">Filter</button>
                <a href="{{ route('admin.reports.index') }}" class="px-4 py-2 text-sm text-gray-600 hover:underline self-center">Reset</a>
            </form>

            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Vehicle</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Driver</th>
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
                                <td class="px-4 py-3 text-gray-700">{{ $report->reporter->first_name }} {{ $report->reporter->last_name }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ ucfirst($report->issue_type) }}</td>
                                <td class="px-4 py-3 text-gray-500 max-w-xs truncate italic">
                                    "{{ $report->description ?? '—' }}"
                                </td>
                                <td class="px-4 py-3 text-gray-500 whitespace-nowrap">{{ $report->created_at->format('M d, Y') }}</td>
                                <td class="px-4 py-3">
                                    @php
                                        $sc = ['pending'=>'bg-yellow-100 text-yellow-700','approved'=>'bg-green-100 text-green-700','rejected'=>'bg-red-100 text-red-700'][$report->status] ?? 'bg-gray-100 text-gray-600';
                                    @endphp
                                    <span class="px-2 py-0.5 rounded text-xs font-semibold {{ $sc }}">{{ ucfirst($report->status) }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <a href="{{ route('admin.reports.show', $report) }}"
                                       class="text-xs px-3 py-1 bg-blue-100 text-blue-700 rounded hover:bg-blue-200 font-medium whitespace-nowrap">
                                        {{ $report->isPending() ? 'Review' : 'View' }}
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="px-4 py-6 text-center text-gray-400">No reports found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="px-4 py-3 border-t border-gray-100 text-sm text-gray-500 flex items-center justify-between">
                    <span>Showing {{ $reports->firstItem() }}–{{ $reports->lastItem() }} of {{ $reports->total() }} records</span>
                    {{ $reports->withQueryString()->links() }}
                </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>