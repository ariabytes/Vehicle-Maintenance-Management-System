<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Report Details</h2>
            <a href="{{ route('driver.reports.index') }}" class="text-sm text-gray-500 hover:underline">← Back to My Reports</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            @if(session('success'))
                <div class="bg-green-50 border border-green-300 text-green-800 px-4 py-3 rounded">{{ session('success') }}</div>
            @endif

            <div class="bg-white rounded-lg border border-gray-200 p-6 space-y-4">

                {{-- Header --}}
                <div class="flex items-center justify-between">
                    <h3 class="font-semibold text-gray-800 text-lg">
                        {{ $report->vehicle->plate_number }} — {{ ucfirst($report->issue_type) }}
                    </h3>
                    @php $rc = ['pending'=>'bg-yellow-100 text-yellow-700','approved'=>'bg-green-100 text-green-700','rejected'=>'bg-red-100 text-red-700'][$report->status] ?? ''; @endphp
                    <span class="px-3 py-1 rounded text-sm font-semibold {{ $rc }}">{{ ucfirst($report->status) }}</span>
                </div>

                {{-- Details --}}
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide">Vehicle</p>
                        <p class="font-medium text-gray-800 mt-0.5">{{ $report->vehicle->plate_number }} — {{ $report->vehicle->model }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide">Date Reported</p>
                        <p class="font-medium text-gray-800 mt-0.5">{{ $report->created_at->format('M d, Y · g:i A') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide">Issue Type</p>
                        <p class="font-medium text-gray-800 mt-0.5">{{ ucfirst($report->issue_type) }}</p>
                    </div>
                    @if($report->reviewed_at)
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide">Reviewed</p>
                            <p class="font-medium text-gray-800 mt-0.5">{{ $report->reviewed_at->format('M d, Y · g:i A') }}</p>
                        </div>
                    @endif
                </div>

                {{-- Description --}}
                @if($report->description)
                    <div class="bg-gray-50 border border-gray-200 rounded p-3">
                        <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Your Description</p>
                        <p class="text-sm text-gray-700 italic">"{{ $report->description }}"</p>
                    </div>
                @endif

                {{-- Admin note --}}
                @if($report->admin_notes)
                    <div class="bg-blue-50 border border-blue-200 rounded p-3">
                        <p class="text-xs text-blue-400 uppercase tracking-wide mb-1">Admin Note</p>
                        <p class="text-sm text-blue-800">"{{ $report->admin_notes }}"</p>
                    </div>
                @endif

                {{-- Job info if approved and assigned --}}
                @if($report->isApproved() && $report->job)
                    <div class="bg-green-50 border border-green-200 rounded p-3 space-y-1">
                        <p class="text-xs text-green-500 uppercase tracking-wide font-semibold">Job Assigned</p>
                        <p class="text-sm text-green-800">
                            Assigned to <strong>{{ $report->job->mechanic->full_name }}</strong>
                        </p>
                        <p class="text-sm text-green-700">
                            Scheduled: {{ $report->job->scheduled_at->format('M d, Y') }}
                            · Priority: {{ ucfirst($report->job->priority) }}
                        </p>
                        @php $jc = ['pending'=>'bg-yellow-100 text-yellow-700','in_shop'=>'bg-blue-100 text-blue-700','in_progress'=>'bg-orange-100 text-orange-700','done'=>'bg-green-100 text-green-700'][$report->job->status] ?? ''; @endphp
                        <span class="text-xs px-2 py-0.5 rounded font-semibold {{ $jc }}">
                            Job Status: {{ ucwords(str_replace('_', ' ', $report->job->status)) }}
                        </span>
                        @if($report->job->mechanic_notes)
                            <div class="mt-2 bg-white border border-green-200 rounded p-2">
                                <p class="text-xs text-gray-400">Mechanic Notes:</p>
                                <p class="text-xs text-gray-700 mt-0.5">{{ $report->job->mechanic_notes }}</p>
                            </div>
                        @endif
                    </div>
                @elseif($report->isApproved() && !$report->job)
                    <div class="bg-yellow-50 border border-yellow-200 rounded p-3">
                        <p class="text-sm text-yellow-800">Your report has been approved. A mechanic will be assigned shortly.</p>
                    </div>
                @endif

                {{-- Actions for pending reports --}}
                @if($report->isPending())
                    <div class="flex gap-3 pt-2 border-t border-gray-100">
                        <a href="{{ route('driver.reports.edit', $report) }}"
                           class="px-4 py-2 text-sm font-semibold bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200">
                            Edit Report
                        </a>
                        <form method="POST" action="{{ route('driver.reports.destroy', $report) }}"
                              onsubmit="return confirm('Cancel this report?')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                class="px-4 py-2 text-sm font-semibold bg-red-100 text-red-700 rounded-md hover:bg-red-200">
                                Cancel Report
                            </button>
                        </form>
                    </div>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>