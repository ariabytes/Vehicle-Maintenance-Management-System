<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Maintenance History</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            {{-- Filters --}}
            <form method="GET" class="flex gap-3 flex-wrap">
                @if(auth()->user()->isAdmin())
                    <input type="text" name="plate_number" value="{{ request('plate_number') }}"
                        placeholder="Search plate number..."
                        class="border border-gray-300 rounded-md px-3 py-2 text-sm w-48 focus:outline-none focus:ring-1 focus:ring-blue-500">
                @endif
                <input type="date" name="from" value="{{ request('from') }}"
                       class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
                <input type="date" name="to" value="{{ request('to') }}"
                       class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
                <button type="submit" class="px-4 py-2 bg-gray-800 text-white text-sm rounded-md hover:bg-gray-700">Filter</button>
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.maintenance-history') }}" class="px-4 py-2 text-sm text-gray-600 hover:underline self-center">Reset</a>
                @elseif(auth()->user()->isDriver())
                    <a href="{{ route('driver.maintenance-history') }}" class="px-4 py-2 text-sm text-gray-600 hover:underline self-center">Reset</a>
                @elseif(auth()->user()->isMechanic())
                    <a href="{{ route('mechanic.maintenance-history') }}" class="px-4 py-2 text-sm text-gray-600 hover:underline self-center">Reset</a>
                @endif
            </form>

            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Vehicle</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Service</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Mechanic</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Completed</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Notes</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($jobs as $job)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 font-medium text-gray-800">
                                    {{ $job->vehicle->plate_number }}
                                    <div class="text-xs text-gray-400">{{ $job->vehicle->model }}</div>
                                </td>
                                <td class="px-4 py-3 text-gray-700">
                                    @if($job->job_type === 'report')
                                        {{ ucfirst($job->report->issue_type ?? '—') }}
                                    @else
                                        {{ $job->maintenanceSchedule->maintenanceType->name ?? '—' }}
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-0.5 rounded text-xs font-semibold {{ $job->job_type === 'report' ? 'bg-orange-100 text-orange-700' : 'bg-blue-100 text-blue-700' }}">
                                        {{ $job->job_type === 'report' ? 'Driver Report' : 'PMS' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-gray-600">{{ $job->mechanic->full_name }}</td>
                                <td class="px-4 py-3 text-gray-500 whitespace-nowrap">
                                    {{ $job->completed_at?->format('M d, Y') ?? '—' }}
                                </td>
                                <td class="px-4 py-3 text-gray-500 italic max-w-xs truncate">
                                    {{ $job->mechanic_notes ? '"' . $job->mechanic_notes . '"' : '—' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-6 text-center text-gray-400">No maintenance history found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                @if($jobs->hasPages())
                    <div class="px-4 py-3 border-t border-gray-100 text-sm text-gray-500 flex items-center justify-between">
                        <span>Showing {{ $jobs->firstItem() }}–{{ $jobs->lastItem() }} of {{ $jobs->total() }} records</span>
                        {{ $jobs->withQueryString()->links() }}
                    </div>
                @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>