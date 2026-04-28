<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-green-50 border border-green-300 text-green-800 px-4 py-3 rounded">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="bg-red-50 border border-red-300 text-red-800 px-4 py-3 rounded">{{ session('error') }}</div>
            @endif

            @if(!$vehicle)
                {{-- No vehicle assigned yet --}}
                <div class="bg-white rounded-lg border border-gray-200 p-8 text-center">
                    <p class="text-gray-400 text-sm">You have no assigned vehicle yet. Please contact your administrator.</p>
                </div>
            @else

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                    {{-- LEFT COLUMN --}}
                    <div class="space-y-6">

                        {{-- Assigned Vehicle Card --}}
                        <div class="bg-gray-900 text-white rounded-xl p-5 space-y-4">
                            <div>
                                <p class="text-xs text-gray-400 uppercase tracking-widest">Assigned Vehicle</p>
                                <div class="flex items-center justify-between mt-1">
                                    <h3 class="text-xl font-bold">{{ $vehicle->plate_number }} | {{ $vehicle->model }}</h3>
                                    @php
                                        $vc = ['active' => 'bg-green-500', 'in_shop' => 'bg-blue-500', 'archived' => 'bg-gray-500'][$vehicle->status] ?? 'bg-gray-500';
                                        $vl = ['active' => 'Active', 'in_shop' => 'In Shop', 'archived' => 'Archived'][$vehicle->status] ?? $vehicle->status;
                                    @endphp
                                    <span class="text-xs px-2 py-1 rounded-full font-semibold {{ $vc }}">{{ $vl }}</span>
                                </div>
                            </div>

                            {{-- Odometer + Next PMS --}}
                            <div class="flex items-center gap-4">
                                <div class="flex-1">
                                    <p class="text-xs text-gray-400 uppercase tracking-widest mb-1">Current Odometer (km)</p>
                                    <form method="POST" action="{{ route('driver.odometer.update') }}" class="flex gap-2">
                                        @csrf @method('PATCH')
                                        <input type="number" name="odometer" value="{{ $vehicle->current_odometer_km }}"
                                               min="{{ $vehicle->current_odometer_km }}"
                                               class="w-32 bg-gray-800 border border-gray-600 text-white text-sm rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                        <button type="submit"
                                                class="px-3 py-2 bg-blue-600 text-white text-sm font-semibold rounded hover:bg-blue-700">
                                            Update
                                        </button>
                                    </form>
                                </div>

                                @php
                                    $nextPms = $vehicle->maintenanceSchedules
                                        ->sortBy('next_due_odo')
                                        ->first();
                                @endphp
                                @if($nextPms)
                                    <div class="text-right">
                                        <p class="text-xs text-gray-400 uppercase tracking-widest">Next PMS</p>
                                        <p class="text-lg font-bold {{ $nextPms->status === 'overdue' ? 'text-red-400' : 'text-yellow-300' }} mt-1">
                                            {{ number_format($nextPms->next_due_odo) }} km
                                        </p>
                                        <p class="text-xs text-gray-400">
                                            {{ $nextPms->status === 'overdue'
                                                ? number_format(abs($nextPms->remaining_km)) . ' km overdue'
                                                : number_format($nextPms->remaining_km) . ' km remaining' }}
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Scheduled Jobs (approved, assigned to mechanic) --}}
                        @php
                            $pmsJobs = $vehicle->maintenanceSchedules
                                ->filter(fn($s) => $s->jobs->isNotEmpty())
                                ->flatMap(fn($s) => $s->jobs);

                            $reportJobs = $vehicle->jobs ?? collect();

                            $scheduledJobs = $pmsJobs->merge($reportJobs);
                        @endphp
                        @if($scheduledJobs->isNotEmpty())
                            <div class="bg-white rounded-lg border border-gray-200">
                                <div class="px-5 py-4 border-b border-gray-100">
                                    <h3 class="font-semibold text-gray-800">My Scheduled Jobs</h3>
                                </div>
                                <ul class="divide-y divide-gray-100">
                                    @foreach($scheduledJobs as $job)
                                        <li class="px-5 py-3">
                                            <div class="flex items-start justify-between">
                                                <div>
                                                    <p class="text-sm font-semibold text-gray-800">
                                                        @if($job->job_type === 'report')
                                                            {{ ucfirst($job->report->issue_type ?? 'Issue') }} — Driver Report
                                                        @else
                                                            {{ $job->maintenanceSchedule->maintenanceType->name ?? 'PMS Job' }}
                                                        @endif
                                                    </p>
                                                    <p class="text-xs text-gray-500 mt-0.5">
                                                        Assigned to {{ $job->mechanic->full_name }}
                                                        · Scheduled {{ $job->scheduled_at->format('M d, Y') }}
                                                    </p>
                                                    <p class="text-xs text-blue-600 mt-0.5 italic">Bring vehicle to shop on scheduled date.</p>
                                                </div>
                                                @php $jc = ['pending'=>'bg-yellow-100 text-yellow-700','in_shop'=>'bg-blue-100 text-blue-700','in_progress'=>'bg-orange-100 text-orange-700','done'=>'bg-green-100 text-green-700'][$job->status] ?? ''; @endphp
                                                <span class="text-xs px-2 py-1 rounded font-semibold {{ $jc }}">
                                                    {{ ucwords(str_replace('_', ' ', $job->status)) }}
                                                </span>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        {{-- Upcoming PMS Table --}}
                        <div class="bg-white rounded-lg border border-gray-200">
                            <div class="px-5 py-4 border-b border-gray-100">
                                <h3 class="font-semibold text-gray-800">Upcoming PMS</h3>
                            </div>
                            @if($vehicle->maintenanceSchedules->isEmpty())
                                <p class="px-5 py-4 text-sm text-gray-400">No maintenance schedules set up yet.</p>
                            @else
                                <table class="min-w-full text-sm">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Type</th>
                                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Remaining</th>
                                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        @foreach($vehicle->maintenanceSchedules->sortBy('next_due_odo') as $sched)
                                            @php
                                                $st = $sched->status;
                                                $sc = ['overdue'=>'bg-red-100 text-red-700','due_soon'=>'bg-yellow-100 text-yellow-700','ok'=>'bg-green-100 text-green-700'][$st] ?? '';
                                                $sl = ['overdue'=>'Overdue','due_soon'=>'Due Soon','ok'=>'OK'][$st] ?? $st;
                                            @endphp
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-5 py-3 text-gray-800 font-medium">{{ $sched->maintenanceType->name }}</td>
                                                <td class="px-5 py-3 text-gray-600">
                                                    @if($st === 'overdue')
                                                        <span class="text-red-600 font-semibold">{{ number_format(abs($sched->remaining_km)) }} km overdue</span>
                                                    @else
                                                        {{ number_format($sched->remaining_km) }} km
                                                    @endif
                                                </td>
                                                <td class="px-5 py-3">
                                                    <span class="px-2 py-0.5 rounded text-xs font-semibold {{ $sc }}">{{ $sl }}</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endif
                        </div>

                    </div>

                    {{-- RIGHT COLUMN --}}
                    <div class="space-y-6">

                        {{-- Quick Report Form --}}
                        <div class="bg-white rounded-lg border border-gray-200 p-5">
                            <h3 class="font-semibold text-gray-800 mb-4">Quick Report Issue</h3>

                            <form method="POST" action="{{ route('driver.reports.store') }}" class="space-y-4">
                                @csrf

                                <div>
                                    <x-input-label for="vehicle_display" value="Vehicle" />
                                    <x-text-input id="vehicle_display" type="text" class="mt-1 block w-full"
                                                  value="{{ $vehicle->plate_number }} — {{ $vehicle->model }}" disabled />
                                    <input type="hidden" name="vehicle_id" value="{{ $vehicle->id }}">
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
                                    <textarea id="description" name="description" rows="3"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="Describe the issue briefly...">{{ old('description') }}</textarea>
                                </div>

                                <div>
                                    <x-input-label for="current_odo" value="Current Odometer Reading (km)" />
                                    <x-text-input id="current_odo" type="text" class="mt-1 block w-full"
                                                  value="{{ number_format($vehicle->current_odometer_km) }} km" disabled />
                                </div>

                                <x-primary-button class="w-full justify-center">
                                    Submit Report
                                </x-primary-button>
                            </form>
                        </div>

                        {{-- Recent Reports --}}
                        <div class="bg-white rounded-lg border border-gray-200">
                            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                                <h3 class="font-semibold text-gray-800">Recent Reports</h3>
                                <a href="{{ route('driver.reports.index') }}" class="text-sm text-blue-600 hover:underline">View all →</a>
                            </div>
                            <ul class="divide-y divide-gray-100">
                                @forelse($recentReports as $report)
                                    <li class="px-5 py-3">
                                        <div class="flex items-start justify-between gap-3">
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-gray-800">{{ ucfirst($report->issue_type) }}</p>
                                                <p class="text-xs text-gray-400 mt-0.5">{{ $report->created_at->diffForHumans() }}</p>
                                                @if($report->description)
                                                    <p class="text-xs text-gray-500 mt-1 italic truncate">"{{ $report->description }}"</p>
                                                @endif
                                                @if($report->admin_notes)
                                                    <div class="mt-2 bg-gray-50 border border-gray-200 rounded p-2">
                                                        <p class="text-xs text-gray-400">Admin Note:</p>
                                                        <p class="text-xs text-gray-600 mt-0.5">"{{ $report->admin_notes }}"</p>
                                                    </div>
                                                @endif
                                                @if($report->isApproved() && $report->job)
                                                    <p class="text-xs text-green-600 mt-1">
                                                        Assigned to {{ $report->job->mechanic->full_name }}
                                                        · Scheduled {{ $report->job->scheduled_at->format('M d, Y') }}
                                                    </p>
                                                @endif
                                            </div>
                                            @php
                                                $rc = ['pending'=>'bg-yellow-100 text-yellow-700','approved'=>'bg-green-100 text-green-700','rejected'=>'bg-red-100 text-red-700'][$report->status] ?? '';
                                            @endphp
                                            <span class="text-xs px-2 py-1 rounded font-semibold {{ $rc }} whitespace-nowrap">
                                                {{ ucfirst($report->status) }}
                                            </span>
                                        </div>
                                    </li>
                                @empty
                                    <li class="px-5 py-4 text-sm text-gray-400">No reports submitted yet.</li>
                                @endforelse
                            </ul>
                        </div>

                    </div>
                </div>

            @endif
        </div>
    </div>
</x-app-layout>