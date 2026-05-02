<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Assign Jobs</h2>
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
                    <option value="pending"     {{ request('status') === 'pending'     ? 'selected' : '' }}>Pending</option>
                    <option value="in_shop"     {{ request('status') === 'in_shop'     ? 'selected' : '' }}>In Shop</option>
                    <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="done"        {{ request('status') === 'done'        ? 'selected' : '' }}>Done</option>
                </select>
                <select name="mechanic_id" class="border border-gray-300 pl-3 pr-8 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <option value="">All Mechanics</option>
                    @foreach($mechanics as $m)
                        <option value="{{ $m->id }}" {{ request('mechanic_id') == $m->id ? 'selected' : '' }}>{{ $m->full_name }}</option>
                    @endforeach
                </select>
                <button type="submit" class="px-4 py-2 bg-gray-800 text-white text-sm rounded-md hover:bg-gray-700">Filter</button>
                <a href="{{ route('admin.jobs.index') }}" class="px-4 py-2 text-sm text-gray-600 hover:underline self-center">Reset</a>
            </form>

            {{-- Quick links to create jobs --}}
            <div class="flex gap-3">
                <a href="{{ route('admin.reports.index') }}?status=approved"
                    class="text-sm px-4 py-2 bg-green-100 text-green-700 rounded-md hover:bg-green-200 font-medium">
                    + Assign from Approved Report
                </a>
                <a href="{{ route('admin.schedules.index', ['needs_attention' => 1]) }}"
                class="text-sm px-4 py-2 bg-red-100 text-red-700 rounded-md hover:bg-red-200 font-medium">
                    + Assign from Overdue & Due Soon
                </a>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Vehicle</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Service</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Mechanic</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Scheduled</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Priority</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($jobs as $job)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 font-medium text-gray-800">
                                    {{ $job->vehicle->plate_number }}
                                    <div class="text-xs text-gray-400">{{ $job->vehicle->model }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-0.5 rounded text-xs font-semibold {{ $job->job_type === 'report' ? 'bg-orange-100 text-orange-700' : 'bg-blue-100 text-blue-700' }}">
                                        {{ $job->job_type === 'report' ? 'Driver Report' : 'PMS' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-gray-600">
                                    @if($job->report)
                                        {{ ucfirst($job->report->issue_type) }}
                                    @elseif($job->maintenanceSchedule)
                                        {{ $job->maintenanceSchedule->maintenanceType->name }}
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-gray-700">{{ $job->mechanic->full_name }}</td>
                                <td class="px-4 py-3 text-gray-500 whitespace-nowrap">{{ $job->scheduled_at->format('M d, Y') }}</td>
                                <td class="px-4 py-3">
                                    @php $pc = ['low'=>'bg-gray-100 text-gray-600','normal'=>'bg-blue-100 text-blue-700','high'=>'bg-red-100 text-red-700'][$job->priority] ?? ''; @endphp
                                    <span class="px-2 py-0.5 rounded text-xs font-semibold {{ $pc }}">{{ ucfirst($job->priority) }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    @php $sc = ['pending'=>'bg-yellow-100 text-yellow-700','in_shop'=>'bg-blue-100 text-blue-700','in_progress'=>'bg-orange-100 text-orange-700','done'=>'bg-green-100 text-green-700'][$job->status] ?? ''; @endphp
                                    <span class="px-2 py-0.5 rounded text-xs font-semibold {{ $sc }}">{{ ucwords(str_replace('_',' ',$job->status)) }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <a href="{{ route('admin.jobs.show', $job) }}"
                                    class="text-xs px-3 py-1 bg-gray-100 text-gray-700 rounded hover:bg-gray-200 font-medium">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="px-4 py-6 text-center text-gray-400">No jobs found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="px-4 py-3 border-t border-gray-100 text-sm text-gray-500 flex items-center justify-between">
                    <span>Showing {{ $jobs->firstItem() }}–{{ $jobs->lastItem() }} of {{ $jobs->total() }} records</span>
                    {{ $jobs->withQueryString()->links() }}
                </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>