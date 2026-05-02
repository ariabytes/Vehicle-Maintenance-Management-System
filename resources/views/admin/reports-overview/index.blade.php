<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Generate Reports</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            {{-- Stat Cards --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white rounded-lg border border-gray-200 p-4 border-b-gray-500 border-b-4 text-center">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Total Jobs</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">{{ $jobs->total() }}</p>
                </div>
                <div class="bg-white rounded-lg border border-gray-200 p-4 border-b-orange-500 border-b-4 text-center">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">From Reports</p>
                    <p class="text-3xl font-bold text-orange-500 mt-1">{{ $jobs->getCollection()->where('job_type', 'report')->count() }}</p>
                </div>
                <div class="bg-white rounded-lg border border-gray-200 p-4 border-b-blue-500 border-b-4 text-center">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">From PMS</p>
                    <p class="text-3xl font-bold text-blue-500 mt-1">{{ $jobs->getCollection()->where('job_type', 'maintenance_schedule')->count() }}</p>
                </div>
                <div class="bg-white rounded-lg border border-gray-200 p-4 border-b-green-500 border-b-4  text-center">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Done</p>
                    <p class="text-3xl font-bold text-green-600 mt-1">{{ $jobs->getCollection()->where('status', 'done')->count() }}</p>
                </div>
            </div>

            {{-- Filters --}}
            <form method="GET" class="flex gap-3 flex-wrap">
                <select name="job_type" class="border border-gray-300 rounded-md px-3 pl-3 pr-8 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <option value="">All Sources</option>
                    <option value="report"               {{ request('job_type') === 'report'               ? 'selected' : '' }}>Driver Report</option>
                    <option value="maintenance_schedule" {{ request('job_type') === 'maintenance_schedule' ? 'selected' : '' }}>PMS Schedule</option>
                </select>
                <select name="status" class="border border-gray-300 pl-3 pr-8 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <option value="">All Status</option>
                    <option value="pending"     {{ request('status') === 'pending'     ? 'selected' : '' }}>Pending</option>
                    <option value="in_shop"     {{ request('status') === 'in_shop'     ? 'selected' : '' }}>In Shop</option>
                    <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="done"        {{ request('status') === 'done'        ? 'selected' : '' }}>Done</option>
                </select>
                <select name="priority" class="border border-gray-300 pl-3 pr-8 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <option value="">All Priority</option>
                    <option value="high"   {{ request('priority') === 'high'   ? 'selected' : '' }}>High</option>
                    <option value="normal" {{ request('priority') === 'normal' ? 'selected' : '' }}>Normal</option>
                    <option value="low"    {{ request('priority') === 'low'    ? 'selected' : '' }}>Low</option>
                </select>
                <div class="flex items-center ml-6 gap-2">
                    <span class="text-sm text-gray-500">From</span>
                    <input type="date" name="from" value="{{ request('from') }}"
                        class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <span class="text-sm text-gray-500">To</span>
                    <input type="date" name="to" value="{{ request('to') }}"
                        class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
                </div>       
                <button type="submit" class="px-4 py-2 bg-gray-800 text-white text-sm rounded-md hover:bg-gray-700">Filter</button>
                <a href="{{ route('admin.reports-overview.index') }}" class="px-4 py-2 text-sm text-gray-600 hover:underline self-center">Reset</a>
            </form>

            {{-- Table --}}
            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-800">Maintenance & Job History</h3>
                    <span class="text-xs text-gray-400">{{ $jobs->total() }} total records</span>
                </div>
                <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Job ID</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Vehicle</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Service</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Source</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Mechanic</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Scheduled</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Priority</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($jobs as $job)
                            <tr class="hover:bg-gray-50 cursor-pointer" onclick="window.location='{{ route('admin.jobs.show', $job) }}'">
                                <td class="px-4 py-3 text-gray-500 font-mono text-xs">{{ $job->id }}</td>
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
                                        {{ $job->job_type === 'report' ? 'Driver Report' : 'PMS Schedule' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-gray-600">{{ $job->mechanic->full_name }}</td>
                                <td class="px-4 py-3 text-gray-500 whitespace-nowrap">{{ $job->scheduled_at->format('M d, Y') }}</td>
                                <td class="px-4 py-3">
                                    @php $pc = ['low'=>'bg-gray-100 text-gray-600','normal'=>'bg-blue-100 text-blue-700','high'=>'bg-red-100 text-red-700'][$job->priority] ?? ''; @endphp
                                    <span class="px-2 py-0.5 rounded text-xs font-semibold {{ $pc }}">{{ ucfirst($job->priority) }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    @php $sc = ['pending'=>'bg-yellow-100 text-yellow-700','in_shop'=>'bg-blue-100 text-blue-700','in_progress'=>'bg-orange-100 text-orange-700','done'=>'bg-green-100 text-green-700'][$job->status] ?? ''; @endphp
                                    <span class="px-2 py-0.5 rounded text-xs font-semibold {{ $sc }}">{{ ucwords(str_replace('_', ' ', $job->status)) }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-6 text-center text-gray-400">No records found.</td>
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