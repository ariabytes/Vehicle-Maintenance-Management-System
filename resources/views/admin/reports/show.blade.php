<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Report — {{ $report->vehicle->plate_number }} · {{ ucfirst($report->issue_type) }}
            </h2>
            <a href="{{ route('admin.reports.index') }}" class="text-sm text-gray-500 hover:underline">← Back to Reports</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            @if(session('success'))
                <div class="bg-green-50 border border-green-300 text-green-800 px-4 py-3 rounded">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="bg-red-50 border border-red-300 text-red-800 px-4 py-3 rounded">{{ session('error') }}</div>
            @endif

            {{-- Report Detail Card --}}
            <div class="bg-white rounded-lg border border-gray-200 p-6 space-y-4">

                <div class="flex items-center justify-between">
                    <h3 class="font-semibold text-gray-800 text-lg">Report Details</h3>
                    @php $sc = ['pending'=>'bg-yellow-100 text-yellow-700','approved'=>'bg-green-100 text-green-700','rejected'=>'bg-red-100 text-red-700'][$report->status] ?? ''; @endphp
                    <span class="px-3 py-1 rounded text-sm font-semibold {{ $sc }}">{{ ucfirst($report->status) }}</span>
                </div>

                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide">Vehicle</p>
                        <p class="font-medium text-gray-800 mt-0.5">{{ $report->vehicle->plate_number }} — {{ $report->vehicle->model }}</p>
                        <p class="text-xs text-gray-400">Current odometer: {{ number_format($report->vehicle->current_odometer_km) }} km</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide">Reported By</p>
                        <p class="font-medium text-gray-800 mt-0.5">{{ $report->reporter->full_name }}</p>
                        <p class="text-xs text-gray-400">{{ $report->created_at->format('M d, Y · g:i A') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide">Issue Type</p>
                        <p class="font-medium text-gray-800 mt-0.5">{{ ucfirst($report->issue_type) }}</p>
                    </div>
                    @if($report->reviewer)
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide">Reviewed By</p>
                            <p class="font-medium text-gray-800 mt-0.5">{{ $report->reviewer->full_name }}</p>
                            <p class="text-xs text-gray-400">{{ $report->reviewed_at?->format('M d, Y · g:i A') }}</p>
                        </div>
                    @endif
                </div>

                @if($report->description)
                    <div class="bg-gray-50 border border-gray-200 rounded p-3">
                        <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Driver's Description</p>
                        <p class="text-sm text-gray-700 italic">"{{ $report->description }}"</p>
                    </div>
                @endif

                @if($report->admin_notes)
                    <div class="bg-blue-50 border border-blue-200 rounded p-3">
                        <p class="text-xs text-blue-400 uppercase tracking-wide mb-1">Admin Note</p>
                        <p class="text-sm text-blue-800">{{ $report->admin_notes }}</p>
                    </div>
                @endif

                {{-- Linked Job --}}
                @if($report->job)
                    <div class="bg-green-50 border border-green-200 rounded p-3">
                        <p class="text-xs text-green-600 uppercase tracking-wide mb-1">Assigned Job</p>
                        <p class="text-sm text-green-800">
                            Assigned to <strong>{{ $report->job->mechanic->full_name }}</strong>
                            · Scheduled {{ $report->job->scheduled_at->format('M d, Y') }}
                            · Priority: {{ ucfirst($report->job->priority) }}
                        </p>
                    </div>
                @endif
            </div>

            {{-- Actions — only show if still pending --}}
            @if($report->isPending())
                <div class="bg-white rounded-lg border border-gray-200 p-6 space-y-5">
                    <h3 class="font-semibold text-gray-800">Review This Report</h3>

                    {{-- Approve + Assign --}}
                    <div class="border border-green-200 bg-green-50 rounded-lg p-4 space-y-4">
                        <p class="text-sm font-semibold text-green-800">Approve &amp; Assign to Mechanic</p>
                        <form method="POST" action="{{ route('admin.reports.approve', $report) }}">
                            @csrf @method('PATCH')

                            <div class="space-y-3">
                                <div>
                                    <x-input-label for="admin_notes_approve" value="Admin Note (optional)" />
                                    <textarea id="admin_notes_approve" name="admin_notes" rows="2"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-green-500 focus:border-green-500"
                                        placeholder="Optional note for the driver..."></textarea>
                                </div>
                            </div>

                            <div class="mt-3">
                                <button type="submit"
                                    class="px-5 py-2 bg-green-600 text-white text-sm font-semibold rounded-md hover:bg-green-700">
                                    Approve Report
                                </button>
                                <p class="text-xs text-gray-400 mt-1">After approving, go to <a href="{{ route('admin.jobs.index') }}" class="underline">Jobs</a> to assign a mechanic.</p>
                            </div>
                        </form>
                    </div>

                    {{-- Reject --}}
                    <div class="border border-red-200 bg-red-50 rounded-lg p-4 space-y-3">
                        <p class="text-sm font-semibold text-red-800">Reject Report</p>
                        <form method="POST" action="{{ route('admin.reports.reject', $report) }}">
                            @csrf @method('PATCH')

                            <div>
                                <x-input-label for="admin_notes_reject" value="Reason for Rejection *" />
                                <textarea id="admin_notes_reject" name="admin_notes" rows="2" required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-red-500 focus:border-red-500"
                                    placeholder="Explain why this report is being rejected..."></textarea>
                                <x-input-error :messages="$errors->get('admin_notes')" class="mt-1" />
                            </div>

                            <div class="mt-3">
                                <button type="submit"
                                    onclick="return confirm('Reject this report?')"
                                    class="px-5 py-2 bg-red-600 text-white text-sm font-semibold rounded-md hover:bg-red-700">
                                    Reject Report
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            {{-- If approved and no job yet, show assign job prompt --}}
            @elseif($report->isApproved() && !$report->job)
                <div class="bg-white rounded-lg border border-green-200 p-5">
                    <p class="text-sm text-gray-700 mb-3">This report is approved. Assign it to a mechanic to create a job.</p>
                    <a href="{{ route('admin.jobs.from-report', $report) }}"
                       class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-md hover:bg-blue-700">
                        Assign Job →
                    </a>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>